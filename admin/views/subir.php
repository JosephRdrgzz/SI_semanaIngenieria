<?php
// views/subir_archivo.php

session_start();
// (opcional) si sólo admin puede subir, descomenta esto:
// if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
//     header('Location: ../../index.php?view=login');
//     exit;
// }

$message = '';
echo '<pre>';
var_dump($_FILES);
echo '</pre>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assets/';
        var_dump($uploadDir, is_dir($uploadDir), is_writable($uploadDir));

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $original = basename($_FILES['archivo']['name']);
        $ext = pathinfo($original, PATHINFO_EXTENSION);
        $name = pathinfo($original, PATHINFO_FILENAME);
        $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $finalName = $safe . '_' . uniqid() . '.' . $ext;

        $target = $uploadDir . $finalName;

        // Debugging checks
        if (!file_exists($_FILES['archivo']['tmp_name'])) {
            $message = "El archivo temporal no existe.";
        } elseif (!is_writable($uploadDir)) {
            $message = "El directorio de destino no tiene permisos de escritura.";
        } elseif (move_uploaded_file($_FILES['archivo']['tmp_name'], $target)) {
            $message = "¡Subida exitosa! Ruta pública: <code>assets/{$finalName}</code>";
        } else {
            $message = "Error al mover el archivo.";
        }
    } else {
        $message = "No se recibió ningún archivo o hubo un error en la subida.";
    }
}?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Archivo</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; }
        .message { margin-bottom: 1rem; color: green; }
        .error   { color: red; }
    </style>
</head>
<body>
<h1>Subir Archivo a <code>assets/</code></h1>
<?php if ($message): ?>
    <p class="message"><?= $message ?></p>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label>Selecciona un archivo:
        <input type="file" name="archivo" required>
    </label>
    <button type="submit">Subir</button>
</form>
</body>
</html>
