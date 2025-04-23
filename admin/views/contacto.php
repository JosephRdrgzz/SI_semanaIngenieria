<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../index.php?view=login");
    exit();
}

// Snippet para obligar a verificar sesión en la vista
$headerSnippet = "<?php
if (!isset(\$_SESSION['id_usuario'])) {
    header(\"Location: index.php?view=login\");
    exit();
}
?>\n\n";

// Ruta al archivo a editar (contacto.php)
$archivoVista = __DIR__ . '/../../views/contacto.php';

$message = null; // Para mostrar mensajes en la misma página

// Al enviar el formulario con el contenido Quill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenido'])) {
    $contenido = $_POST['contenido'];

    // Definimos el CSS responsivo y la clase contenedor-contacto
    $cssResponsivo = <<<CSS
<style>
  .contenedor-contacto {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  .contenedor-contacto img {
    max-width: 100%;
    height: auto;
  }
  @media (max-width: 768px) {
    .contenedor-contacto {
      max-width: 100% !important;
      padding: 10px;
    }
  }
</style>
CSS;

    // Construimos el contenido a guardar en contacto.php
    $contenidoAGuardar = $headerSnippet
        . $cssResponsivo
        . "<div class=\"contenedor-contacto\">"
        . $contenido
        . "</div>";

    // Guardamos en contacto.php
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

    // 1) Quitar el $headerSnippet
    if (strpos($contenidoActual, $headerSnippet) === 0) {
        $contenidoActual = substr($contenidoActual, strlen($headerSnippet));
    }

    // 2) Quitar el <style>...</style> si está al inicio
    $cssStart = "<style>";
    $cssEnd   = "</style>";
    if (strpos($contenidoActual, $cssStart) === 0) {
        $posEnd = strpos($contenidoActual, $cssEnd);
        if ($posEnd !== false) {
            $contenidoActual = substr($contenidoActual, $posEnd + strlen($cssEnd));
        }
    }

    // 3) Quitar el <div class="contenedor-contacto">...</div>
    $wrapperStart = '<div class="contenedor-contacto">';
    $wrapperEnd   = '</div>';
    $contenidoTrim = trim($contenidoActual);

    if (
        strpos($contenidoTrim, $wrapperStart) === 0 &&
        substr($contenidoTrim, -strlen($wrapperEnd)) === $wrapperEnd
    ) {
        // Quitamos el wrapper
        $contenidoTrim = substr($contenidoTrim, strlen($wrapperStart), -strlen($wrapperEnd));
        $contenidoActual = trim($contenidoTrim);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Vista - Contacto</title>
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
        /* Asegurarnos de que las imágenes en el editor Quill no excedan el contenedor */
        .ql-editor img {
            max-width: 100%;
            height: auto;
        }
    </style>
    <!-- Incluir CSS de Quill -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
</head>
<body>
<div class="edit-view-editor">
    <h2>Editar Contenido de Contacto</h2>

    <!-- Mensajes de éxito o error -->
    <?php if ($message === 'success'): ?>
        <div class="message">Contenido actualizado correctamente.</div>
    <?php elseif ($message === 'error'): ?>
        <div class="message">Error al actualizar el contenido.</div>
    <?php endif; ?>

    <!-- Formulario para guardar el contenido -->
    <form id="edit-form" action="" method="post">
        <div id="editor-container">
            <!-- Contenedor Quill -->
            <div id="editor" style="height: 300px;"></div>
            <!-- Campo oculto con el contenido HTML -->
            <input type="hidden" name="contenido" id="contenido">
        </div>
        <button type="submit">Guardar Cambios</button>
    </form>
</div>

<!-- Librería de Quill -->
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

