<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: index.php?view=home");
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

<!-- Formulario para agregar un evento -->
<div id="formulario-evento" style="display:none;">
    <h3>Nuevo Evento</h3>
    <form id="form-agregar-evento">
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
        document.getElementById("formulario-evento").style.display = "block";
    }

    function ocultarFormulario() {
        document.getElementById("formulario-evento").style.display = "none";
    }

    // Manejo de selección de 'Otro' en Lugar
    document.getElementById("select-lugar").addEventListener("change", function() {
        let otroInput = document.getElementById("lugar_otro");
        if (this.value === "otro") {
            otroInput.style.display = "block";
            otroInput.required = true;
        } else {
            otroInput.style.display = "none";
            otroInput.required = false;
        }
    });

    // Manejo de envío del formulario
    document.getElementById("form-agregar-evento").addEventListener("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        fetch("actions/agregar_evento.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Evento agregado correctamente.");
                    location.reload();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => console.error("Error en fetch:", error));
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

    document.getElementById("select-lugar").addEventListener("change", function() {
        let otroInput = document.getElementById("lugar_otro");
        if (this.value === "otro") {
            otroInput.style.display = "block";
            otroInput.required = true;
        } else {
            otroInput.style.display = "none";
            otroInput.required = false;
        }
    });

    function editarEvento(id) {
        let evento = eventos.find(e => e.id == id);

        if (!evento) {
            alert("Error: No se encontró el evento.");
            return;
        }

        let form = document.getElementById("form-agregar-evento");
        form.dataset.id = id;  // Guardar el ID en el formulario

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

        // Manejo del lugar
        let selectLugar = document.querySelector("[name='lugar']");
        if (evento.lugar !== "otro") {
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
