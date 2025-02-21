<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: index.php?view=login");
    exit();
}

// Obtener eventos y salones desde la base de datos
$query_eventos = "SELECT * FROM evento ORDER BY fecha, hora_inicio";
$stmt_eventos = $pdo->query($query_eventos);
$eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);

$query_salones = "SELECT id_salon, capacidad FROM salones ORDER BY id_salon";
$stmt_salones = $pdo->query($query_salones);
$salones = $stmt_salones->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gestión de Eventos</h2>

<!-- Botón para agregar un nuevo evento -->
<button onclick="mostrarFormulario()">Agregar Nuevo Evento</button>

<!-- Formulario para agregar/editar un evento -->
<div id="formulario-evento" style="display:none;">
    <h3 id="titulo-formulario">Nuevo Evento</h3>
    <form id="form-evento">
        <input type="hidden" name="id_evento">
        <label>Nombre:</label> <input type="text" name="nombre" required><br>
        <label>Capacidad:</label> <input type="number" name="capacidad" required min="1"><br>
        <label>Fecha:</label> <input type="date" name="fecha" required><br>
        <label>Hora Inicio:</label> <input type="time" name="hora_inicio" required><br>
        <label>Hora Fin:</label> <input type="time" name="hora_fin" required><br>

        <!-- Selección de salón con opción 'Otro' -->
        <label>Lugar:</label>
        <select name="lugar" id="select-lugar">
            <?php foreach ($salones as $salon): ?>
                <option value="<?= htmlspecialchars($salon['id_salon']) ?>">
                    <?= htmlspecialchars($salon['id_salon']) ?> - Capacidad <?= htmlspecialchars($salon['capacidad']) ?>
                </option>
            <?php endforeach; ?>
            <option value="otro">Otro</option>
        </select>
        <input type="text" name="lugar_otro" id="lugar_otro" style="display:none;" placeholder="Especificar otro lugar"><br>

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
    let eventos = <?= json_encode($eventos) ?>;

    function mostrarFormulario() {
        document.getElementById("titulo-formulario").textContent = "Nuevo Evento";
        document.getElementById("form-evento").reset();
        document.querySelector("[name='id_evento']").value = "";
        document.getElementById("formulario-evento").style.display = "block";
    }

    function ocultarFormulario() {
        document.getElementById("formulario-evento").style.display = "none";
    }

    document.getElementById("select-lugar").addEventListener("change", function() {
        let otroInput = document.getElementById("lugar_otro");
        otroInput.style.display = (this.value === "otro") ? "block" : "none";
        otroInput.required = (this.value === "otro");
    });

    document.getElementById("form-evento").addEventListener("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let id_evento = formData.get("id_evento");
        let url = id_evento ? "actions/editar_evento.php" : "actions/agregar_evento.php";

        fetch(url, {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.debug_query) {
                    alert("Consulta ejecutada:\n" + data.debug_query);
                }

                if (data.success) {
                    alert(data.success);
                    location.reload();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => console.error("Error en fetch:", error));
    });

    function editarEvento(id) {
        let evento = eventos.find(e => e.id == id);

        if (!evento) {
            alert("Error: No se encontró el evento.");
            return;
        }

        document.getElementById("titulo-formulario").textContent = "Editar Evento";
        document.querySelector("[name='id_evento']").value = id;
        document.querySelector("[name='nombre']").value = evento.nombre;
        document.querySelector("[name='capacidad']").value = evento.capacidad;
        document.querySelector("[name='fecha']").value = evento.fecha;
        document.querySelector("[name='hora_inicio']").value = evento.hora_inicio;
        document.querySelector("[name='hora_fin']").value = evento.hora_fin;
        document.querySelector("[name='campus']").value = evento.campus;
        document.querySelector("[name='comentario']").value = evento.comentario;
        document.querySelector("[name='direccion']").value = evento.direccion;
        document.querySelector("[name='lineamientos']").value = evento.lineamientos;
        document.querySelector("[name='expositor']").value = evento.expositor;

        let selectLugar = document.querySelector("[name='lugar']");
        if (selectLugar.querySelector(`option[value='${evento.lugar}']`)) {
            selectLugar.value = evento.lugar;
            document.getElementById("lugar_otro").style.display = "none";
        } else {
            selectLugar.value = "otro";
            document.getElementById("lugar_otro").style.display = "block";
            document.getElementById("lugar_otro").value = evento.lugar;
        }

        document.getElementById("formulario-evento").style.display = "block";
    }
</script>
