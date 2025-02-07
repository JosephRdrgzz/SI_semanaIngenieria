<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: index.php?view=home");
    exit();
}

// Obtener todos los eventos
$query = "SELECT * FROM evento ORDER BY fecha, hora_inicio";
$stmt = $pdo->query($query);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gestión de Eventos</h2>

<!-- Botón para agregar un nuevo evento -->
<button onclick="mostrarFormulario()">Agregar Nuevo Evento</button>

<!-- Formulario para agregar un evento (inicialmente oculto) -->
<div id="formulario-evento" style="display:none;">
    <h3>Nuevo Evento</h3>
    <form id="form-agregar-evento">
        <label>Nombre:</label> <input type="text" name="nombre" required><br>
        <label>Capacidad:</label> <input type="number" name="capacidad" required min="1"><br>
        <label>Fecha:</label> <input type="date" name="fecha" required><br>
        <label>Hora Inicio:</label> <input type="time" name="hora_inicio" required><br>
        <label>Hora Fin:</label> <input type="time" name="hora_fin" required><br>
        <label>Lugar:</label> <input type="text" name="lugar" required><br>
        <label>Campus:</label>
        <select name="campus" required>
            <option value="Norte">Norte</option>
            <option value="Sur">Sur</option>
            <option value="Externo">Externo</option>
        </select><br>
        <label>Comentario:</label> <textarea name="comentario"></textarea><br>
        <label>Dirección:</label> <textarea name="direccion" required></textarea><br>
        <label>Lineamientos:</label> <textarea name="lineamientos" required></textarea><br>
        <label>Expositor:</label> <input type="text" name="expositor" required><br>
        <button type="submit">Guardar</button>
        <button type="button" onclick="ocultarFormulario()">Cancelar</button>
    </form>
</div>

<!-- Lista de eventos -->
<h3>Eventos Existentes</h3>
<table border="1">
    <thead>
    <tr>
        <th>Nombre</th>
        <th>Fecha</th>
        <th>Horario</th>
        <th>Lugar</th>
        <th>Campus</th>
        <th>Capacidad</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($eventos as $evento): ?>
        <tr>
            <td><?= htmlspecialchars($evento['nombre']) ?></td>
            <td><?= $evento['fecha'] ?></td>
            <td><?= $evento['hora_inicio'] . " - " . $evento['hora_fin'] ?></td>
            <td><?= htmlspecialchars($evento['lugar']) ?></td>
            <td><?= htmlspecialchars($evento['campus']) ?></td>
            <td><?= $evento['capacidad'] ?></td>
            <td>
                <button onclick="editarEvento(<?= $evento['id'] ?>)">Editar</button>
                <button onclick="eliminarEvento(<?= $evento['id'] ?>)">Eliminar</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    function mostrarFormulario() {
        document.getElementById("formulario-evento").style.display = "block";
    }

    function ocultarFormulario() {
        document.getElementById("formulario-evento").style.display = "none";
    }

    document.getElementById("form-agregar-evento").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("actions/agregar_evento.php", {
            method: "POST",
            body: formData
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert("Evento agregado correctamente.");
                location.reload();
            } else {
                alert("Error: " + data.error);
            }
        });
    });

    function eliminarEvento(id) {
        if (!confirm("¿Seguro que deseas eliminar este evento?")) return;

        fetch("actions/eliminar_evento.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert("Evento eliminado.");
                location.reload();
            } else {
                alert("Error: " + data.error);
            }
        });
    }
</script>
