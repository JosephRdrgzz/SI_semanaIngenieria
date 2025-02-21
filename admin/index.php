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

    case 'panel_admin': // Agregar la vista del panel de administrador
        if ($_SESSION['tipo_usuario'] === 'admin') {
            require 'views/panel_admin.php';
        } else {
            header("Location: index.php?view=home");
            exit();
        }
        break;

    case 'gestionar_eventos': // Agregar la vista de gestión de eventos
        if ($_SESSION['tipo_usuario'] === 'admin') {
            require 'views/gestionar_eventos.php';
        } else {
            header("Location: index.php?view=home");
            exit();
        }
        break;

    default:
        require "views/$view.php";
        break;
}

// Incluir el footer solo una vez
require __DIR__ . '/components/footer.php';
?>
