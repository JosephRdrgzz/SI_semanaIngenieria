<?php
require_once __DIR__ . '/../config/conexion.php';

/**
 * Obtiene todos los eventos disponibles en la base de datos
 */
function obtenerEventos() {
    global $pdo;
    $query = $pdo->query("SELECT id, nombre, fecha, hora_inicio, hora_fin, lugar, campus FROM evento ORDER BY fecha, hora_inicio");
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Verifica si un usuario tiene traslapes con los eventos seleccionados
 */
require_once __DIR__ . '/../config/conexion.php';

function verificarTraslapes($exp, $eventosSeleccionados) {
    global $pdo;

    // Obtener eventos en los que el usuario YA ESTÁ INSCRITO
    $query = "SELECT id, nombre, fecha, hora_inicio, hora_fin 
              FROM evento 
              WHERE asistencia @> :alumno::jsonb";
    $stmt = $pdo->prepare($query);
    $alumnoJson = json_encode([["exp" => $exp]]);
    $stmt->execute(['alumno' => $alumnoJson]);
    $eventosInscritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener eventos NUEVOS a los que se quiere inscribir
    $placeholders = implode(",", array_fill(0, count($eventosSeleccionados), "?"));
    $query = "SELECT id, nombre, fecha, hora_inicio, hora_fin 
              FROM evento 
              WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($eventosSeleccionados);
    $eventosNuevos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $traslapes = [];

    // VERIFICACIÓN DE TRASLAPES USANDO LA LÓGICA DEL SQL ORIGINAL
    foreach ($eventosInscritos as $inscrito) {
        foreach ($eventosNuevos as $nuevo) {
            if ($inscrito['fecha'] == $nuevo['fecha'] &&
                !(
                    strtotime($inscrito['hora_fin']) <= strtotime($nuevo['hora_inicio']) ||
                    strtotime($inscrito['hora_inicio']) >= strtotime($nuevo['hora_fin'])
                )) {
                $traslapes[] = "{$nuevo['nombre']} se traslapa con {$inscrito['nombre']}";
            }
        }
    }

    // AHORA VERIFICAMOS SI LOS NUEVOS EVENTOS SE TRASLAPAN ENTRE SÍ
    for ($i = 0; $i < count($eventosNuevos); $i++) {
        for ($j = $i + 1; $j < count($eventosNuevos); $j++) {
            $e1 = $eventosNuevos[$i];
            $e2 = $eventosNuevos[$j];

            if ($e1['fecha'] == $e2['fecha'] &&
                !(
                    strtotime($e1['hora_fin']) <= strtotime($e2['hora_inicio']) ||
                    strtotime($e1['hora_inicio']) >= strtotime($e2['hora_fin'])
                )) {
                $traslapes[] = "{$e1['nombre']} se traslapa con {$e2['nombre']}";
            }
        }
    }

    return $traslapes;
}


/**
 * Registra al usuario en uno o más eventos
 */
function inscribirUsuario($exp, $nombre, $eventosSeleccionados) {
    global $pdo;

    try {
        $pdo->beginTransaction();

        foreach ($eventosSeleccionados as $evento_id) {
            // Obtener la asistencia actual del evento
            $query = "SELECT asistencia FROM evento WHERE id = :id FOR UPDATE";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $evento_id]);
            $evento = $stmt->fetch(PDO::FETCH_ASSOC);

            // Decodificar el JSONB de asistencia
            $asistenciaActual = json_decode($evento['asistencia'], true) ?? [];

            // Verificar si el usuario ya está inscrito
            foreach ($asistenciaActual as $asistente) {
                if ($asistente['exp'] === $exp) {
                    throw new Exception("Ya estás inscrito en este evento.");
                }
            }

            // Agregar nuevo asistente
            $nuevoAsistente = ["exp" => $exp, "nombre" => $nombre];
            $asistenciaActual[] = $nuevoAsistente;

            // Convertir de nuevo a JSON y actualizar en la base de datos
            $query = "UPDATE evento SET asistencia = :asistencia WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'asistencia' => json_encode($asistenciaActual),
                'id' => $evento_id
            ]);
        }

        $pdo->commit();
        return ["success" => "Inscripción exitosa"];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ["error" => "Error al inscribir: " . $e->getMessage()];
    }
}


/**
 * Obtiene la lista de eventos en los que el usuario está inscrito
 */
function obtenerMisEventos($exp) {
    global $pdo;

    $query = $pdo->prepare("SELECT id, nombre, fecha, hora_inicio, hora_fin, lugar, campus FROM evento WHERE asistencia @> :json_data");
    $query->execute(['json_data' => json_encode([["exp" => $exp]])]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Cancela la inscripción de un usuario a un evento
 */
function cancelarInscripcion($exp, $eventoId) {
    global $pdo;

    // Obtener lista de asistencia
    $query = $pdo->prepare("SELECT asistencia FROM evento WHERE id = :id");
    $query->execute(['id' => $eventoId]);
    $evento = $query->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        return ["error" => "Evento no encontrado"];
    }

    $asistencia = json_decode($evento['asistencia'], true) ?? [];

    // Eliminar usuario de la lista de asistencia
    $asistencia = array_filter($asistencia, fn($asistente) => $asistente['exp'] != $exp);

    // Actualizar en la base de datos
    $query = $pdo->prepare("UPDATE evento SET asistencia = :asistencia WHERE id = :id");
    $query->execute([
        'asistencia' => json_encode(array_values($asistencia)), // Reindexar array
        'id' => $eventoId
    ]);

    return ["success" => "Inscripción cancelada"];
}
?>
