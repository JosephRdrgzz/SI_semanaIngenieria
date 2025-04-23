<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que se ha enviado el campo "usuario"
if (!isset($_POST['usuario']) || empty(trim($_POST['usuario']))) {
    $_SESSION['error'] = "Se requiere ID o correo.";
    header("Location: ../index.php?view=login");
    exit();
}

$usuario = trim($_POST['usuario']);
$isEmail = (strpos($usuario, '@') !== false); // Si contiene @ se trata de correo

// Primero, buscar en la tabla alumnos
if ($isEmail) {
    $query = $pdo->prepare("SELECT exp, nombre, mail, campus, semestre, celular, telefono, responsable 
                            FROM alumnos WHERE mail = :usuario");
} else {
    $query = $pdo->prepare("SELECT exp, nombre, mail, campus, semestre, celular, telefono, responsable 
                            FROM alumnos WHERE exp = :usuario");
}
$query->execute(['usuario' => $usuario]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Iniciar sesión como alumno
    $_SESSION['id_usuario'] = $user['exp'];
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['semestre'] = $user['semestre'];
    $_SESSION['usuario'] = $user;
    $_SESSION['tipo_usuario'] = 'alumno';

    // Verificar si faltan datos
    $valores = [$user['mail'], $user['campus'], $user['semestre'], $user['celular'], $user['telefono'], $user['responsable']];
    $datosIncompletos = in_array(null, $valores) || in_array('', $valores);
    if ($datosIncompletos) {
        $_SESSION['datosFaltantes'] = true;
        header("Location: ../index.php?view=completar_perfil");
        exit();
    }
    header("Location: ../index.php?view=home");
    exit();
}

// Si no es alumno, buscar en administradores
if ($isEmail) {
    $query = $pdo->prepare("SELECT exp, nombre, correo, contraseña FROM administradores WHERE correo = :usuario");
} else {
    $query = $pdo->prepare("SELECT exp, nombre, correo, contraseña FROM administradores WHERE exp = :usuario");
}
$query->execute(['usuario' => $usuario]);
$admin = $query->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    $_SESSION['error'] = "Usuario no encontrado.";
    header("Location: ../index.php?view=login");
    exit();
}

// Verificar que se envió la contraseña
if (!isset($_POST['contraseña']) || empty(trim($_POST['contraseña']))) {
    $_SESSION['admin_usuario'] = $usuario;
    $_SESSION['admin_nombre'] = $admin['nombre'];
    $_SESSION['admin_pending'] = true; // Necesita ingresar contraseña
    $_SESSION['error'] = "Contraseña requerida para administradores.";
    header("Location: ../index.php?view=login");
    exit();
}

$passIngresada = trim($_POST['contraseña']);
$hashIngresado = hash('sha256', $passIngresada);

if ($hashIngresado !== $admin['contraseña']) {
    $_SESSION['error'] = "Contraseña incorrecta.";
    header("Location: ../index.php?view=login");
    exit();
}

// Iniciar sesión como administrador
$_SESSION['id_usuario'] = $admin['exp'];
$_SESSION['nombre'] = $admin['nombre'];
$_SESSION['tipo_usuario'] = 'admin';

// Limpiar variables temporales
unset($_SESSION['admin_usuario'], $_SESSION['admin_nombre'], $_SESSION['admin_pending']);

header("Location: ../index.php?view=panel_admin");
exit();
?>

