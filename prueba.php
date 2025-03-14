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



