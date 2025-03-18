<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: ../../index.php?view=login");
    exit();
}

// Definir el encabezado obligatorio a incluir en la vista
$headerSnippet = "<?php
if (!isset(\$_SESSION['id_usuario'])) {
    header(\"Location: index.php?view=login\");
    exit();
}
?>\n\n";

// Ruta al archivo de vista que se desea editar
$archivoVista = __DIR__ . '/../../views/generalidades.php';

$message = null; // Variable para mostrar mensajes en la misma página

// Si llega contenido por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenido'])) {
    $contenido = $_POST['contenido'];

    // Envuelves el contenido que se guardará en un contenedor centrado
    $contenidoAGuardar = $headerSnippet
        . '<div style="max-width: 800px; margin: 0 auto; padding: 20px;">'
        . $contenido
        . '</div>';

    if (file_put_contents($archivoVista, $contenidoAGuardar) !== false) {
        $message = 'success'; // Se guardó correctamente
    } else {
        $message = 'error'; // Ocurrió un problema al guardar
    }
}

// Leer el contenido actual (si existe) para cargarlo en el editor
$contenidoActual = '';
if (file_exists($archivoVista)) {
    $contenidoActual = file_get_contents($archivoVista);
    // Remover el header obligatorio
    if (strpos($contenidoActual, $headerSnippet) === 0) {
        $contenidoActual = substr($contenidoActual, strlen($headerSnippet));
    }
    // Remover el contenedor centrado si está presente
    $wrapperStart = '<div style="max-width: 800px; margin: 0 auto; padding: 20px;">';
    $wrapperEnd   = '</div>';

    if (
        strpos($contenidoActual, $wrapperStart) === 0 &&
        substr(trim($contenidoActual), -strlen($wrapperEnd)) === $wrapperEnd
    ) {
        $contenidoActual = substr($contenidoActual, strlen($wrapperStart), -strlen($wrapperEnd));
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
    </style>

    <!-- Incluir CSS de Quill -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
</head>
<body>
<div class="edit-view-editor">
    <h2>Editar Contenido de Generalidades</h2>

    <!-- Mostrar mensaje de éxito o error -->
    <?php if ($message === 'success'): ?>
        <div class="message">Contenido actualizado correctamente.</div>
    <?php elseif ($message === 'error'): ?>
        <div class="message">Error al actualizar el contenido.</div>
    <?php endif; ?>

    <!-- Formulario para guardar el contenido -->
    <form id="edit-form" action="" method="post">
        <div id="editor-container">
            <!-- Contenedor del editor -->
            <div id="editor" style="height: 300px;"></div>
            <!-- Campo oculto para enviar el contenido HTML -->
            <input type="hidden" name="contenido" id="contenido">
        </div>
        <button type="submit">Guardar Cambios</button>
    </form>
</div>

<!-- Incluir la librería de Quill -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    // Inicializar Quill
    const quill = new Quill('#editor', {
        theme: 'snow'
    });

    // Cargar contenido del archivo en Quill
    quill.root.innerHTML = `<?= addslashes($contenidoActual) ?>`;

    // Antes de enviar el formulario, copiar el contenido HTML del editor al campo hidden
    document.getElementById('edit-form').addEventListener('submit', function(e) {
        document.getElementById('contenido').value = quill.root.innerHTML;
    });
</script>
</body>
</html>
