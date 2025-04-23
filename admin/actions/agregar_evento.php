<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

header("Content-Type: application/json");

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

// Obtener los datos del formulario
$nombre = $_POST['nombre'] ?? null;
$tipo_evento = $_POST['tipo_evento'] ?? null;

// Validar el campo "tipo_evento"
// Si se seleccionó "otro" para el tipo de evento
if ($tipo_evento === "otro") {
    $tipo_evento_otro = trim($_POST['tipo_evento_otro'] ?? '');
    if (empty($tipo_evento_otro)) {
        echo json_encode(["error" => "Debe especificar el nuevo tipo de evento"]);
        exit();
    }
    // Intentar agregar el nuevo valor al ENUM
    try {
        $nuevoValor = $pdo->quote($tipo_evento_otro); // Escapa la cadena, incluyendo comillas simples
        $sql_alter = "ALTER TYPE tipo_evento_enum ADD VALUE $nuevoValor";
        $pdo->exec($sql_alter);
    } catch (Exception $e) {
        // Si el error indica que el valor ya existe, lo ignoramos.
        if (strpos($e->getMessage(), 'duplicate key value') === false) {
            echo json_encode(["error" => "Error al agregar el nuevo tipo de evento: " . $e->getMessage()]);
            exit();
        }
    }
    // Usar el nuevo tipo para el insert/update.
    $tipo_evento = $tipo_evento_otro;
} else {
    // En caso contrario, validar que el valor ingresado se encuentre en el ENUM actual
    $query_tipos = $pdo->query("SELECT unnest(enum_range(NULL::tipo_evento_enum)) AS tipo");
    $tipos_existentes = $query_tipos->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array($tipo_evento, $tipos_existentes)) {
        echo json_encode(["error" => "Tipo de evento no válido"]);
        exit();
    }
}


$capacidad = $_POST['capacidad'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$hora_inicio = $_POST['hora_inicio'] ?? null;
$hora_fin = $_POST['hora_fin'] ?? null;
$lugar = $_POST['lugar'] ?? null;
$lugar_otro = $_POST['lugar_otro'] ?? null;
$campus = $_POST['campus'] ?? null;
$comentario = $_POST['comentario'] ?? null;
$direccion = $_POST['direccion'] ?? null;
$lineamientos = $_POST['lineamientos'] ?? null;
$expositor = trim($_POST['expositor'] ?? '');
if ($expositor === '') {
    $expositor = null;
}


// Si el usuario seleccionó "Otro", usamos el input de texto
if ($lugar === "otro" && !empty($lugar_otro)) {
    $lugar = $lugar_otro;
}

// Validar datos requeridos
if (!$nombre || !$capacidad || !$fecha || !$hora_inicio || !$hora_fin || !$lugar || !$campus || !$direccion || !$lineamientos) {
    echo json_encode(["error" => "Todos los campos obligatorios deben llenarse"]);
    exit();
}

// Validar que la hora de inicio sea menor que la de fin
if (strtotime($hora_fin) <= strtotime($hora_inicio)) {
    echo json_encode(["error" => "La hora de inicio debe ser menor que la hora de fin"]);
    exit();
}

// Verificar traslapes en el mismo salón (solo si no es Externo)
try {
    $query = "SELECT nombre, fecha, hora_inicio, hora_fin FROM evento 
              WHERE lugar = :lugar 
              AND campus = :campus 
              AND fecha = :fecha 
              AND (hora_inicio::time, hora_fin::time) OVERLAPS (:hora_inicio::time, :hora_fin::time)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'lugar' => $lugar,
        'campus' => $campus,
        'fecha' => $fecha,
        'hora_inicio' => $hora_inicio,
        'hora_fin' => $hora_fin
    ]);
    $traslapes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($traslapes)) {
        $evento_traslapado = $traslapes[0];
        echo json_encode([
            "error" => "El evento se traslapa con otro: '{$evento_traslapado['nombre']}' el {$evento_traslapado['fecha']} de {$evento_traslapado['hora_inicio']} a {$evento_traslapado['hora_fin']}"
        ]);
        exit();
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Error al verificar traslapes: " . $e->getMessage()]);
    exit();
}

// Validar capacidad del salón solo si NO es Externo
if ($campus !== 'Externo') {
    try {
        $queryCapacidad = "SELECT capacidad FROM salones WHERE id_salon = :lugar";
        $stmtCapacidad = $pdo->prepare($queryCapacidad);
        $stmtCapacidad->execute(['lugar' => $lugar]);
        $salon = $stmtCapacidad->fetch(PDO::FETCH_ASSOC);

        if ($salon && $capacidad > $salon['capacidad']) {
            echo json_encode(["error" => "La capacidad del evento ($capacidad) excede el límite del salón ($salon[capacidad])."]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Error al validar capacidad del salón: " . $e->getMessage()]);
        exit();
    }
}

// Insertar evento en la base de datos
try {
    $pdo->beginTransaction();

    $query = "INSERT INTO evento (nombre, tipo_evento, capacidad, fecha, hora_inicio, hora_fin, lugar, campus, comentario, direccion, lineamientos, expositor)
              VALUES (:nombre, :tipo_evento, :capacidad, :fecha, :hora_inicio, :hora_fin, :lugar, :campus, :comentario, :direccion, :lineamientos, :expositor)";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'nombre' => $nombre,
        'tipo_evento' => $tipo_evento,
        'capacidad' => $capacidad,
        'fecha' => $fecha,
        'hora_inicio' => $hora_inicio,
        'hora_fin' => $hora_fin,
        'lugar' => $lugar,
        'campus' => $campus,
        'comentario' => $comentario,
        'direccion' => $direccion,
        'lineamientos' => $lineamientos,
        'expositor' => $expositor
    ]);

    $pdo->commit();
    echo json_encode(["success" => "Evento agregado correctamente"]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["error" => "Error al agregar evento: " . $e->getMessage()]);
}

