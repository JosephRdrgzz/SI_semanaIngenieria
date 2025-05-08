
<?php
// session_start(); // Normalmente ya se hace en index.php
?>
<div class="my-navbar">
    <div class="my-navbar-container">
        <!-- LOGO -->
        <div class="my-navbar-logo">
            <a href="index.php?view=panel_admin">
                <img src="https://merida.anahuac.mx/hubfs/Anahuac%20A_Mesa%20de%20trabajo%201.png" alt="Mi Logo">
            </a>
        </div>

        <!-- Menú horizontal (pantallas grandes) -->
        <nav class="my-navbar-links" id="navbarLinks">
            <?php if(isset($_SESSION['id_usuario']) && $_SESSION['tipo_usuario']==='admin'): ?>
                <a class="my-navbar-btn" href="index.php?view=panel_admin">PANEL</a>

                <!-- PÁGINAS -->
                <div class="my-navbar-dropdown">
                    <button class="my-navbar-btn">PÁGINAS &#9662;</button>
                    <div class="my-navbar-dropdown-content">
                        <?php if(!empty($_SESSION['is_super']) || in_array('editar_home', $_SESSION['permisos'] ?? [], true)): ?>
                            <a href="index.php?view=editar_home">INICIO (EDITAR)</a>
                        <?php endif; ?>
                        <?php if(!empty($_SESSION['is_super']) || in_array('generalidades', $_SESSION['permisos'] ?? [], true)): ?>
                            <a href="index.php?view=generalidades">GENERALIDADES</a>
                        <?php endif; ?>
                        <?php if(!empty($_SESSION['is_super']) || in_array('contacto', $_SESSION['permisos'] ?? [], true)): ?>
                            <a href="index.php?view=contacto">CONTACTO</a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- GESTIÓN -->
                <div class="my-navbar-dropdown">
                    <button class="my-navbar-btn">GESTIÓN &#9662;</button>
                    <div class="my-navbar-dropdown-content">
                        <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_usuarios', $_SESSION['permisos'] ?? [], true)): ?>
                            <a href="index.php?view=gestionar_usuarios">CARGAR ALUMNOS</a>
                        <?php endif; ?>
                        <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_salones', $_SESSION['permisos'] ?? [], true)): ?>
                            <a href="index.php?view=gestionar_salones">GESTIONAR SALONES</a>
                        <?php endif; ?>
                        <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_alumnos', $_SESSION['permisos'] ?? [], true)): ?>
                            <a href="index.php?view=gestionar_alumnos">GESTIONAR ALUMNOS</a>
                        <?php endif; ?>
                        <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_eventos', $_SESSION['permisos'] ?? [], true)): ?>
                            <a href="index.php?view=gestionar_eventos">GESTIONAR EVENTOS</a>
                        <?php endif; ?>
                        <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_pasados', $_SESSION['permisos'] ?? [], true)): ?>
                            <a href="index.php?view=gestionar_pasados">GESTIONAR EVENTOS PASADOS</a>
                        <?php endif; ?>

                        <!-- Solo super-admin -->
                        <?php if(!empty($_SESSION['is_super'])): ?>
                            <a href="index.php?view=gestionar_administradores">GESTIONAR ADMINISTRADORES</a>
                        <?php endif; ?>
                    </div>
                </div>

                <a class="my-navbar-btn" href="index.php?action=logout">CERRAR SESIÓN</a>

            <?php elseif(isset($_SESSION['id_usuario'])): ?>
                <!-- Usuario normal -->
                <a class="my-navbar-btn"
                   href="index.php?view=editar_perfil&exp=<?=htmlspecialchars($_SESSION['usuario']['exp'])?>">
                    <?=htmlspecialchars($_SESSION['usuario']['nombre'])?>
                </a>
                <a class="my-navbar-btn" href="index.php?action=logout">CERRAR SESIÓN</a>

            <?php else: ?>
                <!-- Invitado -->
                <a class="my-navbar-btn" href="index.php?view=login">INICIAR SESIÓN</a>
            <?php endif; ?>
        </nav>

        <!-- Botón hamburguesa (móviles) -->
        <button class="my-hamburger" id="hamburger">&#9776;</button>
    </div>
