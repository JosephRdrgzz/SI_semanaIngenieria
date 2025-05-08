<?php
session_start();
require __DIR__.'/../config/conexion.php';
if ($_SESSION['tipo_usuario']!=='admin') exit(json_encode(['error'=>'No autorizado']));
$exp = $_GET['exp'] ?? '';
$stmt = $pdo->prepare("SELECT exp,nombre,correo FROM administradores WHERE exp=:exp");
$stmt->execute([':exp'=>$exp]);
echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: ['error'=>'No encontrado']);
