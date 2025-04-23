<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

// Verifica que sea administrador o el rol que requieras
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

if (!isset($_GET['exp'])) {
    echo json_encode(['error' => 'No se proporcionó el expediente']);
    exit();
}

$exp = $_GET['exp'];

try {
    // Asegúrate de que la variable $pdo esté definida
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE exp = :exp LIMIT 1");
    $stmt->execute(['exp' => $exp]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        echo json_encode(['error' => 'Alumno no encontrado']);
        exit();
    }
    echo json_encode($alumno);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
