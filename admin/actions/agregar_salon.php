<?php
// admin/actions/agregar_salon.php
session_start();
require_once __DIR__.'/../config/conexion.php';
if ($_SESSION['tipo_usuario']!=='admin') {
  exit(json_encode(['error'=>'No autorizado']));
}

$id     = trim($_POST['id_salon']);
$nombre = trim($_POST['nombre']);
$campus = $_POST['campus'];
$cap    = (int)$_POST['capacidad'];

$sql = "INSERT INTO salones(id_salon,nombre,campus,capacidad)
        VALUES(:id,:nom,:campus,:cap)";
$stmt = $pdo->prepare($sql);

try {
  $stmt->execute([
    ':id'       => $id,
    ':nom'      => $nombre,
    ':campus'   => $campus,
    ':cap'      => $cap
  ]);
  echo json_encode(['success'=>'Salón creado correctamente.']);
} catch (PDOException $e) {
  if ($e->getCode()==='23505') {
    // Duplicate key
    if (preg_match('/\(([^)]+)\)=\(([^)]+)\)/', $e->getMessage(), $m)) {
      $col = $m[1];
      $val = $m[2];
      $msg = "El $col «$val» ya existe. Por favor, elige otro ID de salón.";
    } else {
      $msg = "Ya existe un salón con ese ID.";
    }
    echo json_encode(['error'=>$msg]);
  } else {
    echo json_encode(['error'=>'Error inesperado: '.$e->getMessage()]);
  }
}
