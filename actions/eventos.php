<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

header('Content-Type: application/json');

// 1) Verificar sesión de alumno
if (!isset($_SESSION['usuario']['exp'])) {
    echo json_encode(["error" => "Usuario no autenticado"]);
    exit();
}

// 2) Exp del alumno
$exp = (string) $_SESSION['usuario']['exp'];

try {
    // 3) Consulta: sólo los que no se ha inscrito y aún caben,
    //    y devolvemos la ruta + flag de imagen
    $sql = <<<SQL
SELECT
  id,
  nombre,
  capacidad,
  fecha,
  hora_inicio,
  hora_fin,
  lugar,
  campus,
  tipo_evento,
  imagen_path,
  (imagen_path IS NOT NULL AND imagen_path <> '') AS has_image,
  COALESCE(
    (
      SELECT count(*) 
      FROM jsonb_object_keys(COALESCE(asistencia,'{}')::jsonb)
    ), 
    0
  ) AS inscritos
FROM evento
WHERE
  NOT jsonb_exists(COALESCE(asistencia,'{}')::jsonb, :exp)
  AND COALESCE(
    (
      SELECT count(*) 
      FROM jsonb_object_keys(COALESCE(asistencia,'{}')::jsonb)
    ), 
    0
  ) < capacidad
ORDER BY fecha, hora_inicio
SQL;

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['exp' => $exp]);
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4) Devolver JSON puro con el array de eventos
    echo json_encode($eventos);

} catch (Exception $e) {
    echo json_encode([
        "error" => "Error al obtener eventos: " . $e->getMessage()
    ]);
}
