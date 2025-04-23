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

// Buscar en alumnos (incluyendo el campo idprograma)
$query = $pdo->prepare("SELECT exp, nombre, mail, campus, semestre, celular, telefono, responsable, idprograma FROM alumnos WHERE exp = :exp");
$query->execute(['exp' => $exp]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "Expediente no encontrado.";
    header("Location: ../index.php?view=login");
    exit();
}

// Guardar datos en sesión
$_SESSION['id_usuario'] = $user['exp'];
$_SESSION['nombre'] = $user['nombre'];
$_SESSION['usuario'] = $user;

// Si el usuario es profesor (idprograma = "PROFESOR"), se marca como tal y se salta la validación del perfil
if ($user['idprograma'] === 'PROFESOR') {
    $_SESSION['tipo_usuario'] = 'profesor';
    $_SESSION['perfil_completo'] = true;
} else {
    // Caso de alumno: se asigna el tipo de usuario y se verifica si el perfil está completo
    $_SESSION['tipo_usuario'] = 'alumno';

    $valores = [
        $user['mail'],
        $user['campus'],
        $user['semestre'],
        $user['celular'],
        $user['telefono'],
        $user['responsable']
    ];

    // Verificar si hay campos vacíos
    $datosIncompletos = in_array(null, $valores) || in_array('', $valores);

    if ($datosIncompletos) {
        $_SESSION['datosFaltantes'] = true;
        header("Location: ../index.php?view=completar_perfil");
        exit();
    } else {
        $_SESSION['perfil_completo'] = true;
    }
}

// Redirigir al home (o la vista que decidas)
header("Location: ../index.php?view=home");
exit();
?>

