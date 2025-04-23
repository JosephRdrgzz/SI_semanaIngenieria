<?php
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_GET['id'])) {
    header("Location: index.php?view=eventos");
    exit();
}

$id_evento = $_GET['id'];
$query = $pdo->prepare("SELECT * FROM evento WHERE id = :id");
$query->execute(['id' => $id_evento]);
$evento = $query->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    header("Location: index.php?view=eventos2");
    exit();
}

// Función para eliminar el primer párrafo si existe
function removeLeadingP($html) {
    $html = trim($html);
    // Comprobar si inicia con <p>
    if (stripos($html, '<p>') === 0) {
        // Usamos DOMDocument para manipular el HTML
        $dom = new DOMDocument();
        // Agregamos encabezado para manejar codificación
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $ps = $dom->getElementsByTagName('p');
        if ($ps->length > 0) {
            // Removemos el primer elemento <p>
            $firstP = $ps->item(0);
            $firstP->parentNode->removeChild($firstP);
            $html = $dom->saveHTML();
        }
    }
    return $html;
}

// Procesar los campos que contienen HTML de Quill
$comentario_html = isset($evento['comentario']) ? removeLeadingP($evento['comentario']) : '';
$lineamientos_html = isset($evento['lineamientos']) ? removeLeadingP($evento['lineamientos']) : '';

// Contar el número de personas inscritas en el campo asistencia
$asistencia = $evento['asistencia'] ? json_decode($evento['asistencia'], true) : [];
if (!is_array($asistencia)) {
    $asistencia = [];
}
$num_inscritos = count($asistencia);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Evento</title>
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        nav {
            all: revert;
        }
        .evento-detalles .evento-header {
            padding: 40px 0 30px 0;
            margin-bottom: 30px;
        }
        .evento-detalles .campus-badge {
            padding: 5px 12px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            display: inline-block;
            margin-top: 8px;
        }
        .evento-detalles .campus-Norte {
            background-color: #4CAF50;
        }
        .evento-detalles .campus-Sur {
            background-color: #2196F3;
        }
        .evento-detalles .campus-Externo {
            background-color: #FF9800;
        }
        .evento-detalles .info-section {
            margin-bottom: 30px;
        }
        .evento-detalles .info-block {
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .evento-detalles .section-title {
            border-left: 5px solid #26a69a;
            padding-left: 15px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .evento-detalles .info-icon {
            margin-right: 10px;
            vertical-align: middle;
            color: #26a69a;
        }
        .evento-detalles .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .evento-detalles .info-item i {
            margin-right: 10px;
            color: #26a69a;
        }
        .evento-detalles .info-text {
            font-size: 16px;
        }
        .evento-detalles .evento-actions {
            margin-top: 40px;
            margin-bottom: 60px;
        }
        .evento-detalles .lineamientos-list li,
        .evento-detalles .direccion-text {
            margin-bottom: 10px;
        }
        .evento-detalles .card-panel {
            border-radius: 8px;
        }
        .evento-detalles .expositor-info {
            display: flex;
            align-items: center;
        }
        .evento-detalles .expositor-avatar {
            background-color: #26a69a;
            color: white;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 24px;
            margin-right: 15px;
        }
    </style>
</head>
<body>

<!-- Contenedor de detalles del evento -->
<div class="container evento-detalles">
    <!-- Encabezado del evento -->
    <div class="evento-header">
        <div class="row">
            <div class="col s12">
                <a href="index.php?view=mis_eventos" class="btn-flat waves-effect">
                    <i class="material-icons left">arrow_back</i>Volver a mis eventos
                </a>
                <h4 class="header teal-text"><?= htmlspecialchars($evento['nombre']) ?></h4>
                <div class="campus-badge campus-<?= htmlspecialchars($evento['campus']) ?>"><?= htmlspecialchars($evento['campus']) ?></div>
            </div>
        </div>
    </div>

    <!-- Información principal -->
    <div class="row">
        <div class="col s12 m8">
            <div class="info-section">
                <h5 class="section-title">Información General</h5>
                <div class="card-panel">
                    <div class="row">
                        <div class="col s12 m6">
                            <div class="info-item">
                                <i class="material-icons">event</i>
                                <div>
                                    <span class="grey-text">Fecha</span>
                                    <p class="info-text"><?= htmlspecialchars($evento['fecha']) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6">
                            <div class="info-item">
                                <i class="material-icons">access_time</i>
                                <div>
                                    <span class="grey-text">Horario</span>
                                    <p class="info-text"><?= htmlspecialchars($evento['hora_inicio']) ?> - <?= htmlspecialchars($evento['hora_fin']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m6">
                            <div class="info-item">
                                <i class="material-icons">place</i>
                                <div>
                                    <span class="grey-text">Lugar</span>
                                    <p class="info-text"><?= htmlspecialchars($evento['lugar']) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6">
                            <div class="info-item">
                                <i class="material-icons">group</i>
                                <div>
                                    <span class="grey-text">Capacidad</span>
                                    <p class="info-text"><?= htmlspecialchars($evento['capacidad']) ?> personas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lineamientos -->
            <div class="info-section">
                <h5 class="section-title">Lineamientos</h5>
                <div class="card-panel">
                    <?php echo nl2br($lineamientos_html); ?>
                </div>

            </div>

            <!-- Información Adicional (Comentarios) -->
            <div class="info-section">
                <h5 class="section-title">Información Adicional</h5>
                <div class="card-panel">
                    <?php echo nl2br($comentario_html); ?>
                </div>
            </div>
        </div>

        <!-- Sidebar con información complementaria -->
        <div class="col s12 m4">
            <div class="info-section">
                <h5 class="section-title">Expositor</h5>
                <div class="card-panel">
                    <div class="expositor-info">
                        <div class="expositor-avatar">
                            <span><?= strtoupper(substr($evento['expositor'], 0, 2)) ?></span>
                        </div>
                        <div>
                            <h6 class="teal-text text-darken-1 margin-bottom-0"><?= htmlspecialchars($evento['expositor']) ?></h6>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h5 class="section-title">Ubicación</h5>
                <div class="card-panel">
                    <p class="direccion-text">
                        <i class="tiny material-icons info-icon">location_on</i>
                        <?= htmlspecialchars($evento['direccion']) ?>
                    </p>

                </div>
            </div>

            <div class="info-section">
                <h5 class="section-title">Estadísticas</h5>
                <div class="card-panel">
                    <div class="info-item">
                        <i class="material-icons">people_outline</i>
                        <div>
                            <span class="grey-text">Registrados</span>
                            <p class="info-text"><?= $num_inscritos ?> / <?= htmlspecialchars($evento['capacidad']) ?></p>
                            <div class="progress">
                                <div class="determinate" style="width: <?= ($num_inscritos / $evento['capacidad']) * 100 ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>



