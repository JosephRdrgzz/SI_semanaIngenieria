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
    // Primero, obtenemos la asistencia actual
    $query = "SELECT asistencia FROM evento WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $evento_id]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        echo json_encode(["error" => "Evento no encontrado"]);
        exit();
    }

    $asistencia_actual = json_decode($evento['asistencia'], true);

    // Filtrar la asistencia para remover al usuario
    $nueva_asistencia = array_filter($asistencia_actual, function ($asistente) use ($exp) {
        return $asistente['exp'] !== $exp;
    });

    // Convertir de nuevo a JSONB y actualizar la base de datos
    $query = "UPDATE evento SET asistencia = :nueva_asistencia, capacidad = capacidad + 1 WHERE id = :id AND fecha > NOW()";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'nueva_asistencia' => json_encode(array_values($nueva_asistencia)), // Se usa `array_values` para reindexar el array
        'id' => $evento_id
    ]);

    echo json_encode(["success" => "Inscripción cancelada"]);
} catch (Exception $e) {
    echo json_encode(["error" => "Error al cancelar: " . $e->getMessage()]);
}
?>
