<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: index.php?view=login");
    exit();
}

// Obtener eventos desde la base de datos
$query_eventos = "SELECT * FROM evento ORDER BY fecha, hora_inicio";
$stmt_eventos = $pdo->query($query_eventos);
$eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-events-container">
    <h2>Gestión de Eventos</h2>

    <!-- Botón para agregar un nuevo evento -->
    <button class="agregar" onclick="mostrarFormulario()">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none" class="svg-icon"><g stroke-width="2" stroke-linecap="round" stroke="#fff"><rect y="5" x="4" width="16" rx="2" height="16"></rect><path d="m8 3v4"></path><path d="m16 3v4"></path><path d="m4 11h16"></path></g></svg>
        <span class="lable">Agregar Evento</span>
    </button>

    <!-- Formulario para agregar/editar un evento -->
    <div id="formulario-evento" style="display:none;">
        <h3 id="titulo-formulario">Nuevo Evento</h3>
        <form id="form-evento">
            <input type="hidden" name="id_evento">
            <label>Nombre:</label> <input type="text" name="nombre" required><br>
            <label>Tipo de Evento:</label>
            <select name="tipo_evento" required>
                <option value="">Seleccione un tipo</option>
                <option value="Taller">Taller</option>
                <option value="Exposición">Exposición</option>
                <option value="Competencia">Competencia</option>
                <option value="Oportunidad Laboral">Oportunidad Laboral</option>
            </select><br>

            <label>Capacidad:</label> <input type="number" name="capacidad" required min="1"><br>
            <label>Fecha:</label> <input type="date" name="fecha" required><br>
            <label>Hora Inicio:</label> <input type="time" name="hora_inicio" required><br>
            <label>Hora Fin:</label> <input type="time" name="hora_fin" required><br>
            <label>Campus:</label>
            <select name="campus" id="select-campus" required>
                <option value="">Seleccione un campus</option>
                <option value="Norte">Norte</option>
                <option value="Sur">Sur</option>
                <option value="Externo">Externo</option>
            </select><br>

            <!-- Selección de salón con opción 'Otro' -->
            <label>Lugar:</label>
            <select name="lugar" id="select-lugar">
                <!-- Opciones de salones se cargarán dinámicamente -->
            </select>
            <input type="text" name="lugar_otro" id="lugar_otro" style="display:none;" placeholder="Especificar otro lugar"><br>

            <label>Comentario:</label> <textarea name="comentario"></textarea><br>
            <label>Dirección:</label> <textarea name="direccion" required></textarea><br>
            <label>Lineamientos:</label> <textarea name="lineamientos" required></textarea><br>
            <label>Expositor:</label> <input type="text" name="expositor" required><br>
            <button type="submit">Guardar</button>
            <button type="button" onclick="ocultarFormulario()">Cancelar</button>
        </form>
    </div>

<!-- Lista de eventos -->
<h2>Eventos Existentes</h2>
</div>
<div class="container">
    <ul class="responsive-table">
        <li class="table-header">
            <div class="col col-1">Nombre</div>
            <div class="col col-2">Tipo</div>
            <div class="col col-3">Fecha</div>
            <div class="col col-4">Horario</div>
            <div class="col col-5">Lugar</div>
            <div class="col col-6">Campus</div>
            <div class="col col-7">Capacidad</div>
            <div class="col col-8">Acciones</div>
        </li>
        <?php foreach ($eventos as $evento): ?>
            <li class="table-row">
                <div class="col col-1" data-label="Nombre"><?= htmlspecialchars($evento['nombre']) ?></div>
                <div class="col col-2" data-label="Tipo"><?= $evento['tipo_evento'] ?></div>
                <div class="col col-3" data-label="Fecha"><?= $evento['fecha'] ?></div>
                <div class="col col-4" data-label="Horario"><?= $evento['hora_inicio'] . " - " . $evento['hora_fin'] ?></div>
                <div class="col col-5" data-label="Lugar"><?= htmlspecialchars($evento['lugar']) ?></div>
                <div class="col col-6" data-label="Campus"><?= htmlspecialchars($evento['campus']) ?></div>
                <div class="col col-7" data-label="Capacidad"><?= $evento['capacidad'] ?></div>
                <div class="col col-8" data-label="Acciones">
                    <button class="Btn" onclick="editarEvento(<?= $evento['id'] ?>)">Edit
                        <svg class="svg" viewBox="0 0 512 512">
                            <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                        </svg>

                    </button>

                    <button class="unique-button" onclick="eliminarEvento(<?= $evento['id'] ?>)">
                        <span class="text">Delete</span>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24">
                                <path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    let eventos = <?= json_encode($eventos) ?>;

    function mostrarFormulario() {
        document.getElementById("titulo-formulario").textContent = "Nuevo Evento";
        document.getElementById("form-evento").reset();
        document.querySelector("[name='id_evento']").value = "";
        document.getElementById("formulario-evento").style.display = "block";
        document.getElementById("select-lugar").style.display = "none"; // Hide by default
    }

    function ocultarFormulario() {
        document.getElementById("formulario-evento").style.display = "none";
    }

    document.getElementById("select-campus").addEventListener("change", function() {
        let campus = this.value;
        let selectLugar = document.getElementById("select-lugar");
        let otroInput = document.getElementById("lugar_otro");

        if (campus === "Externo") {
            otroInput.style.display = "block";
            otroInput.required = true;
            selectLugar.style.display = "none";
            selectLugar.required = false;
        } else {
            otroInput.style.display = "none";
            otroInput.required = false;
            selectLugar.style.display = "block";
            selectLugar.required = true;
            fetch(`actions/obtener_salones.php?campus=${campus}`)
                .then(response => response.json())
                .then(data => {
                    selectLugar.innerHTML = data.map(salon =>
                        `<option value="${salon.id_salon}">${salon.id_salon} - Capacidad ${salon.capacidad}</option>`
                    ).join('') + '<option value="otro">Otro</option>';
                })
                .catch(error => console.error("Error al obtener salones:", error));
        }
    });

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
        document.querySelector("[name='tipo_evento']").value = evento.tipo_evento;
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
        // Desplazarte suavemente hasta el formulario
        document.getElementById("formulario-evento").scrollIntoView({
            behavior: "smooth"
        });
    }


    function eliminarEvento(id) {
        if (!confirm("¿Seguro que deseas eliminar este evento?")) {
            return; // Si el usuario cancela, no hacemos nada
        }

        fetch("actions/eliminar_evento.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success);
                    location.reload(); // Recargamos la página para ver los cambios
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => console.error("Error al eliminar evento:", error));
    }

</script>