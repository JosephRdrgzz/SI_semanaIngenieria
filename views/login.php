<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Si ya hay una sesión iniciada, redirigir al home
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php?view=home");
    exit();
}

$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Iconos de Google -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<!-- Agregas la clase "login-page" aquí -->
<body class="login-page">
<!-- Tus estilos dentro de la clase .login-page para que no afecten a navbar -->
<style>
    nav {
        all: unset; /* Elimina todos los estilos por defecto */
    }


    .login-page {
        margin-top: 7vh; /* O el espacio que necesites */
    }

    /* Ejemplo de sobreescritura: solo afecta elementos dentro de .login-page */
    .login-page form {
        border-radius: 4px !important;
        background-color: #424242; /* gris oscuro, por ejemplo */
        padding: 2rem;
        color: #fff;
    }

    /* Botón anaranjado dentro de .login-page */
    .login-page .orange {
        background-color: #FF5900 !important;
    }

    .login-page .sub {
        margin-top: 2rem;
    }
</style>

<!-- Contenido principal del login -->
<div class="container">
    <div class="row">
        <div class="col s12 center">
            <h2>Semana de La Ingeniería</h2>
        </div>
    </div>

    <div class="row">
        <!-- "grey darken-3" es de Materialize, pero solo se verá dentro de .login-page -->
        <form class="col m8 offset-m2 s12 grey darken-3 z-depth-1 white-text" method="POST" action="actions/login.php">
            <div class="row">
                <div class="col s12 center">
                    <h3>Iniciar Sesión</h3>
                </div>
            </div>
            <?php if (!empty($error)): ?>
                <div class="row">
                    <div class="col s12 center">
                        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="input-field col m8 offset-m2 s10 offset-s1">
                    <input placeholder="ID con ceros" id="exp" type="text" name="exp" class="white-text validate" required>
                    <label for="exp">Matrícula</label>
                </div>
            </div>

            <div class="row" id="password-container" style="display: none;">
                <div class="input-field col m8 offset-m2 s10 offset-s1">
                    <input placeholder="Ingresa tu password" id="password-field" type="password" name="password" class="white-text validate">
                    <label for="password-field">Contraseña</label>
                </div>
            </div>

            <div class="row">
                <div class="col offset-s1 s10 right-align">
                    <button class="sub orange btn waves-effect waves-light" type="submit" name="action">
                        Iniciar Sesión
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

</body>
</html>
