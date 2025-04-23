<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../index.php?view=login");
    exit();
}

// Snippet para proteger la vista generalidades
$headerSnippet = "<?php
if (!isset(\$_SESSION['id_usuario'])) {
    header(\"Location: index.php?view=login\");
    exit();
}
?>\n\n";

// Ruta al archivo a editar (generalidades.php)
$archivoVista = __DIR__ . '/../../views/generalidades.php';

$message = null;

// Si llega contenido por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenido'])) {
    $contenido = $_POST['contenido'];

    // Definimos el CSS responsivo y la clase .contenedor-home
    $cssResponsivo = <<<CSS
<style>
  .contenedor-home {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  .contenedor-home img {
    max-width: 100%;
    height: auto;
  }
  @media (max-width: 768px) {
    .contenedor-home {
      max-width: 100% !important;
      padding: 10px;
    }
  }
</style>
CSS;

    // Construimos el contenido a guardar
    $contenidoAGuardar = $headerSnippet
        . $cssResponsivo
        . "<div class=\"contenedor-home\">"
        . $contenido
        . "</div>";

    // Guardamos en generalidades.php
    if (file_put_contents($archivoVista, $contenidoAGuardar) !== false) {
        $message = 'success';
    } else {
        $message = 'error';
    }
}

// Leer el contenido actual para cargarlo en Quill
$contenidoActual = '';
if (file_exists($archivoVista)) {
    $contenidoActual = file_get_contents($archivoVista);

    // 1) Quitar el headerSnippet
    if (strpos($contenidoActual, $headerSnippet) === 0) {
        $contenidoActual = substr($contenidoActual, strlen($headerSnippet));
    }

    // 2) Quitar el bloque <style>...</style>
    $cssStart = "<style>";
    $cssEnd   = "</style>";
    if (strpos($contenidoActual, $cssStart) === 0) {
        $posEnd = strpos($contenidoActual, $cssEnd);
        if ($posEnd !== false) {
            $contenidoActual = substr($contenidoActual, $posEnd + strlen($cssEnd));
        }
    }

    // 3) Quitar <div class="contenedor-home"> ... </div>
    $wrapperStart = '<div class="contenedor-home">';
    $wrapperEnd   = '</div>';
    $contenidoTrim = trim($contenidoActual);

    if (
        strpos($contenidoTrim, $wrapperStart) === 0 &&
        substr($contenidoTrim, -strlen($wrapperEnd)) === $wrapperEnd
    ) {
        $contenidoTrim = substr($contenidoTrim, strlen($wrapperStart), -strlen($wrapperEnd));
        $contenidoActual = trim($contenidoTrim);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Vista - Generalidades</title>
    <style>
        .edit-view-editor {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .edit-view-editor h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .edit-view-editor .message {
            margin: 10px 0;
            padding: 10px;
            background-color: #e0f7fa;
            border: 1px solid #b2ebf2;
            border-radius: 3px;
            text-align: center;
            color: #00695c;
        }
        .edit-view-editor button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #1cc972;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        /* Para que en el editor Quill las imágenes no excedan el contenedor */
        .ql-editor img {
            max-width: 100%;
            height: auto;
        }
    </style>
    <!-- Quill CSS -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
</head>
<body>
<div class="edit-view-editor">
    <h2>Editar Contenido de Generalidades</h2>

    <!-- Mensaje de éxito/error -->
    <?php if ($message === 'success'): ?>
        <div class="message">Contenido actualizado correctamente.</div>
    <?php elseif ($message === 'error'): ?>
        <div class="message">Error al actualizar el contenido.</div>
    <?php endif; ?>

    <form id="edit-form" action="" method="post">
        <div id="editor-container">
            <div id="editor" style="height: 300px;"></div>
            <input type="hidden" name="contenido" id="contenido">
        </div>
        <button type="submit">Guardar Cambios</button>
    </form>
</div>

<!-- Quill JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    // Inicializar Quill
    const quill = new Quill('#editor', { theme: 'snow' });

    // Guardamos el contenido actual en una variable de JavaScript
    const contenidoActual = <?= json_encode($contenidoActual) ?>;

    // Insertamos el contenido en Quill
    quill.clipboard.dangerouslyPasteHTML(contenidoActual);

    // Al enviar el formulario, pasamos el contenido a <input hidden>
    document.getElementById('edit-form').addEventListener('submit', function() {
        document.getElementById('contenido').value = quill.root.innerHTML;
    });
</script>

</body>
</html>

