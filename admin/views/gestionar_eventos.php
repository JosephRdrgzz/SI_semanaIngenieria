<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none" class="svg-icon">
            <g stroke-width="2" stroke-linecap="round" stroke="#fff">
                <rect y="5" x="4" width="16" rx="2" height="16"></rect>
                <path d="m8 3v4"></path>
                <path d="m16 3v4"></path>
                <path d="m4 11h16"></path>
            </g>
        </svg>
        <span class="lable">Agregar Evento</span>
    </button>

    <!-- Formulario para agregar/editar un evento con estilo "login-box" -->
    <div class="login-box" id="formulario-evento" style="display: none;">
        <p id="titulo-formulario">Nuevo Evento</p> <!-- Cambia a "Editar Evento" en editarEvento() -->

        <form id="form-evento">
            <!-- ID oculto para edición -->
            <input type="hidden" name="id_evento">

            <!-- Agrupamos en filas de dos columnas con .two-column -->
            <div class="two-column">
                <!-- Nombre (con label flotante normal) -->
                <div class="user-box">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>

                <!-- Tipo de Evento (sin flotante) -->
                <div class="user-box no-floating select-box">
                    <label for="tipo_evento">Tipo de Evento</label>
                    <select name="tipo_evento" id="tipo_evento" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="Taller">Taller</option>
                        <option value="Exposición">Exposición</option>
                        <option value="Competencia">Competencia</option>
                        <option value="Oportunidad Laboral">Oportunidad Laboral</option>
                    </select>
                </div>
            </div>

            <div class="two-column">
                <!-- Capacidad (sin flotante) -->
                <div class="user-box no-floating">
                    <label for="capacidad">Capacidad</label>
                    <input type="number" name="capacidad" id="capacidad" min="1" required>
                </div>

                <!-- Fecha (sin flotante) -->
                <div class="user-box no-floating">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" required>
                </div>
            </div>

            <div class="two-column">
                <!-- Hora Inicio (sin flotante) -->
                <div class="user-box no-floating">
                    <label for="hora_inicio">Hora Inicio</label>
                    <input type="time" name="hora_inicio" id="hora_inicio" required>
                </div>
                <!-- Hora Fin (sin flotante) -->
                <div class="user-box no-floating">
                    <label for="hora_fin">Hora Fin</label>
                    <input type="time" name="hora_fin" id="hora_fin" required>
                </div>
            </div>

            <div class="two-column">
                <!-- Campus (sin flotante) -->
                <div class="user-box no-floating select-box">
                    <label for="select-campus">Campus</label>
                    <select name="campus" id="select-campus" required>
                        <option value="">Seleccione un campus</option>
                        <option value="Norte">Norte</option>
                        <option value="Sur">Sur</option>
                        <option value="Externo">Externo</option>
                    </select>
                </div>

                <!-- Lugar (sin flotante) -->
                <div class="user-box no-floating select-box" id="lugar-container">
                    <label for="select-lugar">Lugar</label>
                    <select name="lugar" id="select-lugar" style="display:none;"></select>
                    <input type="text" name="lugar_otro" id="lugar_otro" style="display:none;" placeholder="Especificar otro lugar">
                </div>
            </div>

            <!-- Comentario (sin flotante) -->
            <div class="user-box no-floating textarea-box">
                <label for="comentario">Comentario</label>
                <textarea name="comentario" id="comentario"></textarea>
            </div>

            <!-- Dirección (con flotante) -->
            <div class="user-box textarea-box">
                <textarea name="direccion" required></textarea>
                <label>Dirección</label>
            </div>

            <!-- Lineamientos (con flotante) -->
            <div class="user-box textarea-box">
                <textarea name="lineamientos" required></textarea>
                <label>Lineamientos</label>
            </div>

            <!-- Expositor (con flotante) -->
            <div class="user-box">
                <input type="text" name="expositor" required>
                <label>Expositor</label>
            </div>

            <!-- Botón Submit con animación de borde -->
            <button type="submit" class="styled-button">
                <span></span><span></span><span></span><span></span>
                Guardar
            </button>

            <button type="button" class="styled-button" onclick="ocultarFormulario()">
                <span></span><span></span><span></span><span></span>
                Cancelar
            </button>

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
                <div class="col col-2" data-label="Tipo"><?= htmlspecialchars($evento['tipo_evento']) ?></div>
                <div class="col col-3" data-label="Fecha"><?= htmlspecialchars($evento['fecha']) ?></div>
                <div class="col col-4" data-label="Horario"><?= htmlspecialchars($evento['hora_inicio'] . " - " . $evento['hora_fin']) ?></div>
                <div class="col col-5" data-label="Lugar"><?= htmlspecialchars($evento['lugar']) ?></div>
                <div class="col col-6" data-label="Campus"><?= htmlspecialchars($evento['campus']) ?></div>
                <div class="col col-7" data-label="Capacidad"><?= htmlspecialchars($evento['capacidad']) ?></div>
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
        document.getElementById("select-lugar").style.display = "none"; // Oculto por defecto
        document.getElementById("lugar_otro").style.display = "none";
    }

    function ocultarFormulario() {
        document.getElementById("formulario-evento").style.display = "none";
    }

    // Al cambiar el campus, cargamos salones o mostramos "otro"
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

            // CORREGIDO: uso de backticks
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

    // Al cambiar el lugar, si es "otro", mostramos input
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

    // Guardar evento (submit)
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

                if (data.success) {
                    alert(data.success);
                    location.reload();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => console.error("Error en fetch:", error));
    });

    // Editar evento
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

        // Mostrar formulario
        document.getElementById("formulario-evento").style.display = "block";

        // Campus externo o no
        let selectLugar = document.getElementById("select-lugar");
        let otroInput = document.getElementById("lugar_otro");

        if (evento.campus === "Externo") {
            selectLugar.style.display = "none";
            selectLugar.required = false;
            otroInput.style.display = "block";
            otroInput.required = true;
            otroInput.value = evento.lugar;
        } else {
            // Cargar salones
            fetch(`actions/obtener_salones.php?campus=${evento.campus}`)
                .then(response => response.json())
                .then(data => {
                    selectLugar.innerHTML = data.map(salon =>
                        `<option value="${salon.id_salon}">${salon.id_salon} - Capacidad ${salon.capacidad}</option>`
                    ).join('') + '<option value="otro">Otro</option>';

                    selectLugar.style.display = "block";
                    selectLugar.required = true;
                    otroInput.style.display = "none";
                    otroInput.required = false;

                    // Verificar si la opción (salón) está en la lista
                    if (selectLugar.querySelector(`option[value='${evento.lugar}']`)) {
                        selectLugar.value = evento.lugar;
                    } else {
                        // Caso "otro"
                        selectLugar.value = "otro";
                        otroInput.style.display = "block";
                        otroInput.required = true;
                        otroInput.value = evento.lugar;
                    }
                })
                .catch(error => console.error("Error al obtener salones:", error));
        }

        // Scroll suave hacia el formulario
        document.getElementById("formulario-evento").scrollIntoView({ behavior: "smooth" });
    }

    // Eliminar evento
    function eliminarEvento(id) {
        if (!confirm("¿Seguro que deseas eliminar este evento?")) {
            return;
        }
        fetch("actions/eliminar_evento.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success);
                    location.reload();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => console.error("Error al eliminar evento:", error));
    }

    document.addEventListener('DOMContentLoaded', initFloatingLabels);
</script>