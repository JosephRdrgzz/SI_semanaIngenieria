<?php
if ($_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: index.php?view=home");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <style>
        .admin-panel {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .admin-panel h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .admin-panel ul {
            list-style: none;
            padding: 0;
        }
        .admin-panel li {
            margin-bottom: 20px;
        }
        .admin-panel a {
            text-decoration: none;
            color: rgba(255, 103, 36, 0.97);
            font-weight: bold;
        }
        .admin-panel p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="admin-panel">
    <h2>Panel de Administración</h2>
    <p>Bienvenid@, <?= htmlspecialchars($_SESSION['nombre']) ?>.</p>

    <ul>
        <li>
            <a href="index.php?view=gestionar_eventos">Gestionar Eventos</a>
            <p>Aquí puedes crear, editar y eliminar eventos. Los eventos son actividades que se llevarán a cabo durante la Semana de Ingeniería, como talleres, exposiciones, competencias, etc.</p>
        </li>
        <li>
            <a href="index.php?view=gestionar_alumnos">Gestionar Alumnos</a>
            <p>En esta sección puedes administrar los alumnos del sistema. Puedes agregar nuevos usuarios, editar la información de los usuarios existentes y eliminar usuarios si es necesario.</p>
        </li>
        <li>
            <a href="index.php?view=gestionar_usuarios">Cargar Alumnos</a>
            <p>En esta sección puedes cargar los alumnos del sistema. Puedes agregar nuevos usuarios mediante un archivo de tipo csv.</p>
        </li>
        <li>
            <a href="index.php?view=editar_home">Editar Página de Inicio</a>
            <p>Utiliza esta opción para modificar el contenido de la página de inicio del sitio web. Puedes actualizar los textos y las imágenes que se muestran a los visitantes.</p>
        </li>
        <li>
            <a href="index.php?view=generalidades">Editar Generalidades</a>
            <p>Esta sección te permite editar la información general del evento, como la descripción, los objetivos y cualquier otra información relevante.</p>
        </li>
        <li>
            <a href="index.php?view=contacto">Editar Contacto</a>
            <p>Aquí puedes actualizar la información de contacto que se muestra en el sitio web, como direcciones de correo electrónico, números de teléfono y direcciones físicas.</p>
        </li>
    </ul>
</div>
</body>
</html>
