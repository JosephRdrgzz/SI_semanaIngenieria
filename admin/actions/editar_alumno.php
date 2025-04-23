<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$expOriginal = $_POST['exp_oculto'] ?? '';
if ($expOriginal === '') {
    echo json_encode(['error' => 'Expediente original no especificado']);
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

// Verificar si ya existe otro alumno (distinto del actual) con el mismo expediente o nombre
$stmt_check = $pdo->prepare("SELECT COUNT(*) FROM alumnos WHERE (exp = :exp OR nombre = :nombre) AND exp <> :expOriginal");
$stmt_check->execute([':exp' => $exp, ':nombre' => $nombre, ':expOriginal' => $expOriginal]);
$count = $stmt_check->fetchColumn();

if ($count > 0) {
    echo json_encode(['error' => 'Ya existe otro alumno con el mismo expediente o nombre']);
    exit();
}

try {
    $sql = "UPDATE alumnos
            SET exp = :exp, nombre = :nombre, idprograma = :idprograma,
                mail = :mail, campus = :campus, semestre = :semestre,
                celular = :celular, telefono = :telefono, responsable = :responsable
            WHERE exp = :expOriginal";
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
        ':responsable' => $responsable,
        ':expOriginal' => $expOriginal
    ]);
    echo json_encode(['success' => 'Alumno actualizado correctamente']);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

