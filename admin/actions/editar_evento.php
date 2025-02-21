<?php
try {
    // Habilitar la depuración de errores
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    header("Content-Type: application/json");

    session_start();
    require_once __DIR__ . '/../config/conexion.php';
    require_once __DIR__ . '/../models/admin.php';

    // Verificar que el usuario sea administrador
    if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        throw new Exception("Acceso no autorizado");
    }

    // Recibir los datos del formulario
    $id_evento = $_POST['id_evento'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $capacidad = $_POST['capacidad'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $hora_inicio = $_POST['hora_inicio'] ?? null;
    $hora_fin = $_POST['hora_fin'] ?? null;
    $lugar = $_POST['lugar'] ?? null;
    $lugar_otro = $_POST['lugar_otro'] ?? null;
    $campus = $_POST['campus'] ?? null;
    $comentario = $_POST['comentario'] ?? null;
    $direccion = $_POST['direccion'] ?? null;
    $lineamientos = $_POST['lineamientos'] ?? null;
    $expositor = $_POST['expositor'] ?? null;

    // Validar que los datos requeridos no estén vacíos
    if (!$id_evento || !$nombre || !$capacidad || !$fecha || !$hora_inicio || !$hora_fin || !$lugar || !$campus || !$direccion || !$lineamientos || !$expositor) {
        throw new Exception("Todos los campos obligatorios deben llenarse");
    }

    // Si el usuario seleccionó "Otro", usar el input de texto como lugar
    if ($lugar === "otro" && !empty($lugar_otro)) {
        $lugar = $lugar_otro;
    }

    // Validar que la hora de inicio sea menor que la hora de fin
    if ($hora_inicio >= $hora_fin) {
        throw new Exception("La hora de inicio debe ser menor que la hora de fin");
    }

    // Verificar si el evento existe antes de actualizarlo
    $query_existencia = "SELECT id FROM evento WHERE id = :id_evento";
    $stmt_existencia = $pdo->prepare($query_existencia);
    $stmt_existencia->execute(['id_evento' => $id_evento]);
    $evento_existente = $stmt_existencia->fetch(PDO::FETCH_ASSOC);

    if (!$evento_existente) {
        throw new Exception("El evento que intenta editar no existe");
    }

    // Verificar traslapes excluyendo el evento actual
    $traslapes = verificarTraslapesParaEditar($id_evento, $lugar, $fecha, $hora_inicio, $hora_fin);
    if (!empty($traslapes)) {
        $eventos_traslapados = array_map(fn($t) => "{$t['nombre']} ({$t['hora_inicio']} - {$t['hora_fin']})", $traslapes);
        throw new Exception("El evento se traslapa con otro en el mismo lugar: " . implode(", ", $eventos_traslapados));
    }

    // Verificar capacidad máxima del salón
    $capacidad_salon = obtenerCapacidadSalon($lugar);
    if ($capacidad_salon && $capacidad > $capacidad_salon['capacidad']) {
        throw new Exception("La capacidad ingresada supera el cupo máximo del salón");
    }

    // 🔹 Construcción de la consulta SQL con los valores
    $query_update = "UPDATE evento 
                     SET nombre = :nombre, capacidad = :capacidad, fecha = :fecha, 
                         hora_inicio = :hora_inicio, hora_fin = :hora_fin, lugar = :lugar, 
                         campus = :campus, comentario = :comentario, direccion = :direccion, 
                         lineamientos = :lineamientos, expositor = :expositor
                     WHERE id = :id_evento";

    // Iniciar transacción para seguridad
    $pdo->beginTransaction();

    // Preparar y ejecutar la consulta de actualización
    $stmt = $pdo->prepare($query_update);
    $stmt->execute([
        'id_evento' => $id_evento,
        'nombre' => $nombre,
        'capacidad' => $capacidad,
        'fecha' => $fecha,
        'hora_inicio' => $hora_inicio,
        'hora_fin' => $hora_fin,
        'lugar' => $lugar,
        'campus' => $campus,
        'comentario' => $comentario,
        'direccion' => $direccion,
        'lineamientos' => $lineamientos,
        'expositor' => $expositor
    ]);

    // Confirmar la transacción
    $pdo->commit();

    // Enviar la consulta en la respuesta JSON para depuración
    echo json_encode([
        "success" => "Evento actualizado correctamente",
        "query_executed" => $query_update
    ]);
} catch (Exception $e) {
    // En caso de error, revertir la transacción si se inició
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
exit();
?>
