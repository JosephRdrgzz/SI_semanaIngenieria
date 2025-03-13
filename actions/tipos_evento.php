<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

try {
    $query = "SELECT DISTINCT tipo_evento FROM evento";
    $stmt = $pdo->query($query);
    $tipos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($tipos);
} catch (Exception $e) {
    echo json_encode(["error" => "Error al obtener tipos de evento: " . $e->getMessage()]);
}
?>