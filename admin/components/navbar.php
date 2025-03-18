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
            <a class="my-navbar-btn" href="index.php?view=panel_admin">INICIO</a>
            <a class="my-navbar-btn" href="index.php?view=generalidades">GENERALIDADES</a>
            <a class="my-navbar-btn" href="index.php?view=concursos">CONCURSOS</a>
            <a class="my-navbar-btn" href="index.php?view=contacto">CONTACTO</a>

            <?php if (isset($_SESSION['id_usuario'])): ?>
                <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
                    <a class="my-navbar-btn" href="index.php?view=gestionar_eventos">GESTIONAR EVENTOS</a>
                    <a class="my-navbar-btn" href="index.php?view=gestionar_usuarios">GESTIONAR USUARIOS</a>
                <?php else: ?>
                    <a class="my-navbar-btn" href="index.php?view=eventos">EVENTOS</a>
                    <a class="my-navbar-btn" href="index.php?view=mis_eventos">Mis Eventos</a>
                    <a class="my-navbar-btn" href="index.php?view=editar_perfil&exp=<?= $_SESSION['usuario']['exp'] ?>">
                        <?= $_SESSION['usuario']['nombre'] ?>
                    </a>
                <?php endif; ?>
                <a class="my-navbar-btn" href="index.php?action=logout">CERRAR SESIÓN</a>
            <?php else: ?>
                <a class="my-navbar-btn" href="index.php?view=login">INICIAR SESIÓN</a>
            <?php endif; ?>
        </nav>

        <!-- Botón hamburguesa (visible en móviles) -->
        <button class="my-hamburger" id="hamburger">
            &#9776;
        </button>
    </div>
</div>

<!-- OVERLAY (menú en móviles) -->
<div class="my-overlay" id="myOverlay">
    <button class="my-overlay-close" id="overlayClose">&times;</button>
    <nav class="my-overlay-menu">
        <a class="my-overlay-link" href="index.php?view=panel_admin">INICIO</a>
        <a class="my-overlay-link" href="index.php?view=generalidades">GENERALIDADES</a>
        <a class="my-overlay-link" href="index.php?view=concursos">CONCURSOS</a>
        <a class="my-overlay-link" href="index.php?view=contacto">CONTACTO</a>

        <?php if (isset($_SESSION['id_usuario'])): ?>
            <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
                <a class="my-overlay-link" href="index.php?view=gestionar_eventos">GESTIONAR EVENTOS</a>
                <a class="my-overlay-link" href="index.php?view=gestionar_usuarios">GESTIONAR USUARIOS</a>
            <?php else: ?>
                <a class="my-overlay-link" href="index.php?view=eventos">EVENTOS</a>
                <a class="my-overlay-link" href="index.php?view=mis_eventos">Mis Eventos</a>
                <a class="my-overlay-link" href="index.php?view=editar_perfil&exp=<?= $_SESSION['usuario']['exp'] ?>">
                    <?= $_SESSION['usuario']['nombre'] ?>
                </a>
            <?php endif; ?>
            <a class="my-overlay-link" href="index.php?action=logout">CERRAR SESIÓN</a>
        <?php else: ?>
            <a class="my-overlay-link" href="index.php?view=login">INICIAR SESIÓN</a>
        <?php endif; ?>
    </nav>
</div>

<!-- ✅ JS para hacer funcionar el menú hamburguesa -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const hamburger = document.getElementById("hamburger");
        const overlay = document.getElementById("myOverlay");
        const overlayClose = document.getElementById("overlayClose");

        hamburger.addEventListener("click", function () {
            overlay.classList.add("my-overlay--active");
        });

        overlayClose.addEventListener("click", function () {
            overlay.classList.remove("my-overlay--active");
        });

        document.querySelectorAll(".my-overlay-link").forEach(link => {
            link.addEventListener("click", function () {
                overlay.classList.remove("my-overlay--active");
            });
        });
    });
</script>
<style>

    /* ================================
    NAVBAR ESTILOS
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
    OVERLAY (Menú en móviles)
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
    }
    .my-overlay--active {
        transform: translateY(0);
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
        .my-navbar-container {
            justify-content: space-between;
        }

        .my-navbar-links {
            display: none;
        }

        .my-hamburger {
            display: block;
        }
    }
</style>