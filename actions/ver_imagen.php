<?php
// actions/ver_imagen.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config/conexion.php';

// 1) Validar parámetro
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("HTTP/1.1 400 Bad Request");
    exit("ID inválido");
}

try {
    // 2) Recuperar blob y MIME (añade columna imagen_mime a tu tabla)
    $stmt = $pdo->prepare("
        SELECT imagen_blob, imagen_mime
          FROM evento
         WHERE id = :id
           AND imagen_blob IS NOT NULL
         LIMIT 1
    ");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        header("HTTP/1.1 404 Not Found");
        exit("No hay imagen para este evento");
    }

    // 3) Descomprimir si fuera necesario
    $blob = @gzuncompress($row['imagen_blob']);
    if ($blob === false) {
        $blob = $row['imagen_blob'];
    }

    // 4) Enviar cabeceras y contenido
    $mime = $row['imagen_mime'] ?? 'application/octet-stream';
    header("Content-Type: $mime");
    header("Cache-Control: public, max-age=86400");
    echo $blob;

} catch (Exception $e) {
    error_log("ver_imagen.php error: " . $e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    exit("Error interno: " . $e->getMessage());
}
