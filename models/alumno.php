<?php
require_once __DIR__ . '/../config/conexion.php';

/**
 * Obtiene la información de un alumno por su expediente.
 */
function obtenerAlumno($exp) {
    global $pdo;
    $query = $pdo->prepare("SELECT exp, nombre, idprograma, mail, campus, semestre, celular, telefono, responsable 
                            FROM alumnos WHERE exp = :exp");
    $query->execute(['exp' => $exp]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

/**
 * Actualiza la información del alumno (solo los campos permitidos).
 */
function actualizarAlumno($exp, $nuevo_campus, $nuevo_mail, $nuevo_celular, $nuevo_responsable) {
    global $pdo;
    $query = $pdo->prepare("UPDATE alumnos 
                            SET campus = :campus, mail = :mail, celular = :celular, responsable = :responsable 
                            WHERE exp = :exp");
    return $query->execute([
        'campus' => $nuevo_campus,
        'mail' => $nuevo_mail,
        'celular' => $nuevo_celular,
        'responsable' => $nuevo_responsable,
        'exp' => $exp
    ]);
}
?>
<?php