</div>
<!-- OVERLAY (menú para móviles) -->
<div class="my-overlay" id="myOverlay">
    <button class="my-overlay-close" id="overlayClose">&times;</button>
    <nav class="my-overlay-menu">
        <?php if(isset($_SESSION['id_usuario']) && $_SESSION['tipo_usuario']==='admin'): ?>
            <a class="my-overlay-link" href="index.php?view=panel_admin">PANEL</a>
            <?php if(!empty($_SESSION['is_super']) || in_array('editar_home', $_SESSION['permisos'] ?? [], true)): ?>
                <a class="my-overlay-link" href="index.php?view=editar_home">INICIO (EDITAR)</a>
            <?php endif; ?>
            <?php if(!empty($_SESSION['is_super']) || in_array('generalidades', $_SESSION['permisos'] ?? [], true)): ?>
                <a class="my-overlay-link" href="index.php?view=generalidades">GENERALIDADES</a>
            <?php endif; ?>
            <?php if(!empty($_SESSION['is_super']) || in_array('contacto', $_SESSION['permisos'] ?? [], true)): ?>
                <a class="my-overlay-link" href="index.php?view=contacto">CONTACTO</a>
            <?php endif; ?>

            <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_eventos', $_SESSION['permisos'] ?? [], true)): ?>
                <a class="my-overlay-link" href="index.php?view=gestionar_eventos">GESTIONAR EVENTOS</a>
            <?php endif; ?>
            <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_pasados', $_SESSION['permisos'] ?? [], true)): ?>
                <a class="my-overlay-link" href="index.php?view=gestionar_pasados">GESTIONAR PASADOS</a>
            <?php endif; ?>
            <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_salones', $_SESSION['permisos'] ?? [], true)): ?>
                <a class="my-overlay-link" href="index.php?view=gestionar_salones">GESTIONAR SALONES</a>
            <?php endif; ?>
            <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_alumnos', $_SESSION['permisos'] ?? [], true)): ?>
                <a class="my-overlay-link" href="index.php?view=gestionar_alumnos">GESTIONAR ALUMNOS</a>
            <?php endif; ?>
            <?php if(!empty($_SESSION['is_super']) || in_array('gestionar_usuarios', $_SESSION['permisos'] ?? [], true)): ?>
                <a class="my-overlay-link" href="index.php?view=gestionar_usuarios">CARGAR ALUMNOS</a>
            <?php endif; ?>

            <?php if(!empty($_SESSION['is_super'])): ?>
                <a class="my-overlay-link" href="index.php?view=gestionar_administradores">
                    GESTIONAR ADMINISTRADORES
                </a>
            <?php endif; ?>

            <a class="my-overlay-link" href="index.php?action=logout">CERRAR SESIÓN</a>

        <?php elseif(isset($_SESSION['id_usuario'])): ?>
            <a class="my-overlay-link"
               href="index.php?view=editar_perfil&exp=<?=htmlspecialchars($_SESSION['usuario']['exp'])?>">
                <?=htmlspecialchars($_SESSION['usuario']['nombre'])?>
            </a>
            <a class="my-overlay-link" href="index.php?action=logout">CERRAR SESIÓN</a>

        <?php else: ?>
            <a class="my-overlay-link" href="index.php?view=login">INICIAR SESIÓN</a>
        <?php endif; ?>
    </nav>
</div>

<!-- JS para el menú hamburguesa -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hamburger = document.getElementById("hamburger");
        const overlay = document.getElementById("myOverlay");
        const overlayClose = document.getElementById("overlayClose");
        function checkOverlayVisibility() {
            // Si el botón hamburguesa está oculto => estamos en pantalla grande
            // (o en general, donde .my-navbar-links está visible)
            if (window.getComputedStyle(hamburger).display === "none") {
                // Fuerza a cerrar el overlay
                overlay.classList.remove("my-overlay--active");
            }
        }

        // Cada vez que la ventana cambie de tamaño, volvemos a verificar
        window.addEventListener("resize", checkOverlayVisibility);

        // Verificamos al cargar
        checkOverlayVisibility();

        hamburger.addEventListener("click", function () {
            overlay.classList.add("my-overlay--active");
        });

        overlayClose.addEventListener("click", function () {
            overlay.classList.remove("my-overlay--active");
        });

        // Cerrar overlay al hacer clic en un enlace
        document.querySelectorAll(".my-overlay-link").forEach(link => {
            link.addEventListener("click", function () {
                overlay.classList.remove("my-overlay--active");
            });
        });
    });
</script>

