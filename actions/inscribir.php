<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/eventos.php';

header("Content-Type: application/json");

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    echo json_encode(["error" => "Debes iniciar sesión para inscribirte."]);
    exit();
}

$exp = $_SESSION['usuario']['exp'];
$nombre = $_SESSION['usuario']['nombre'];

// Obtener eventos seleccionados
$data = json_decode(file_get_contents("php://input"), true);
$eventosSeleccionados = $data['eventos'] ?? [];

if (empty($eventosSeleccionados)) {
    echo json_encode(["error" => "No seleccionaste ningún evento."]);
    exit();
}

// **Verificar si hay TRASLAPES antes de inscribirse**
$traslapes = verificarTraslapes($exp, $eventosSeleccionados);

if (!empty($traslapes)) {
    echo json_encode(["error" => "Conflicto de horarios: " . implode(", ", $traslapes)]);
    exit();
}

// **Inscribir al usuario en los eventos**
$resultado = inscribirUsuario($exp, $nombre, $eventosSeleccionados);
echo json_encode($resultado);
?>
