<?php
session_start();
require_once __DIR__ . '/config/conexion.php';

// Cerrar sesión si se accede a "logout"
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php?view=login");
    exit();
}

// Determinar la vista a mostrar desde la URL
$view = isset($_GET['view']) ? $_GET['view'] : 'home';

// Si el usuario está logueado y su perfil no está completo, redirigir a "completar_perfil"
// Permitimos que se acceda a "login" y "completar_perfil" para que el usuario pueda iniciar sesión y completar su perfil.
if (isset($_SESSION['id_usuario']) && (!isset($_SESSION['perfil_completo']) || $_SESSION['perfil_completo'] === false)) {
    if (!in_array($view, ['login', 'completar_perfil'])) {
        header("Location: index.php?view=completar_perfil");
        exit();
    }
}

// Incluir el header y el navbar una sola vez
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';

switch ($view) {
    case 'eventos':
        if (!isset($_SESSION['id_usuario'])) {
            require 'views/login.php';
        } else {
            require 'views/eventos.php';
        }
        break;

    case 'login':
        require 'views/login.php';
        break;

    case 'completar_perfil':
        require 'views/completar_perfil.php';
        break;

    default:
        require "views/$view.php";
        break;
}

// El floating button
require __DIR__ . '/components/floating.php';

// Incluir el footer solo una vez
require __DIR__ . '/components/footer.php';
?>

