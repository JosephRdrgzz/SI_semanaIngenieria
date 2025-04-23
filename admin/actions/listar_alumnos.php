<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

// Verifica que sea administrador o el rol que requieras
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Capturar la búsqueda (si viene vacía, mostramos todos)
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

try {
    if ($busqueda === '') {
        // Si no hay término de búsqueda, retorna todos
        $stmt = $pdo->query("SELECT * FROM alumnos ORDER BY exp ASC");
    } else {
        // Filtra por expediente o nombre
        $stmt = $pdo->prepare("
            SELECT *
            FROM alumnos
            WHERE LOWER(exp) LIKE LOWER(:busqueda)
               OR LOWER(nombre) LIKE LOWER(:busqueda)
            ORDER BY exp ASC
        ");
        $stmt->execute([':busqueda' => "%{$busqueda}%"]);
    }

    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['alumnos' => $alumnos]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
