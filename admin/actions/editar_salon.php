<?php
// admin/actions/editar_salon.php
session_start();
require_once __DIR__.'/../config/conexion.php';
if ($_SESSION['tipo_usuario']!=='admin') {
  exit(json_encode(['error'=>'No autorizado']));
}

$id_old = trim($_POST['id_salon_oculto']);
$nombre = trim($_POST['nombre']);
$campus = $_POST['campus'];
$cap    = (int)$_POST['capacidad'];

$sql = "UPDATE salones
           SET nombre   = :nom,
               campus   = :campus,
               capacidad= :cap
         WHERE id_salon = :old";
$stmt = $pdo->prepare($sql);

try {
  $stmt->execute([
    ':nom'    => $nombre,
    ':campus' => $campus,
    ':cap'    => $cap,
    ':old'    => $id_old
  ]);
  echo json_encode(['success'=>'Salón actualizado correctamente.']);
} catch (PDOException $e) {
  if ($e->getCode()==='23505') {
    // Raro en UPDATE, pero por si hubiera cambio de PK
    if (preg_match('/\(([^)]+)\)=\(([^)]+)\)/', $e->getMessage(), $m)) {
      $col = $m[1];
      $val = $m[2];
      $msg = "El $col «$val» ya existe. Por favor, elige otro ID de salón.";
    } else {
      $msg = "Violación de unicidad en salón.";
    }
    echo json_encode(['error'=>$msg]);
  } else {
    echo json_encode(['error'=>'Error inesperado: '.$e->getMessage()]);
  }
}
