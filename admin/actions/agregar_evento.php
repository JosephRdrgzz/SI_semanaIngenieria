<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1) Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

// 2) Leer datos del formulario
$nombre         = trim($_POST['nombre']        ?? '');
$tipo_evento    = $_POST['tipo_evento']        ?? '';
$tipo_otro      = trim($_POST['tipo_evento_otro'] ?? '');
$capacidad      = $_POST['capacidad']          ?? '';
$fecha          = $_POST['fecha']              ?? '';
$hora_inicio    = $_POST['hora_inicio']        ?? '';
$hora_fin       = $_POST['hora_fin']           ?? '';
$lugar          = $_POST['lugar']              ?? '';
$lugar_otro     = trim($_POST['lugar_otro']    ?? '');
$campus         = $_POST['campus']             ?? '';
$comentario     = $_POST['comentario']         ?? '';
$direccion      = $_POST['direccion']          ?? '';
$lineamientos   = $_POST['lineamientos']       ?? '';
$expositor      = trim($_POST['expositor']     ?? '');

// 3) Manejar “otro” tipo de evento (y ALTER TYPE si aplica)
if ($tipo_evento === 'otro') {
    if ($tipo_otro === '') {
        echo json_encode(["error" => "Debe especificar el nuevo tipo de evento"]);
        exit();
    }
    try {
        $val = $pdo->quote($tipo_otro);
        $pdo->exec("ALTER TYPE tipo_evento_enum ADD VALUE $val");
    } catch (Exception $e) {
        // ignorar si ya existe
        if (strpos($e->getMessage(), 'duplicate key value') === false) {
            echo json_encode(["error" => "Error al agregar tipo de evento: ".$e->getMessage()]);
            exit();
        }
    }
    $tipo_evento = $tipo_otro;
} else {
    // Validar que $tipo_evento esté en el ENUM
    $stmt = $pdo->query("SELECT unnest(enum_range(NULL::tipo_evento_enum)) AS tipo");
    $validos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array($tipo_evento, $validos)) {
        echo json_encode(["error" => "Tipo de evento no válido"]);
        exit();
    }
}

// 4) Manejar “otro” lugar
if ($lugar === 'otro') {
    if ($lugar_otro !== '') {
        $lugar = $lugar_otro;
    } else {
        echo json_encode(["error" => "Debe especificar el nuevo lugar"]);
        exit();
    }
}

// 5) Validaciones de campos obligatorios
if (
    $nombre === '' ||
    $tipo_evento === '' ||
    $capacidad === '' ||
    $fecha === '' ||
    $hora_inicio === '' ||
    $hora_fin === '' ||
    $lugar === '' ||
    $campus === '' ||
    $direccion === '' ||
    $lineamientos === ''
) {
    echo json_encode(["error" => "Todos los campos obligatorios deben llenarse"]);
    exit();
}

// 6) Validar orden de horas
if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
    echo json_encode(["error" => "La hora de inicio debe ser menor que la de fin"]);
    exit();
}

// 7) Verificar traslapes en mismo salón y fecha
try {
    $sqlOver = "
      SELECT nombre, fecha, hora_inicio, hora_fin
      FROM evento
      WHERE lugar = :lugar
        AND campus = :campus
        AND fecha = :fecha
        AND (hora_inicio::time, hora_fin::time)
            OVERLAPS (:hora_inicio::time, :hora_fin::time)
    ";
    $stmtOv = $pdo->prepare($sqlOver);
    $stmtOv->execute([
        'lugar'       => $lugar,
        'campus'      => $campus,
        'fecha'       => $fecha,
        'hora_inicio' => $hora_inicio,
        'hora_fin'    => $hora_fin
    ]);
    $ov = $stmtOv->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($ov)) {
        $e = $ov[0];
        echo json_encode([
            "error" => "Se traslapa con '{$e['nombre']}' el {$e['fecha']} de {$e['hora_inicio']} a {$e['hora_fin']}"
        ]);
        exit();
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Error al verificar traslapes: ".$e->getMessage()]);
    exit();
}

// 8) Validar capacidad del salón (si no es Externo)
if ($campus !== 'Externo') {
    try {
        $stmtCap = $pdo->prepare("SELECT capacidad FROM salones WHERE id_salon = :lugar");
        $stmtCap->execute(['lugar' => $lugar]);
        $s = $stmtCap->fetch(PDO::FETCH_ASSOC);
        if ($s && $capacidad > $s['capacidad']) {
            echo json_encode([
                "error" => "La capacidad del evento ($capacidad) excede el límite del salón ({$s['capacidad']})."
            ]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Error al validar capacidad: ".$e->getMessage()]);
        exit();
    }
}

// 9) Subida de imagen
$rutaImagen = null;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../assets/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext      = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $safeName = uniqid('evt_') . '.' . $ext;
    $destino  = $uploadDir . $safeName;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
        $rutaImagen = 'assets/' . $safeName;
    } else {
        echo json_encode(["error" => "Error al mover la imagen"]);
        exit();
    }
}

try {
    // 10) Insertar evento
    $pdo->beginTransaction();
    $sqlIns = "
      INSERT INTO evento (
        nombre, tipo_evento,
        capacidad, fecha, hora_inicio, hora_fin,
        lugar, campus,
        comentario, direccion, lineamientos,
        expositor, imagen_path
      ) VALUES (
        :nombre, :tipo_evento,
        :capacidad, :fecha, :hora_inicio, :hora_fin,
        :lugar, :campus,
        :comentario, :direccion, :lineamientos,
        :expositor, :imagen_path
      )
    ";
    $stmtIns = $pdo->prepare($sqlIns);
    $stmtIns->execute([
        'nombre'        => $nombre,
        'tipo_evento'   => $tipo_evento,
        'capacidad'     => $capacidad,
        'fecha'         => $fecha,
        'hora_inicio'   => $hora_inicio,
        'hora_fin'      => $hora_fin,
        'lugar'         => $lugar,
        'campus'        => $campus,
        'comentario'    => $comentario,
        'direccion'     => $direccion,
        'lineamientos'  => $lineamientos,
        'expositor'     => $expositor,
        'imagen_path'   => $rutaImagen
    ]);
    $pdo->commit();
    echo json_encode(["success" => "Evento agregado correctamente"]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["error" => "Error al agregar evento: ".$e->getMessage()]);
}

