<?php
// Asegúrate de que la sesión esté iniciada si usas la lógica de $_SESSION
// session_start(); // Normalmente ya se hace en index.php
?>
<div class="my-navbar">
    <div class="my-navbar-container">
        <!-- LOGO (ajusta a tu gusto) -->
        <div class="my-navbar-logo">
            <a href="index.php?view=home">
                <img src="https://merida.anahuac.mx/hubfs/Anahuac%20A_Mesa%20de%20trabajo%201.png" alt="Mi Logo">
            </a>
        </div>

        <!-- Menú horizontal (visible en pantallas grandes) -->
        <nav class="my-navbar-links" id="navbarLinks">
            <a class="my-navbar-btn" href="index.php?view=home">INICIO</a>
            <a class="my-navbar-btn" href="index.php?view=generalidades">GENERALIDADES</a>
            <a class="my-navbar-btn" href="index.php?view=contacto">CONTACTO</a>

            <?php if (isset($_SESSION['id_usuario'])): ?>
                <a class="my-navbar-btn" href="index.php?view=eventos">EVENTOS</a>
                <a class="my-navbar-btn" href="index.php?view=mis_eventos">Mis Eventos</a>
                <a class="my-navbar-btn" href="index.php?view=editar_perfil&exp=<?= $_SESSION['usuario']['exp'] ?>">
                    <?= $_SESSION['usuario']['nombre'] ?>
                </a>
                <a class="my-navbar-btn" href="index.php?action=logout">CERRAR SESIÓN</a>
            <?php else: ?>
                <a class="my-navbar-btn" href="index.php?view=login">INICIAR SESIÓN</a>
            <?php endif; ?>
        </nav>

        <!-- Botón hamburguesa (visible en pantallas pequeñas) -->
        <button class="my-hamburger" id="hamburger">
            &#9776;
        </button>
    </div>
</div>

<!-- OVERLAY que cubre la pantalla en móviles -->
<div class="my-overlay" id="myOverlay">
    <!-- Botón para cerrar el menú -->
    <button class="my-overlay-close" id="overlayClose">
        &times;
    </button>

    <!-- Enlaces dentro del overlay -->
    <nav class="my-overlay-menu">
        <a class="my-overlay-link" href="index.php?view=home">INICIO</a>
        <a class="my-overlay-link" href="index.php?view=generalidades">GENERALIDADES</a>
        <a class="my-overlay-link" href="index.php?view=contacto">CONTACTO</a>

        <?php if (isset($_SESSION['id_usuario'])): ?>
            <a class="my-overlay-link" href="index.php?view=eventos">EVENTOS</a>
            <a class="my-overlay-link" href="index.php?view=mis_eventos">Mis Eventos</a>
            <a class="my-overlay-link" href="index.php?view=editar_perfil&exp=<?= $_SESSION['usuario']['exp'] ?>">
                <?= $_SESSION['usuario']['nombre'] ?>
            </a>
            <a class="my-overlay-link" href="index.php?action=logout">CERRAR SESIÓN</a>
        <?php else: ?>
            <a class="my-overlay-link" href="index.php?view=login">INICIAR SESIÓN</a>
        <?php endif; ?>
    </nav>
</div>

<!-- CSS incrustado en el mismo archivo (navbar.php) -->
<style>
    /* IMPORTAMOS LA FUENTE LATO */
    @import url('https://fonts.googleapis.com/css?family=Lato:400,700&display=swap');

    /* ================================
       NAVBAR ESTILOS
       ================================ */

    /* Ajuste base de la navbar */
    .my-navbar {
        background-color: #fff;
        border-bottom: 1px solid #ccc;
    }

    .my-navbar-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0.5rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Logo */
    .my-navbar-logo img {
        height: 40px;
    }

    /* Menú horizontal (pantallas grandes) */
    .my-navbar-links {
        display: flex;
        gap: 1rem;
    }

    .my-navbar-btn {
        text-decoration: none;
        color: #333;
        font-weight: 600;
        transition: color 0.2s;
    }

    .my-navbar-btn:hover {
        color: #666;
    }

    /* Botón hamburguesa (oculto en pantallas grandes) */
    .my-hamburger {
        display: none;
        background: none;
        border: none;
        font-size: 1.8rem;
        cursor: pointer;
    }

    /* ================================
       OVERLAY ESTILOS (MÓVILES)
       ================================ */
    .my-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        z-index: 9999;
        transform: translateY(-100%);
        transition: transform 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 2rem;

        /* Para que no se corte CERRAR SESIÓN cuando haya muchas opciones */
        overflow-y: auto;
    }

    /* Clase que se agrega al abrir overlay */
    .my-overlay.my-overlay--active {
        transform: translateY(0);
    }

    /* Botón para cerrar overlay (arriba derecha) */
    .my-overlay-close {
        background: none;
        border: none;
        font-size: 2rem;
        margin-left: auto;
        margin-right: 1rem;
        cursor: pointer;
        align-self: flex-start;
    }

    /* Menú vertical dentro del overlay */
    .my-overlay-menu {
        margin-top: 2rem;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1rem; /* reduce de 1.5rem a 1rem para compactar */
    }

    .my-overlay-link {
        text-decoration: none;
        color: #333;
        font-size: 1.2rem;       /* reduce de 1.4rem a 1.2rem */
        font-weight: 600;
        font-family: 'Lato', sans-serif; /* usar la fuente Lato */
    }

    /* ================================
       RESPONSIVE
       ================================ */
    @media (max-width: 768px) {
        /* Ocultar el menú horizontal y mostrar el botón hamburguesa */
        .my-navbar-links {
            display: none;
        }
        .my-hamburger {
            display: block;
        }

        /* Ajuste de la navbar en pantallas chicas */
        .my-navbar-container {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }
</style>
