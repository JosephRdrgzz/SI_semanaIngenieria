<?php

require_once __DIR__ . '/../config/conexion.php';

$campus = $_GET['campus'] ?? '';

if ($campus) {
    $query = "SELECT id_salon, capacidad FROM salones WHERE campus = :campus ORDER BY id_salon";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['campus' => $campus]);
    $salones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($salones);
} else {
    echo json_encode([]);
}
