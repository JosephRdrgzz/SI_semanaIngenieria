<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['usuario']['exp'])) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit();
}

$exp = $_SESSION['usuario']['exp'];

try {
    $query = "SELECT id, nombre, fecha, hora_inicio, hora_fin, lugar, campus,
                     (fecha > CURRENT_DATE) AS cancelable
              FROM evento
              WHERE jsonb_exists(COALESCE(asistencia, '{}')::jsonb, :exp)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['exp' => $exp]);
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($eventos);
} catch (Exception $e) {
    echo json_encode(["error" => "Error al obtener eventos: " . $e->getMessage()]);
}
?>
