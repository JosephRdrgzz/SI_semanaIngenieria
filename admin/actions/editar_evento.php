<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/admin.php';

header("Content-Type: application/json");

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

// Recibir POST
$id_evento = $_POST['id_evento'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$tipo_evento = $_POST['tipo_evento'] ?? null;
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

// Ajustar 'expositor' para permitir nulo
$expositor = trim($_POST['expositor'] ?? '');
if ($expositor === '') {
    $expositor = null;
}

// Validar el campo "tipo_evento"
if ($tipo_evento === "otro") {
    $tipo_evento_otro = trim($_POST['tipo_evento_otro'] ?? '');
    if (empty($tipo_evento_otro)) {
        echo json_encode(["error" => "Debe especificar el nuevo tipo de evento"]);
        exit();
    }
    // Intentar agregar el nuevo valor al ENUM
    try {
        $nuevoValor = $pdo->quote($tipo_evento_otro);
        $sql_alter = "ALTER TYPE tipo_evento_enum ADD VALUE $nuevoValor";
        $pdo->exec($sql_alter);
    } catch (Exception $e) {
        // Si el error indica que el valor ya existe, lo ignoramos.
        if (strpos($e->getMessage(), 'duplicate key value') === false) {
            echo json_encode(["error" => "Error al agregar el nuevo tipo de evento: " . $e->getMessage()]);
            exit();
        }
    }
    $tipo_evento = $tipo_evento_otro;
} else {
    // Validar que el valor ingresado se encuentre en el ENUM actual
    $query_tipos = $pdo->query("SELECT unnest(enum_range(NULL::tipo_evento_enum)) AS tipo");
    $tipos_existentes = $query_tipos->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array($tipo_evento, $tipos_existentes)) {
        echo json_encode(["error" => "Tipo de evento no válido"]);
        exit();
    }
}

// Validar datos (sin 'expositor' como requerido)
if (!$id_evento || !$nombre || !$capacidad || !$fecha || !$hora_inicio || !$hora_fin || !$lugar || !$campus || !$direccion || !$lineamientos) {
    echo json_encode(["error" => "Todos los campos obligatorios deben llenarse"]);
    exit();
}

// Si el usuario seleccionó "Otro" en lugar
if ($lugar === "otro" && !empty($lugar_otro)) {
    $lugar = $lugar_otro;
}

// Validar horas
if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
    echo json_encode(["error" => "La hora de inicio debe ser menor que la hora de fin"]);
    exit();
}

// Verificar existencia del evento
$query_existencia = "SELECT id FROM evento WHERE id = :id_evento";
$stmt_existencia = $pdo->prepare($query_existencia);
$stmt_existencia->execute(['id_evento' => $id_evento]);
$evento_existente = $stmt_existencia->fetch(PDO::FETCH_ASSOC);
if (!$evento_existente) {
    echo json_encode(["error" => "El evento que intenta editar no existe"]);
    exit();
}

// Verificar traslapes
$traslapes = verificarTraslapesParaEditar($id_evento, $lugar, $fecha, $hora_inicio, $hora_fin);
if (!empty($traslapes)) {
    $evento_traslapado = $traslapes[0];
    echo json_encode([
        "error" => "El evento se traslapa con otro: '{$evento_traslapado['nombre']}' el {$evento_traslapado['fecha']} de {$evento_traslapado['hora_inicio']} a {$evento_traslapado['hora_fin']}"
    ]);
    exit();
}

// Verificar capacidad del salón
$capacidad_salon = obtenerCapacidadSalon($lugar);
if ($capacidad_salon && $capacidad > $capacidad_salon['capacidad']) {
    echo json_encode(["error" => "La capacidad del evento ($capacidad) excede el límite del salón ({$capacidad_salon['capacidad']})."]);
    exit();
}

// Actualizar
try {
    $pdo->beginTransaction();

    $query_update = "UPDATE evento 
                     SET nombre = :nombre, tipo_evento = :tipo_evento, capacidad = :capacidad, fecha = :fecha, 
                         hora_inicio = :hora_inicio, hora_fin = :hora_fin, lugar = :lugar, 
                         campus = :campus, comentario = :comentario, direccion = :direccion, 
                         lineamientos = :lineamientos, expositor = :expositor
                     WHERE id = :id_evento";

    $stmt = $pdo->prepare($query_update);
    $stmt->execute([
        'id_evento'   => $id_evento,
        'nombre'      => $nombre,
        'tipo_evento' => $tipo_evento,
        'capacidad'   => $capacidad,
        'fecha'       => $fecha,
        'hora_inicio' => $hora_inicio,
        'hora_fin'    => $hora_fin,
        'lugar'       => $lugar,
        'campus'      => $campus,
        'comentario'  => $comentario,
        'direccion'   => $direccion,
        'lineamientos'=> $lineamientos,
        'expositor'   => $expositor  // Puede ser NULL
    ]);

    $pdo->commit();

    echo json_encode([
        "success" => "Evento actualizado correctamente"
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["error" => "Error al editar evento: " . $e->getMessage()]);
}
exit();

