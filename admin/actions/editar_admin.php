<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';

// Solo admin puede editar
if (empty($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Datos recibidos
$exp    = $_POST['exp_oculto'] ?? '';
$nombre = trim($_POST['nombre']  ?? '');
$correo = trim($_POST['correo']  ?? '');
$pass   = trim($_POST['contrasena'] ?? '');

// Validar expediente
if (!$exp) {
    echo json_encode(['error' => 'Falta expediente']);
    exit();
}

// Montar query dinámico
$sql    = "UPDATE administradores
              SET nombre = :nom,
                  correo = :mail";
$params = [
    ':nom'  => $nombre,
    ':mail' => $correo
];

// Si proporcionaron nueva contraseña, la hasheamos con SHA-256
if ($pass !== '') {
    $sql .= ", contraseña = :pass";
    $params[':pass'] = hash('sha256', $pass);
}

// Aquí la cláusula WHERE va al final, sin comas
$sql .= " WHERE exp = :exp";
$params[':exp'] = $exp;

// Ejecutar
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => 'Administrador actualizado']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error BD: ' . $e->getMessage()]);
}
