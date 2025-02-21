<div align="center">
    <a href="index.php?view=home"> INICIO </a>
    <a href="index.php?view=generalidades"> GENERALIDADES </a>
    <a href="index.php?view=concursos"> CONCURSOS </a>
    <a href="index.php?view=contacto"> CONTACTO </a>

    <?php if (isset($_SESSION['id_usuario'])): ?>
        <a href="index.php?view=eventos"> EVENTOS </a>
        <a href="index.php?view=mis_eventos">Mis Eventos</a>
        <a href="index.php?view=logout">(<?= $_SESSION['nombre'] ?? 'Invitado' ?>)</a>
        <a href="index.php?action=logout"> CERRAR SESIÓN </a>
    <?php else: ?>
        <a href="index.php?view=login"> INICIAR SESIÓN </a>
    <?php endif; ?>
    <!-- Mostrar Panel de Administración solo si el usuario es admin -->
    <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
        <a href="index.php?view=panel_admin">Panel de Administración</a>
    <?php endif; ?>
    <pre><?php //var_dump($_SESSION); ?></pre>

</div>
