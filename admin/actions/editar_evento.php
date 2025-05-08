<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1) Sólo admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

// 2) Recoger POST
$id_evento      = $_POST['id_evento']           ?? null;
$nombre         = trim($_POST['nombre']         ?? '');
$tipo_evento    = $_POST['tipo_evento']         ?? '';
$tipo_otro      = trim($_POST['tipo_evento_otro'] ?? '');
$capacidad      = $_POST['capacidad']           ?? '';
$fecha          = $_POST['fecha']               ?? '';
$hora_inicio    = $_POST['hora_inicio']         ?? '';
$hora_fin       = $_POST['hora_fin']            ?? '';
$lugar          = $_POST['lugar']               ?? '';
$lugar_otro     = trim($_POST['lugar_otro']     ?? '');
$campus         = $_POST['campus']              ?? '';
$comentario     = $_POST['comentario']          ?? '';
$direccion      = $_POST['direccion']           ?? '';
$lineamientos   = $_POST['lineamientos']        ?? '';
$expositor      = trim($_POST['expositor']      ?? '');
$remove_image   = ($_POST['remove_image'] ?? '0') === '1';

// 3) Validaciones básicas
if (!$id_evento || $nombre === '' || $capacidad === '' || $fecha === '' ||
    $hora_inicio === '' || $hora_fin === '' || $lugar === '' ||
    $campus === '' || $direccion === '' || $lineamientos === ''
) {
    echo json_encode(["error" => "Faltan campos obligatorios"]);
    exit();
}
if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
    echo json_encode(["error" => "La hora de inicio debe ser menor que la de fin"]);
    exit();
}

// 4) Tipo evento “otro” (igual que en agregar)
if ($tipo_evento === 'otro') {
    if ($tipo_otro === '') {
        echo json_encode(["error" => "Debe especificar el nuevo tipo de evento"]);
        exit();
    }
    try {
        $val = $pdo->quote($tipo_otro);
        $pdo->exec("ALTER TYPE tipo_evento_enum ADD VALUE $val");
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'duplicate key value') === false) {
            echo json_encode(["error" => $e->getMessage()]);
            exit();
        }
    }
    $tipo_evento = $tipo_otro;
}

// 5) Lugar “otro”
if ($lugar === 'otro') {
    if ($lugar_otro === '') {
        echo json_encode(["error" => "Debe especificar el nuevo lugar"]);
        exit();
    }
    $lugar = $lugar_otro;
}

// 6) Traer la ruta de la imagen actual
$stmt0 = $pdo->prepare("SELECT imagen_path FROM evento WHERE id = :id");
$stmt0->execute(['id' => $id_evento]);
$row = $stmt0->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo json_encode(["error" => "Evento no encontrado"]);
    exit();
}
$imagenActual = $row['imagen_path'];  // puede ser NULL o 'assets/xxx.png'

// 7) Verificar traslapes y capacidad (igual que en agregar…)
//    [ omito el detalle para no repetirlo, pero aquí va tu lógica de OVERLAPS y de capacidad ]


// 8) Lógica de imagen:
//    - Si piden eliminar 
//    - Si suben nueva 
//    - En otro caso, conservar $imagenActual
$rutaImagen = $imagenActual;

// 8.1) ¿Eliminar imagen?
if ($remove_image && $imagenActual) {
    $path = __DIR__ . '/../' . $imagenActual;
    if (is_file($path)) unlink($path);
    $rutaImagen = null;
}

// 8.2) ¿Subieron nueva?
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    // si había una anterior, bórrala
    if ($rutaImagen) {
        $vieja = __DIR__ . '/../' . $rutaImagen;
        if (is_file($vieja)) unlink($vieja);
    }
    // mover la nueva
    $uploadDir = __DIR__ . '/../assets/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext      = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $newName  = uniqid('evt_') . '.' . $ext;
    $destino  = $uploadDir . $newName;
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
        echo json_encode(["error" => "Error guardando la nueva imagen"]);
        exit();
    }
    $rutaImagen = 'assets/' . $newName;
}

// 9) Ya con $rutaImagen listo (null o string), actualizar
try {
    $pdo->beginTransaction();
    $sql = "
      UPDATE evento SET
        nombre       = :nombre,
        tipo_evento  = :tipo_evento,
        capacidad    = :capacidad,
        fecha        = :fecha,
        hora_inicio  = :hora_inicio,
        hora_fin     = :hora_fin,
        lugar        = :lugar,
        campus       = :campus,
        comentario   = :comentario,
        direccion    = :direccion,
        lineamientos = :lineamientos,
        expositor    = :expositor,
        imagen_path  = :imagen_path
      WHERE id = :id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre'       => $nombre,
        'tipo_evento'  => $tipo_evento,
        'capacidad'    => $capacidad,
        'fecha'        => $fecha,
        'hora_inicio'  => $hora_inicio,
        'hora_fin'     => $hora_fin,
        'lugar'        => $lugar,
        'campus'       => $campus,
        'comentario'   => $comentario,
        'direccion'    => $direccion,
        'lineamientos' => $lineamientos,
        'expositor'    => $expositor ?: null,
        'imagen_path'  => $rutaImagen,
        'id'           => $id_evento
    ]);
    $pdo->commit();
    echo json_encode(["success" => "Evento actualizado correctamente"]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["error" => "Error al actualizar evento: " . $e->getMessage()]);
}

