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

// Recibir los datos del formulario
$id_evento = $_POST['id_evento'] ?? null;
$nombre = $_POST['nombre'] ?? null;
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

// Validar que los datos requeridos no estén vacíos
if (!$id_evento || !$nombre || !$capacidad || !$fecha || !$hora_inicio || !$hora_fin || !$lugar || !$campus || !$direccion || !$lineamientos || !$expositor) {
    echo json_encode(["error" => "Todos los campos obligatorios deben llenarse"]);
    exit();
}

// Si el usuario seleccionó "Otro", usar el input de texto como lugar
if ($lugar === "otro" && !empty($lugar_otro)) {
    $lugar = $lugar_otro;
}

// Validar que la hora de inicio sea menor que la hora de fin
if ($hora_inicio >= $hora_fin) {
    echo json_encode(["error" => "La hora de inicio debe ser menor que la hora de fin"]);
    exit();
}

// Verificar si el evento existe antes de actualizarlo
$query_existencia = "SELECT id FROM evento WHERE id = :id_evento";
$stmt_existencia = $pdo->prepare($query_existencia);
$stmt_existencia->execute(['id_evento' => $id_evento]);
$evento_existente = $stmt_existencia->fetch(PDO::FETCH_ASSOC);

if (!$evento_existente) {
    echo json_encode(["error" => "El evento que intenta editar no existe"]);
    exit();
}

// Verificar traslapes excluyendo el evento actual
$traslapes = verificarTraslapesParaEditar($id_evento, $lugar, $fecha, $hora_inicio, $hora_fin);
if (!empty($traslapes)) {
    $eventos_traslapados = array_map(fn($t) => "{$t['nombre']} ({$t['hora_inicio']} - {$t['hora_fin']})", $traslapes);
    echo json_encode(["error" => "El evento se traslapa con otro en el mismo lugar: " . implode(", ", $eventos_traslapados)]);
    exit();
}

// Verificar capacidad máxima del salón
$capacidad_salon = obtenerCapacidadSalon($lugar);
if ($capacidad_salon && $capacidad > $capacidad_salon['capacidad']) {
    echo json_encode(["error" => "La capacidad ingresada supera el cupo máximo del salón"]);
    exit();
}

// **Actualizar evento en la base de datos**
try {
    $pdo->beginTransaction();

    $query = "UPDATE evento 
              SET nombre = :nombre, capacidad = :capacidad, fecha = :fecha, 
                  hora_inicio = :hora_inicio, hora_fin = :hora_fin, lugar = :lugar, 
                  campus = :campus, comentario = :comentario, direccion = :direccion, 
                  lineamientos = :lineamientos, expositor = :expositor
              WHERE id = :id_evento";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id_evento' => $id_evento,
        'nombre' => $nombre,
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
    echo json_encode(["success" => "Evento actualizado correctamente"]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["error" => "Error al actualizar evento: " . $e->getMessage()]);
}
?>