<!-- CSS incrustado en el mismo archivo -->
<style>
    /* ================================
       NAVBAR BASE
       ================================ */
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
    /* Menú horizontal */
    .my-navbar-links {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .my-navbar-btn {
        text-decoration: none;
        color: #333;
        font-weight: 600;
        white-space: nowrap; /* Evita que los textos largos se partan */
        padding: 0.5rem 1rem;
        transition: color 0.2s;
    }
    .my-navbar-btn:hover {
        color: #666;
    }
    /* Botón hamburguesa (móviles) */
    .my-hamburger {
        display: none;
        background: none;
        border: none;
        font-size: 1.8rem;
        cursor: pointer;
    }

    /* ================================
       SUBMENÚ DISCRETO
       ================================ */
    .my-navbar-dropdown {
        position: relative;
        display: inline-block; /* Se alinea con los demás enlaces */
    }
    /* Botón del submenú */
    .my-navbar-dropdown button {
        background: none;
        border: none;
        font: inherit;
        color: inherit;
        cursor: pointer;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }
    /* Contenedor del submenú */
    .my-navbar-dropdown-content {
        display: none;
        position: absolute;
        top: 100%; /* Debajo del item principal */
        left: 0;
        background-color: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        min-width: 160px; /* Ajusta el ancho del submenú */
        z-index: 999;
    }
    .my-navbar-dropdown-content a {
        display: block; /* Cada enlace en su propia línea */
        color: #333;
        text-decoration: none;
        padding: 0.5rem 1rem;
        font-size: 14px;
        font-weight: normal;
    }
    .my-navbar-dropdown-content a:hover {
        background-color: #f1f1f1;
        color: #000;
    }
    /* Mostrar submenú al hover */
    .my-navbar-dropdown:hover .my-navbar-dropdown-content {
        display: block;
    }

    /* ================================
       OVERLAY (Menú en móviles)
       ================================ */
    .my-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: #fff;
        z-index: 9999;
        transform: translateY(-300%);
        transition: transform 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
        padding-top: 2rem;
        pointer-events: none; /* No intercepta clics */
    }
    .my-overlay--active {
        transform: translateY(0);
        pointer-events: auto; /* Ahora sí intercepta clics */

    }
    .my-overlay-close {
        background: none;
        border: none;
        font-size: 2rem;
        margin-left: auto;
        margin-right: 1rem;
        cursor: pointer;
        align-self: flex-start;
    }
    .my-overlay-menu {
        margin-top: 2rem;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .my-overlay-link {
        text-decoration: none;
        color: #333;
        font-size: 1.4rem;
        font-weight: 600;
    }

    /* ================================
       RESPONSIVE
       ================================ */
    @media (max-width: 1024px) {
        .my-navbar-links {
            display: none !important; /* Oculta el menú horizontal en pantallas pequeñas */
        }
        .my-hamburger {
            display: block; /* Muestra el botón hamburguesa */
        }
    }
    /* Contenedor del menú horizontal */
    .my-navbar-links {
        display: flex;
        align-items: center;  /* Alinea verticalmente todos los ítems */
        gap: 1rem;
    }

    /* Cada enlace (a) y el contenedor del submenú deben tener display similar */
    .my-navbar-links > a,
    .my-navbar-dropdown {
        display: inline-flex;    /* o inline-block */
        align-items: center;
    }

    /* Botón del submenú */
    .my-navbar-dropdown button {
        display: inline-flex;    /* Se comporta como los enlaces */
        align-items: center;
        margin: 0;               /* Quitar márgenes por defecto */
        padding: 0.5rem 1rem;
        border: none;
        background: none;
        font: inherit;           /* Mismo tamaño de letra que los enlaces */
        color: #333;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;     /* Evita quiebres de línea */
    }

    /* Asegura que los enlaces y el botón tengan la misma altura */
    .my-navbar-btn,
    .my-navbar-dropdown button {
        line-height: 1.5;        /* Ajusta a tu gusto */
    }

    /* Submenú en sí */
    .my-navbar-dropdown-content {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        min-width: 160px;
        z-index: 999;
    }
    .my-navbar-dropdown-content a {
        display: block;
        padding: 0.5rem 1rem;
        text-decoration: none;
        color: #333;
        font-size: 14px;
    }

    /* Mostrar submenú al hover */
    .my-navbar-dropdown:hover .my-navbar-dropdown-content {
        display: block;
    }

</style>



