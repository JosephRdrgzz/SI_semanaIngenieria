<?php
session_start();
require __DIR__.'/../config/conexion.php';
if ($_SESSION['tipo_usuario']!=='admin') exit(json_encode(['error'=>'No autorizado']));
$data = json_decode(file_get_contents('php://input'), true);
$exp  = $data['exp'] ?? '';
$stmt = $pdo->prepare("DELETE FROM administradores WHERE exp=:exp");
try {
    $stmt->execute([':exp'=>$exp]);
    echo json_encode(['success'=>'Administrador eliminado']);
} catch(Exception $e){
    echo json_encode(['error'=>$e->getMessage()]);
}
