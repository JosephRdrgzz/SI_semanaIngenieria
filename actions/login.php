<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que se ha enviado un expediente
if (!isset($_POST['exp']) || empty($_POST['exp'])) {
    $_SESSION['error'] = "Expediente requerido.";
    header("Location: ../index.php?view=login");
    exit();
}

$exp = $_POST['exp'];
$esAdmin = false;

// 1️⃣ Buscar en alumnos
$query = $pdo->prepare("SELECT exp, nombre, mail, campus, semestre, celular, telefono, responsable FROM alumnos WHERE exp = :exp");
$query->execute(['exp' => $exp]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
        $_SESSION['error'] = "Expediente no encontrado.";
        header("Location: ../index.php?view=login");
        exit();
}

// **Alumno: Guardar datos en sesión y verificar campos faltantes**
$_SESSION['id_usuario'] = $user['exp'];
$_SESSION['nombre'] = $user['nombre'];
$_SESSION['semestre'] = $user['semestre'];
$_SESSION['usuario'] = $user;
$_SESSION['tipo_usuario'] = 'alumno';

// Verificar si falta al menos un campo
$valores = [$user['mail'], $user['campus'], $user['semestre'], $user['celular'], $user['telefono'], $user['responsable']];
$datosIncompletos = in_array(null, $valores) || in_array('', $valores);

if ($datosIncompletos) {
    $_SESSION['datosFaltantes'] = true;
    header("Location: ../index.php?view=completar_perfil");
    exit();
}

// **Redirigir al home**
header("Location: ../index.php?view=home");
exit();
?>
