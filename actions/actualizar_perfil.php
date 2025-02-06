<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?view=login");
    exit();
}

$exp = $_SESSION['usuario']['exp'];

// Validar los datos antes de actualizar
$mail = filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL);
$campus = in_array($_POST['campus'], ['Norte', 'Sur']) ? $_POST['campus'] : null;
$semestre = filter_var($_POST['semestre'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 12]]);
$celular = preg_match('/^\d{10}$/', $_POST['celular']) ? $_POST['celular'] : null;
$telefono = preg_match('/^\d{10}$/', $_POST['telefono']) ? $_POST['telefono'] : null;
$responsable = preg_match('/^\d{10}$/', $_POST['responsable']) ? $_POST['responsable'] : null;

// Si algún dato no es válido, redirigir de nuevo
if (!$mail || !$campus || !$semestre || !$celular || !$telefono || !$responsable) {
    $_SESSION['error'] = "Datos inválidos, por favor revisa la información.";
    header("Location: index.php?view=completar_perfil");
    exit();
}

// Actualizar en la base de datos
$query = $pdo->prepare("UPDATE alumnos SET mail = :mail, campus = :campus, semestre = :semestre, celular = :celular, telefono = :telefono, responsable = :responsable WHERE exp = :exp");
$query->execute([
    'mail' => $mail,
    'campus' => $campus,
    'semestre' => $semestre,
    'celular' => $celular,
    'telefono' => $telefono,
    'responsable' => $responsable,
    'exp' => $exp
]);

// Actualizar sesión
$_SESSION['usuario']['mail'] = $mail;
$_SESSION['usuario']['campus'] = $campus;
$_SESSION['usuario']['semestre'] = $semestre;
$_SESSION['usuario']['celular'] = $celular;
$_SESSION['usuario']['telefono'] = $telefono;
$_SESSION['usuario']['responsable'] = $responsable;

// Redirigir al home
unset($_SESSION['datosFaltantes']);
header("Location: ../index.php?view=home");
exit();
?>
