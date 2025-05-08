<?php
session_start();
require_once __DIR__.'/../config/conexion.php';
if ($_SESSION['tipo_usuario']!=='admin' && $_SESSION['tipo_usuario']!=='super') {
    exit(json_encode(['error'=>'No autorizado']));
}
$id = $_GET['id_salon'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM salones WHERE id_salon=:id");
$stmt->execute(['id'=>$id]);
$salon = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$salon) exit(json_encode(['error'=>'No encontrado']));
echo json_encode($salon);
