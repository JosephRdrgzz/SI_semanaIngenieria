<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario']['exp'])) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit();
}

// Forzamos el valor a cadena y lo registramos
$exp = (string) $_SESSION['usuario']['exp'];
error_log("Depuración eventos.php - Expediente: " . $exp);

try {
    // Inyectamos directamente $exp en la consulta para depuración
    $query = "SELECT id, nombre, capacidad, fecha, hora_inicio, hora_fin, lugar, campus, tipo_evento,
                     COALESCE(
                         (SELECT count(*) FROM jsonb_object_keys(COALESCE(asistencia, '{}')::jsonb)), 0
                     ) AS inscritos
              FROM evento
              WHERE NOT jsonb_exists(COALESCE(asistencia, '{}')::jsonb, '" . $exp . "')
                AND COALESCE(
                         (SELECT count(*) FROM jsonb_object_keys(COALESCE(asistencia, '{}')::jsonb)), 0
                     ) < capacidad";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Depuración eventos.php - Eventos obtenidos: " . print_r($eventos, true));

    // Devolvemos la consulta de depuración
    echo json_encode(["debug" => ["exp" => $exp, "query" => $query], "result" => $eventos]);
} catch (Exception $e) {
    error_log("Depuración eventos.php - Error: " . $e->getMessage());
    echo json_encode(["error" => "Error al obtener eventos: " . $e->getMessage()]);
}
?>
