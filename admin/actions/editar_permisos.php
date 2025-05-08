<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';

// Sólo super-admin
if (empty($_SESSION['is_super'])) {
    echo json_encode(['error'=>'No autorizado']);
    exit();
}

$exp     = $_POST['exp'] ?? '';
$vistas  = $_POST['vistas'] ?? [];

if (!$exp) {
    echo json_encode(['error'=>'Falta expediente']);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1) Borrar viejos
    $del = $pdo->prepare("DELETE FROM admin_vistas WHERE admin_exp = :exp");
    $del->execute([':exp' => $exp]);

    // 2) Insertar nuevos
    if (is_array($vistas) && count($vistas) > 0) {
        $ins = $pdo->prepare("
            INSERT INTO admin_vistas (admin_exp, vista_id)
            VALUES (:exp, :vid)
        ");
        foreach ($vistas as $vid) {
            $ins->execute([':exp' => $exp, ':vid' => $vid]);
        }
    }

    $pdo->commit();
    echo json_encode(['success'=>'Permisos actualizados']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error'=>$e->getMessage()]);
}
