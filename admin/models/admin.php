<?php
require_once __DIR__ . '/../config/conexion.php';

/**
 * Verifica si un expediente pertenece a un administrador
 */
function verificarAdmin($exp) {
    global $pdo;
    $query = $pdo->prepare("SELECT COUNT(*) FROM administradores WHERE exp = :exp");
    $query->execute(['exp' => $exp]);
    return $query->fetchColumn() > 0;
}

/**
 * Verifica las credenciales de un administrador
 */
function verificarCredencialesAdmin($exp, $contraseña) {
    global $pdo;
    $query = $pdo->prepare("SELECT exp, nombre, contraseña FROM administradores WHERE exp = :exp");
    $query->execute(['exp' => $exp]);
    $admin = $query->fetch(PDO::FETCH_ASSOC);

    if ($admin && hash('sha256', $contraseña) === $admin['contraseña']) {
        return ["exp" => $admin['exp'], "nombre" => $admin['nombre']];
    }

    return false;
}


/**
 * Obtiene todos los eventos ordenados por fecha y hora de inicio
 */
function obtenerEventos() {
    global $pdo;
    $query = "SELECT * FROM evento ORDER BY fecha, hora_inicio";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtiene la lista de todos los salones
 */
function obtenerSalones() {
    global $pdo;
    $query = "SELECT id_salon, nombre FROM salones ORDER BY nombre";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Verifica si un nuevo evento tiene traslapes con otros en el mismo salón y horario
 */
function verificarTraslapesSalon($lugar, $fecha, $horaInicio, $horaFin) {
    global $pdo;
    $query = "SELECT id, nombre FROM evento 
              WHERE lugar = :lugar 
              AND fecha = :fecha 
              AND (hora_inicio, hora_fin) OVERLAPS (:hora_inicio, :hora_fin)";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'lugar' => $lugar,
        'fecha' => $fecha,
        'hora_inicio' => $horaInicio,
        'hora_fin' => $horaFin
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function verificarTraslapesParaEditar($id_evento, $lugar, $fecha, $hora_inicio, $hora_fin) {
    global $pdo;
    $query = "SELECT id, nombre, fecha, hora_inicio, hora_fin 
              FROM evento 
              WHERE lugar = :lugar 
              AND fecha = :fecha 
              AND id != :id_evento
              AND (hora_inicio, hora_fin) OVERLAPS (:hora_inicio, :hora_fin)";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'lugar' => $lugar,
        'fecha' => $fecha,
        'id_evento' => $id_evento,
        'hora_inicio' => $hora_inicio,
        'hora_fin' => $hora_fin
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/**
 * Obtiene la capacidad del salón
 */
function obtenerCapacidadSalon($lugar) {
    global $pdo;
    $query = "SELECT capacidad FROM salones WHERE id_salon = :lugar";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['lugar' => $lugar]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Actualiza un evento en la base de datos
 */
function actualizarEvento($id_evento, $nombre, $capacidad, $fecha, $hora_inicio, $hora_fin, $lugar, $campus, $comentario, $direccion, $lineamientos, $expositor) {
    global $pdo;
    $query = "UPDATE evento 
              SET nombre = :nombre, capacidad = :capacidad, fecha = :fecha, 
                  hora_inicio = :hora_inicio, hora_fin = :hora_fin, lugar = :lugar, 
                  campus = :campus, comentario = :comentario, direccion = :direccion, 
                  lineamientos = :lineamientos, expositor = :expositor
              WHERE id = :id_evento";

    $stmt = $pdo->prepare($query);
    return $stmt->execute([
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
}

?>
