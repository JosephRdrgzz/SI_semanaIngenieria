<?php
// Configuración de conexión para PostgreSQL
$host = "localhost"; // También puedes probar con 127.0.0.1
$dbname = "semana";
$username = "semana";
$password = "semana2024";
$port = "5432"; // Puerto por defecto de PostgreSQL

// Intentar conectar
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}


?>
