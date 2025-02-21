<?php
if ($_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: index.php?view=home");
    exit();
}
?>

<h2>Panel de Administración</h2>
<p>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?>.</p>

<ul>
    <li><a href="index.php?view=gestionar_eventos">Gestionar Eventos</a></li>
    <li><a href="index.php?view=gestionar_usuarios">Gestionar Usuarios</a></li>
</ul>
