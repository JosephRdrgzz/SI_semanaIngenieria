<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

header("Content-Type: application/json");

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

// Recibir ID del evento
$data = json_decode(file_get_contents("php://input"), true);
$evento_id = $data['id'] ?? null;

if (!$evento_id) {
    echo json_encode(["error" => "ID de evento requerido"]);
    exit();
}

try {
    $query = "DELETE FROM evento WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $evento_id]);

    echo json_encode(["success" => "Evento eliminado correctamente"]);
} catch (Exception $e) {
    echo json_encode(["error" => "Error al eliminar evento: " . $e->getMessage()]);
}
?>
