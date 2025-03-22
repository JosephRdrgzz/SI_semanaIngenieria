<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/eventos.php';

header("Content-Type: application/json");

// Activar reportes de error para depuración (en desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    echo json_encode(["error" => "Debes iniciar sesión para inscribirte."]);
    exit();
}

$exp = $_SESSION['usuario']['exp'];
$nombre = $_SESSION['usuario']['nombre'];

// Obtener eventos seleccionados del body JSON
$data = json_decode(file_get_contents("php://input"), true);
$eventosSeleccionados = $data['eventos'] ?? [];

if (empty($eventosSeleccionados)) {
    echo json_encode(["error" => "No seleccionaste ningún evento."]);
    exit();
}

// Opcional: Verificar traslapes
$traslapes = verificarTraslapes($exp, $eventosSeleccionados);
if (!empty($traslapes)) {
    echo json_encode(["error" => "Conflicto de horarios: " . implode(", ", $traslapes)]);
    exit();
}

// Llamar a la función que inscribe al usuario
$resultado = inscribirUsuario($exp, $nombre, $eventosSeleccionados);
echo json_encode($resultado);
?>