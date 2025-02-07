<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que el usuario es admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(["error" => "Acceso denegado"]);
    exit();
}

// Recibir datos
$datos = $_POST;

try {
    $query = "INSERT INTO evento (nombre, capacidad, fecha, hora_inicio, hora_fin, lugar, campus, comentario, direccion, lineamientos, expositor) 
              VALUES (:nombre, :capacidad, :fecha, :hora_inicio, :hora_fin, :lugar, :campus, :comentario, :direccion, :lineamientos, :expositor)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($datos);

    echo json_encode(["success" => "Evento agregado con éxito"]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
