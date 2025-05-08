<?php
session_start();
require_once __DIR__.'/../config/conexion.php';
if ($_SESSION['tipo_usuario']!=='admin' && $_SESSION['tipo_usuario']!=='super') {
    exit(json_encode([]));
}
$stmt = $pdo->query("SELECT id_salon,nombre,campus,capacidad,descripcion FROM salones ORDER BY id_salon");
$salones = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['data'=>$salones]);
