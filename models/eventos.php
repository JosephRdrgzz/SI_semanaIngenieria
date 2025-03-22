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

    // 1) Obtener eventos donde el usuario YA ESTÁ INSCRITO
    $query = "SELECT id, nombre, fecha, hora_inicio, hora_fin 
              FROM evento
              WHERE jsonb_exists(asistencia, :exp)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['exp' => $exp]);
    $eventosInscritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2) Obtener eventos NUEVOS a los que se quiere inscribir
    $placeholders = implode(",", array_fill(0, count($eventosSeleccionados), "?"));
    $query = "SELECT id, nombre, fecha, hora_inicio, hora_fin 
              FROM evento 
              WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($query);
    $stmt->execute($eventosSeleccionados);
    $eventosNuevos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $traslapes = [];

    // 3) Verificar traslapes con eventosInscritos
    foreach ($eventosInscritos as $inscrito) {
        foreach ($eventosNuevos as $nuevo) {
            if ($inscrito['fecha'] === $nuevo['fecha'] &&
                !(
                    strtotime($inscrito['hora_fin']) <= strtotime($nuevo['hora_inicio']) ||
                    strtotime($inscrito['hora_inicio']) >= strtotime($nuevo['hora_fin'])
                )
            ) {
                $traslapes[] = "{$nuevo['nombre']} se traslapa con {$inscrito['nombre']}";
            }
        }
    }

    // 4) Verificar si los NUEVOS eventos se traslapan entre sí
    for ($i = 0; $i < count($eventosNuevos); $i++) {
        for ($j = $i + 1; $j < count($eventosNuevos); $j++) {
            $e1 = $eventosNuevos[$i];
            $e2 = $eventosNuevos[$j];
            if ($e1['fecha'] === $e2['fecha'] &&
                !(
                    strtotime($e1['hora_fin']) <= strtotime($e2['hora_inicio']) ||
                    strtotime($e1['hora_inicio']) >= strtotime($e2['hora_fin'])
                )
            ) {
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
            // Bloqueamos la fila para evitar condiciones de carrera
            $query = "SELECT asistencia FROM evento WHERE id = :id FOR UPDATE";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['id' => $evento_id]);
            $evento = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$evento) {
                throw new Exception("Evento no encontrado.");
            }

            // Decodificamos el campo asistencia; si es NULL o no es un array, se inicializa como objeto vacío
            $asistenciaActual = json_decode($evento['asistencia'], true);
            if (!is_array($asistenciaActual)) {
                $asistenciaActual = [];
            }

            // Verificamos si el usuario ya está inscrito (la clave ya existe)
            if (isset($asistenciaActual[$exp])) {
                throw new Exception("Ya estás inscrito en este evento.");
            }

            // Agregamos la inscripción: se añade la clave del usuario con un arreglo vacío
            $asistenciaActual[$exp] = [];

            // Actualizamos el campo asistencia con el nuevo objeto JSON
            $queryUpdate = "UPDATE evento SET asistencia = :nuevaAsistencia WHERE id = :id";
            $stmtUpdate = $pdo->prepare($queryUpdate);
            $stmtUpdate->execute([
                'nuevaAsistencia' => json_encode($asistenciaActual),
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