<?php

session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar si el usuario es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(["error" => "Acceso denegado"]);
    exit();
}

// Obtener ID del evento
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["error" => "ID de evento no proporcionado"]);
    exit();
}

try {
    $query = "DELETE FROM evento WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);

    echo json_encode(["success" => "Evento eliminado con éxito"]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

