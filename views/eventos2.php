<?php
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php?view=login");
    exit();
}
$query = $pdo->prepare("SELECT id, nombre, capacidad, fecha, hora_inicio, hora_fin, lugar, campus FROM evento");
$query->execute();
$eventos = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Alternativos</title>
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .evento-badge {
            padding: 5px 12px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            display: inline-block;
            margin-top: 8px;
        }
        .campus-norte {
            background-color: #4CAF50;
        }
        .campus-sur {
            background-color: #2196F3;
        }
        .campus-externo {
            background-color: #FF9800;
        }
        .card-detail {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .card-detail i {
            margin-right: 10px;
            color: #26a69a;
        }
        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-content {
            flex-grow: 1;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 style="text-align: center;">Eventos Disponibles</h2>
    <p style="text-align: center;">Para inscribirte a un evento, selecciona los eventos que te interesen y haz clic en "Registrarse".</p>
    <div class="row" id="contenedor-eventos-alternativo"></div>
</div>

<!-- Carrito flotante de eventos seleccionados -->
<div id="carrito-eventos" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none; /* Por defecto oculto */
    max-width: 300px;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    z-index: 9999;
">
    <h3 style="margin-top: 0;">Eventos seleccionados</h3>
    <ul id="lista-eventos-seleccionados" style="list-style: disc; padding-left: 20px; margin: 0;"></ul>

    <!-- Formulario de inscripción dentro del carrito -->
    <form id="form-inscripcion" style="margin-top: 10px;">
        <button type="submit" style="width: 100%; background-color: orange;">
            <span class="text">Inscribirse</span>
        </button>
    </form>
</div>

<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<!-- Carga tu archivo JavaScript al final -->
<script src="scripts/eventos_alternativo.js"></script>
<style>        nav {
        all: revert;  /* O 'all: unset;' */
        /* Vuelve a estilos por defecto del navegador */
    }</style>
</body>
</html>