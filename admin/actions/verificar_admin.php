<?php
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_GET['exp']) || empty($_GET['exp'])) {
    echo json_encode(["es_admin" => false]);
    exit();
}

$exp = $_GET['exp'];

$query = $pdo->prepare("SELECT exp FROM administradores WHERE exp = :exp");
$query->execute(['exp' => $exp]);
$admin = $query->fetch(PDO::FETCH_ASSOC);

echo json_encode(["es_admin" => !empty($admin)]);
?>
