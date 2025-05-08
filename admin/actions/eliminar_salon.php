<?php
session_start();
require_once __DIR__.'/../config/conexion.php';
if ($_SESSION['tipo_usuario']!=='admin' && $_SESSION['tipo_usuario']!=='super') {
    exit(json_encode(['error'=>'No autorizado']));
}
$data = json_decode(file_get_contents('php://input'), true);
$id   = $data['id_salon'] ?? '';
$stmt = $pdo->prepare("DELETE FROM salones WHERE id_salon=:id");
try {
    $stmt->execute([':id'=>$id]);
    echo json_encode(['success'=>'Salón eliminado']);
} catch(Exception $e){
    echo json_encode(['error'=>$e->getMessage()]);
}
