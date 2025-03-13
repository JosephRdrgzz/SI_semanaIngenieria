<div class="nav">
    <div class="container">
        <button class="hamburger" id="hamburger">
            &#9776;
        </button>
        <div id="nav-links" class="nav-links">
            <a class="btn" href="index.php?view=home">INICIO</a>
            <a class="btn" href="index.php?view=generalidades">GENERALIDADES</a>
            <a class="btn" href="index.php?view=concursos">CONCURSOS</a>
            <a class="btn" href="index.php?view=contacto">CONTACTO</a>

            <?php if (isset($_SESSION['id_usuario'])): ?>
                <a class="btn" href="index.php?view=eventos">EVENTOS</a>
                <a class="btn" href="index.php?view=mis_eventos">Mis Eventos</a>
                <a class="btn" href="index.php?view=editar_perfil&exp=<?= $_SESSION['usuario']['exp'] ?>"><?= $_SESSION['usuario']['nombre'] ?></a>
                <a class="btn" href="index.php?action=logout">CERRAR SESIÓN</a>
            <?php else: ?>
                <a class="btn" href="index.php?view=login">INICIAR SESIÓN</a>
            <?php endif; ?>
        </div>
    </div>
</div>