<?php
session_start();
require_once __DIR__ . '/../models/alumno.php';

// Verificar que el usuario está autenticado y que se envió el expediente
if (!isset($_SESSION['usuario']) || !isset($_POST['exp'])) {
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

$exp = $_POST['exp'];

// Validar los datos recibidos
$nuevo_campus = $_POST['campus'] ?? null;
$nuevo_mail = $_POST['mail'] ?? null;
$nuevo_celular = $_POST['celular'] ?? null;
$nuevo_responsable = $_POST['responsable'] ?? null;

// Validar que el correo termine en @anahuac.mx
if (!preg_match('/^[a-zA-Z0-9._%+-]+@anahuac\.mx$/', $nuevo_mail)) {
    echo json_encode(["error" => "El correo debe ser @anahuac.mx"]);
    exit();
}

// Actualizar los datos en la base de datos
if (actualizarAlumno($exp, $nuevo_campus, $nuevo_mail, $nuevo_celular, $nuevo_responsable)) {
    echo json_encode(["success" => "Perfil actualizado correctamente"]);
} else {
    echo json_encode(["error" => "Error al actualizar el perfil"]);
}
?>
