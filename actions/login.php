<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que se haya enviado un expediente
if (!isset($_POST['exp']) || empty($_POST['exp'])) {
    die("Expediente requerido.");
}

$exp = $_POST['exp'];

// Consultar si el expediente existe
$query = $pdo->prepare("SELECT exp, nombre, mail, campus, semestre, celular, telefono, responsable FROM alumnos WHERE exp = :exp");
$query->execute(['exp' => $exp]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Expediente no encontrado.");
}

// Guardamos datos clave en sesión
$_SESSION['id_usuario'] = $user['exp'];
$_SESSION['nombre'] = $user['nombre'];
$_SESSION['semestre'] = $user['semestre'];
$_SESSION['usuario'] = $user;  // Guardar toda la información también, por si se necesita

// Verificamos si falta al menos un campo
$valores = [$user['mail'], $user['campus'], $user['semestre'], $user['celular'], $user['telefono'], $user['responsable']];
$datosIncompletos = in_array(null, $valores) || in_array('', $valores);

//  Si hay datos incompletos, redirigir a completar perfil
if ($datosIncompletos) {
    $_SESSION['datosFaltantes'] = true;
    header("Location: ../index.php?view=completar_perfil");
    exit();
}

//  Si todo está completo, redirigir al home
header("Location: ../index.php?view=home");
exit();
