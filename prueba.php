<?php
$passIngresada = "admin456";  // Usa la contraseña que estás ingresando en el login
$hashGenerado = hash('sha256', $passIngresada);

echo "Hash generado para 'admin456': " . $hashGenerado;
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Campus</title>
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .card-title {
            padding: 20px 20px 20px 20px !important;
            font-weight: bold !important;
            font-size: 1.3rem !important;
            max-width: 70%; /* Dejar espacio para la etiqueta de campus */
            line-height: 1.4;
        }
        .chip {
            margin: 3px;
            font-weight: 500;
        }
        .card-detail {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .card-detail i {
            margin-right: 8px;
            color: #E65100; /* Naranja oscuro */
        }
        .campus-norte {
            background-color: #4CAF50 !important;
        }
        .campus-sur {
            background-color: #2196F3 !important;
        }
        .campus-externo {
            background-color: #FF9800 !important;
        }
        .evento-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
        }
        .card-header {
            position: relative;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            min-height: 80px; /* Altura mínima para garantizar espacio suficiente */
            display: flex;
            align-items: flex-start;
        }
        .card-content {
            padding-top: 20px !important;
        }
        .card-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px !important;
        }
        .btn-registrarse {
            margin-left: 10px;
        }
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .row .col {
            margin-bottom: 20px;
        }
        .detalles-link {
            flex-grow: 1;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <!-- Evento 1 -->
        <div class="col s12 m6 l4">
            <div class="card hoverable">
                <div class="card-header">
                    <span class="card-title">Conferencia de Innovación Digital</span>
                    <div class="evento-badge campus-norte">Norte</div>
                </div>
                <div class="card-content">
                    <div class="card-detail">
                        <i class="material-icons">group</i>
                        <span>Capacidad: 120 personas</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">event</i>
                        <span>Fecha: 15 de marzo, 2025</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">access_time</i>
                        <span>Horario: 14:00 - 17:30</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">place</i>
                        <span>Lugar: Auditorio Principal</span>
                    </div>
                </div>
                <div class="card-action">
                    <a href="#" class="teal-text detalles-link">Detalles</a>
                    <a href="#" class="btn waves-effect waves-light amber darken-3 btn-registrarse">Registrarse</a>
                </div>
            </div>
        </div>

        <!-- Evento 2 -->
        <div class="col s12 m6 l4">
            <div class="card hoverable">
                <div class="card-header">
                    <span class="card-title">Taller de Emprendimiento Social</span>
                    <div class="evento-badge campus-sur">Sur</div>
                </div>
                <div class="card-content">
                    <div class="card-detail">
                        <i class="material-icons">group</i>
                        <span>Capacidad: 45 personas</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">event</i>
                        <span>Fecha: 18 de marzo, 2025</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">access_time</i>
                        <span>Horario: 10:00 - 13:00</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">place</i>
                        <span>Lugar: Sala de Conferencias B</span>
                    </div>
                </div>
                <div class="card-action">
                    <a href="#" class="teal-text detalles-link">Detalles</a>
                    <a href="#" class="btn waves-effect waves-light amber darken-3 btn-registrarse">Registrarse</a>
                </div>
            </div>
        </div>

        <!-- Evento 3 -->
        <div class="col s12 m6 l4">
            <div class="card hoverable">
                <div class="card-header">
                    <span class="card-title">Seminario de Ciencias Aplicadas</span>
                    <div class="evento-badge campus-externo">Externo</div>
                </div>
                <div class="card-content">
                    <div class="card-detail">
                        <i class="material-icons">group</i>
                        <span>Capacidad: 80 personas</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">event</i>
                        <span>Fecha: 22 de marzo, 2025</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">access_time</i>
                        <span>Horario: 09:00 - 14:00</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">place</i>
                        <span>Lugar: Centro de Convenciones</span>
                    </div>
                </div>
                <div class="card-action">
                    <a href="#" class="teal-text detalles-link">Detalles</a>
                    <a href="#" class="btn waves-effect waves-light amber darken-3 btn-registrarse">Registrarse</a>
                </div>
            </div>
        </div>

        <!-- Evento 4 -->
        <div class="col s12 m6 l4">
            <div class="card hoverable">
                <div class="card-header">
                    <span class="card-title">Festival Cultural Universitario</span>
                    <div class="evento-badge campus-norte">Norte</div>
                </div>
                <div class="card-content">
                    <div class="card-detail">
                        <i class="material-icons">group</i>
                        <span>Capacidad: 300 personas</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">event</i>
                        <span>Fecha: 25 de marzo, 2025</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">access_time</i>
                        <span>Horario: 16:00 - 21:00</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">place</i>
                        <span>Lugar: Plaza Central</span>
                    </div>
                </div>
                <div class="card-action">
                    <a href="#" class="teal-text detalles-link">Detalles</a>
                    <a href="#" class="btn waves-effect waves-light amber darken-3 btn-registrarse">Registrarse</a>
                </div>
            </div>
        </div>

        <!-- Evento 5 -->
        <div class="col s12 m6 l4">
            <div class="card hoverable">
                <div class="card-header">
                    <span class="card-title">Hackathon: Soluciones Sustentables</span>
                    <div class="evento-badge campus-sur">Sur</div>
                </div>
                <div class="card-content">
                    <div class="card-detail">
                        <i class="material-icons">group</i>
                        <span>Capacidad: 50 equipos</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">event</i>
                        <span>Fecha: 29-30 de marzo, 2025</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">access_time</i>
                        <span>Horario: 08:00 - 20:00</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">place</i>
                        <span>Lugar: Edificio de Ingeniería</span>
                    </div>
                </div>
                <div class="card-action">
                    <a href="#" class="teal-text detalles-link">Detalles</a>
                    <a href="#" class="btn waves-effect waves-light amber darken-3 btn-registrarse">Registrarse</a>
                </div>
            </div>
        </div>

        <!-- Evento 6 -->
        <div class="col s12 m6 l4">
            <div class="card hoverable">
                <div class="card-header">
                    <span class="card-title">Conferencia Internacional de Medicina</span>
                    <div class="evento-badge campus-externo">Externo</div>
                </div>
                <div class="card-content">
                    <div class="card-detail">
                        <i class="material-icons">group</i>
                        <span>Capacidad: 200 personas</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">event</i>
                        <span>Fecha: 2 de abril, 2025</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">access_time</i>
                        <span>Horario: 09:00 - 18:00</span>
                    </div>
                    <div class="card-detail">
                        <i class="material-icons">place</i>
                        <span>Lugar: Hospital Universitario</span>
                    </div>
                </div>
                <div class="card-action">
                    <a href="#" class="teal-text detalles-link">Detalles</a>
                    <a href="#" class="btn waves-effect waves-light amber darken-3 btn-registrarse">Registrarse</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>

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
        .evento-header {
            padding: 40px 0 30px 0;
            margin-bottom: 30px;
        }
        .campus-badge {
            padding: 5px 12px;
            border-radius: 4px;
            color: white;
            font-weight: 500;
            display: inline-block;
            margin-top: 8px;
        }
        .campus-Norte {
            background-color: #4CAF50;
        }
        .campus-Sur {
            background-color: #2196F3;
        }
        .campus-Externo {
            background-color: #FF9800;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-block {
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .section-title {
            border-left: 5px solid #26a69a;
            padding-left: 15px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .info-icon {
            margin-right: 10px;
            vertical-align: middle;
            color: #26a69a;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .info-item i {
            margin-right: 10px;
            color: #26a69a;
        }
        .info-text {
            font-size: 16px;
        }
        .evento-actions {
            margin-top: 40px;
            margin-bottom: 60px;
        }
        .lineamientos-list li, .direccion-text {
            margin-bottom: 10px;
        }
        .card-panel {
            border-radius: 8px;
        }
        .expositor-info {
            display: flex;
            align-items: center;
        }
        .expositor-avatar {
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

<div class="container">
    <!-- Encabezado del evento -->
    <div class="evento-header">
        <div class="row">
            <div class="col s12">
                <a href="#" class="btn-flat waves-effect">
                    <i class="material-icons left">arrow_back</i>Volver a eventos
                </a>
                <h4 class="header teal-text">Congreso de Innovación en Educación 2025</h4>
                <div class="campus-badge campus-Norte">Campus Norte</div>
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
                                    <p class="info-text">15 de marzo, 2025</p>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6">
                            <div class="info-item">
                                <i class="material-icons">access_time</i>
                                <div>
                                    <span class="grey-text">Horario</span>
                                    <p class="info-text">09:00 - 17:30</p>
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
                                    <p class="info-text">Auditorio Principal</p>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m6">
                            <div class="info-item">
                                <i class="material-icons">group</i>
                                <div>
                                    <span class="grey-text">Capacidad</span>
                                    <p class="info-text">180 personas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción y lineamientos -->
            <div class="info-section">
                <h5 class="section-title">Lineamientos</h5>
                <div class="card-panel">
                    <ul class="lineamientos-list">
                        <li><i class="tiny material-icons info-icon">check_circle</i> Registro previo obligatorio a través de la plataforma institucional.</li>
                        <li><i class="tiny material-icons info-icon">check_circle</i> Presentar identificación oficial al ingresar.</li>
                        <li><i class="tiny material-icons info-icon">check_circle</i> Se otorgará constancia digital a quienes asistan a todas las sesiones.</li>
                        <li><i class="tiny material-icons info-icon">check_circle</i> Los materiales digitales estarán disponibles para descarga durante el evento.</li>
                        <li><i class="tiny material-icons info-icon">check_circle</i> Se prohíbe la grabación no autorizada de las ponencias.</li>
                    </ul>
                </div>
            </div>

            <!-- Comentarios -->
            <div class="info-section">
                <h5 class="section-title">Información Adicional</h5>
                <div class="card-panel">
                    <p>Este congreso busca fomentar la discusión sobre las nuevas tendencias en educación y tecnología aplicada. Contará con ponentes nacionales e internacionales y sesiones prácticas por la tarde.</p>
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
                            <span>DM</span>
                        </div>
                        <div>
                            <h6 class="teal-text text-darken-1 margin-bottom-0">Dr. Manuel Sánchez</h6>
                            <p class="grey-text">Departamento de Pedagogía</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h5 class="section-title">Ubicación</h5>
                <div class="card-panel">
                    <p class="direccion-text">
                        <i class="tiny material-icons info-icon">location_on</i>
                        Av. Universidad 1200, Col. Educativa, Edificio A, Piso 2.
                    </p>
                    <div class="center-align">
                        <div style="background-color: #e9e9e9; height: 180px; display: flex; align-items: center; justify-content: center;">
                            <i class="large material-icons grey-text text-lighten-1">map</i>
                        </div>
                        <a href="#" class="btn-flat teal-text waves-effect">Ver mapa completo</a>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h5 class="section-title">Estadísticas</h5>
                <div class="card-panel">
                    <div class="info-item">
                        <i class="material-icons">people_outline</i>
                        <div>
                            <span class="grey-text">Registrados</span>
                            <p class="info-text">132 / 180</p>
                            <div class="progress">
                                <div class="determinate" style="width: 73%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="row evento-actions">
        <div class="col s12 center-align">
            <a class="btn-large waves-effect waves-light teal" href="#">
                <i class="material-icons left">how_to_reg</i>
                Registrarse al Evento
            </a>
            <a class="btn-large waves-effect waves-light amber darken-2" href="#">
                <i class="material-icons left">share</i>
                Compartir Evento
            </a>
        </div>
    </div>
</div>

<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>

