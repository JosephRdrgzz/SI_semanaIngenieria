<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['usuario']['exp'])) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit();
}

$exp = $_SESSION['usuario']['exp'];
$evento_id = json_decode(file_get_contents("php://input"), true)['evento_id'] ?? null;

try {
    // Obtener la asistencia actual
    $query = "SELECT asistencia FROM evento WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $evento_id]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        echo json_encode(["error" => "Evento no encontrado"]);
        exit();
    }

    // Decodificar asistencia; se espera un objeto asociativo
    $asistencia_actual = json_decode($evento['asistencia'], true);
    if (!is_array($asistencia_actual)) {
        $asistencia_actual = [];
    }

    // Remover la clave del alumno (cancelar la inscripción)
    if (isset($asistencia_actual[$exp])) {
        unset($asistencia_actual[$exp]);
    }

    // Si no queda ningún inscrito, forzamos que sea un objeto vacío (no un arreglo vacío)
    if (empty($asistencia_actual)) {
        $asistencia_actual = new stdClass();
    }

    // Actualizar la asistencia en la BD sin modificar la capacidad
    $query = "UPDATE evento SET asistencia = :nueva_asistencia WHERE id = :id AND fecha > NOW()";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'nueva_asistencia' => json_encode($asistencia_actual),
        'id' => $evento_id
    ]);

    echo json_encode(["success" => "Inscripción cancelada"]);
} catch (Exception $e) {
    echo json_encode(["error" => "Error al cancelar: " . $e->getMessage()]);
}
?>
