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

// 1     Buscar en alumnos
$query = $pdo->prepare("SELECT exp, nombre, mail, campus, semestre, celular, telefono, responsable FROM alumnos WHERE exp = :exp");
$query->execute(['exp' => $exp]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // 2 Si no es alumno, buscar en administradores
    $query = $pdo->prepare("SELECT exp, nombre, correo, contraseña FROM administradores WHERE exp = :exp");
    $query->execute(['exp' => $exp]);
    $admin = $query->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $esAdmin = true;

        // Verificar que se envió la contraseña
        if (!isset($_POST['contraseña']) || empty($_POST['contraseña'])) {
            $_SESSION['admin_exp'] = $admin['exp'];
            $_SESSION['admin_nombre'] = $admin['nombre'];
            $_SESSION['admin_pending'] = true; // Indica que necesita contraseña
            header("Location: ../index.php?view=login");
            exit();
        }

        // 3 Validar la contraseña con SHA256
        if ($admin && hash('sha256', $_POST['contraseña']) === $admin['contraseña']) {
            $_SESSION['id_usuario'] = $admin['exp'];
            $_SESSION['nombre'] = $admin['nombre'];
            $_SESSION['tipo_usuario'] = 'admin';

            // Eliminar datos temporales
            unset($_SESSION['admin_exp'], $_SESSION['admin_nombre'], $_SESSION['admin_pending']);

            header("Location: ../index.php?view=panel_admin");
            exit();
        } else {
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: ../index.php?view=login");
            exit();
        }

    } else {
        $_SESSION['error'] = "Expediente no encontrado.";
        header("Location: ../index.php?view=login");
        exit();
    }
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
