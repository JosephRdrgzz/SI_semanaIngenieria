<?php
session_start();
require_once __DIR__ . '/../models/alumno.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?view=login");
    exit();
}

$usuario = $_SESSION['usuario'];
$exp = $usuario['exp'];

// Obtener datos del alumno
$alumno = obtenerAlumno($exp);

if (!$alumno) {
    echo "Error: Alumno no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <script>
        function actualizarPerfil(event) {
            event.preventDefault();
            let formData = new FormData(document.getElementById("form-editar-perfil"));
            formData.append("exp", "<?= $exp ?>"); // Asegurar que el expediente se envía

            fetch("../actions/editar_perfil.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    let mensajeDiv = document.getElementById("mensaje");
                    if (data.success) {
                        mensajeDiv.innerHTML = `<p style="color: green;">${data.success}</p>`;
                    } else {
                        mensajeDiv.innerHTML = `<p style="color: red;">${data.error}</p>`;
                    }
                })
                .catch(error => console.error("Error en la actualización:", error));
        }
    </script>
</head>
<body>

<h2>Editar Perfil</h2>
<div id="mensaje"></div>

<form id="form-editar-perfil" onsubmit="actualizarPerfil(event)">
    <label>Nombre:</label>
    <input type="text" value="<?= htmlspecialchars($alumno['nombre']) ?>" disabled><br>

    <label>Programa:</label>
    <input type="text" value="<?= htmlspecialchars($alumno['idprograma']) ?>" disabled><br>

    <label>Semestre:</label>
    <input type="text" value="<?= htmlspecialchars($alumno['semestre']) ?>" disabled><br>

    <label>Campus:</label>
    <select name="campus">
        <option value="Norte" <?= $alumno['campus'] === 'Norte' ? 'selected' : '' ?>>Norte</option>
        <option value="Sur" <?= $alumno['campus'] === 'Sur' ? 'selected' : '' ?>>Sur</option>
        <option value="Externo" <?= $alumno['campus'] === 'Externo' ? 'selected' : '' ?>>Externo</option>
    </select><br>

    <label>Correo:</label>
    <input type="email" name="mail" value="<?= htmlspecialchars($alumno['mail']) ?>" required><br>

    <label>Celular:</label>
    <input type="text" name="celular" value="<?= htmlspecialchars($alumno['celular']) ?>" required><br>

    <label>Responsable:</label>
    <input type="text" name="responsable" value="<?= htmlspecialchars($alumno['responsable']) ?>" required><br>

    <button type="submit">Guardar Cambios</button>
</form>

</body>
</html>
