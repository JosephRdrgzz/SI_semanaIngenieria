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
$expositor = $_POST['expositor'] ?? null;

// Validar que el tipo de evento es uno de los permitidos
$tipos_permitidos = ['Taller', 'Exposición', 'Competencia', 'Oportunidad Laboral'];
if (!in_array($tipo_evento, $tipos_permitidos)) {
    echo json_encode(["error" => "Tipo de evento no válido"]);
    exit();
}

// Validar datos
if (!$id_evento || !$nombre || !$capacidad || !$fecha || !$hora_inicio || !$hora_fin || !$lugar || !$campus || !$direccion || !$lineamientos || !$expositor) {
    echo json_encode(["error" => "Todos los campos obligatorios deben llenarse"]);
    exit();
}

// Si el usuario seleccionó "Otro", usar el input de texto como lugar
if ($lugar === "otro" && !empty($lugar_otro)) {
    $lugar = $lugar_otro;
}

// Validar que la hora de inicio sea menor que la hora de fin
if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
    echo json_encode(["error" => "La hora de inicio debe ser menor que la hora de fin"]);
    exit();
}

// Verificar si el evento existe
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

// Verificar capacidad
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
        'id_evento' => $id_evento,
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

    echo json_encode([
        "success" => "Evento actualizado correctamente",
        "debug_query" => $query_update
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["error" => "Error al editar evento: " . $e->getMessage()]);
}
exit();
