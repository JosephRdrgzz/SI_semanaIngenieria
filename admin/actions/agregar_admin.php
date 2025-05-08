<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . '/../config/conexion.php';

if ($_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(['error'=>'No autorizado']);
    exit();
}

$exp    = trim($_POST['exp']      ?? '');
$nombre = trim($_POST['nombre']   ?? '');
$correo = trim($_POST['correo']   ?? '');
$pass   = $_POST['contrasena']    ?? '';

// Campos obligatorios al crear
if (!$exp || !$nombre || !$correo || !$pass) {
    echo json_encode(['error'=>'Completa todos los campos']);
    exit();
}

// Validar dominio
if (!preg_match('/@anahuac\.mx$/', $correo)) {
    echo json_encode(['error'=>'El correo debe terminar en @anahuac.mx']);
    exit();
}

// Hash SHA-256
$pass_hash = hash('sha256', $pass);

try {
    $stmt = $pdo->prepare("
      INSERT INTO administradores(exp,nombre,correo,contraseña)
      VALUES(:exp,:nom,:mail,:pass)
    ");
    $stmt->execute([
        ':exp'=>$exp,
        ':nom'=>$nombre,
        ':mail'=>$correo,
        ':pass'=>$pass_hash
    ]);
    echo json_encode(['success'=>'Administrador creado']);
} catch (PDOException $e) {
    if ($e->getCode()==='23505') {
        echo json_encode(['error'=>'Ese expediente o correo ya existe']);
    } else {
        echo json_encode(['error'=>$e->getMessage()]);
    }
}
