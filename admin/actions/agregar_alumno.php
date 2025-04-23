<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$exp        = $_POST['exp'] ?? '';
$nombre     = $_POST['nombre'] ?? '';
$idprograma = $_POST['idprograma'] ?? '';
$mail       = $_POST['mail'] ?? '';
$campus     = $_POST['campus'] ?? '';
$semestre   = $_POST['semestre'] ?? '';
$celular    = $_POST['celular'] ?? '';
$telefono   = $_POST['telefono'] ?? '';
$responsable= $_POST['responsable'] ?? '';

// Validar campos requeridos
if ($exp === '' || $nombre === '' || $idprograma === '') {
    echo json_encode(['error' => 'Campos requeridos faltantes: expediente, nombre y programa']);
    exit();
}

// Verificar si ya existe un alumno con el mismo expediente o nombre
$stmt_check = $pdo->prepare("SELECT COUNT(*) FROM alumnos WHERE exp = :exp OR nombre = :nombre");
$stmt_check->execute([':exp' => $exp, ':nombre' => $nombre]);
$count = $stmt_check->fetchColumn();

if ($count > 0) {
    echo json_encode(['error' => 'Ya existe un alumno con el mismo expediente o nombre']);
    exit();
}

try {
    $sql = "INSERT INTO alumnos (exp, nombre, idprograma, mail, campus, semestre, celular, telefono, responsable)
            VALUES (:exp, :nombre, :idprograma, :mail, :campus, :semestre, :celular, :telefono, :responsable)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':exp'         => $exp,
        ':nombre'      => $nombre,
        ':idprograma'  => $idprograma,
        ':mail'        => $mail,
        ':campus'      => $campus,
        ':semestre'    => $semestre,
        ':celular'     => $celular,
        ':telefono'    => $telefono,
        ':responsable' => $responsable
    ]);
    echo json_encode(['success' => 'Alumno agregado correctamente']);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

