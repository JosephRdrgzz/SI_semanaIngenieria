<?php
session_start();
require_once __DIR__ . '/config/conexion.php';

// 1) Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php?view=login");
    exit;
}

// 2) Helper de permisos
function puedeVer(string $vista): bool {
    // Súper-admin ve TODO
    if (!empty($_SESSION['is_super'])) {
        return true;
    }
    // Sólo admin “normales”
    if (empty($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        return false;
    }
    // Comprueba permiso puntual
    return in_array($vista, $_SESSION['permisos'] ?? [], true);
}

// 3) Determinar vista y cargar layout
$view = $_GET['view'] ?? 'home';
require __DIR__ . '/components/header.php';
require __DIR__ . '/components/navbar.php';

switch ($view) {

    // ** PANEL ADMIN ** — basta con ser admin (super o normal)
    case 'panel_admin':
        if ($_SESSION['tipo_usuario'] !== 'admin') {
            header("Location: index.php?view=home");
            exit;
        }
       
        require 'views/panel_admin.php';
        break;

    // -- VISTAS PROTEGIDAS --
    case 'editar_home':
        if (!puedeVer('editar_home')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/editar_home.php';
        break;

    case 'generalidades':
        if (!puedeVer('generalidades')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/generalidades.php';
        break;

    case 'contacto':
        if (!puedeVer('contacto')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/contacto.php';
        break;

    case 'gestionar_eventos':
        if (!puedeVer('gestionar_eventos')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/gestionar_eventos.php';
        break;

    case 'gestionar_pasados':
        if (!puedeVer('gestionar_pasados')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/gestionar_pasados.php';
        break;

    case 'gestionar_alumnos':
        if (!puedeVer('gestionar_alumnos')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/gestionar_alumnos.php';
        break;

    case 'gestionar_usuarios':
        if (!puedeVer('gestionar_usuarios')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/gestionar_usuarios.php';
        break;

    case 'gestionar_administradores':
        if (!puedeVer('gestionar_administradores')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/gestionar_administradores.php';
        break;

    case 'editar_salones':
        if (!puedeVer('editar_salones')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/editar_salones.php';
        break;

    case 'editar_permisos':
        if (!puedeVer('editar_permisos')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/editar_permisos.php';
	break;
case 'gestionar_salones':   
        if (!puedeVer('gestionar_salones')) {
            header("Location: index.php?view=home");
            exit;
        }
        require 'views/gestionar_salones.php';
        break;


    // -- VISTAS PÚBLICAS --
    case 'login':
    case 'home':
        require "views/{$view}.php";
        break;

    default:
        header("HTTP/1.0 404 Not Found");
        echo "Página no encontrada";
        break;
}

require __DIR__ . '/components/footer.php';
