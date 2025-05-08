<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin' && $_SESSION['tipo_usuario']!=='super') {
    header("Location: index.php?view=login");
    exit();
}

// Obtener eventos desde la base de datos
$query_eventos = "
  SELECT
    id, nombre, tipo_evento, capacidad,
    fecha, hora_inicio, hora_fin,
    lugar, campus, comentario,
    direccion, lineamientos, expositor,
    imagen_path,
    (imagen_path IS NOT NULL AND imagen_path <> '') AS has_image
  FROM evento
  ORDER BY fecha, hora_inicio
";
$stmt_eventos = $pdo->query($query_eventos);
$eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);
// Consultar los tipos de evento existentes en el ENUM
$query_tipos = $pdo->query("SELECT unnest(enum_range(NULL::tipo_evento_enum)) AS tipo");
$tipos_evento = $query_tipos->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Quill Editor -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>

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
        <span class="lable">Agregar Nuevo Evento</span>
    </button>

    <!-- Filtros -->
    <div class="filters" style="margin-top:20px;">
        <label for="ordenar-fecha">Ordenar por fecha:</label>
        <button onclick="ordenarEventos('asc')">Más Cercano</button>
        <button onclick="ordenarEventos('desc')">Más Lejano</button>

        <label for="filtrar-campus" style="margin-left:20px;">Filtrar por campus:</label>
        <select id="filtrar-campus" onchange="aplicarFiltros()">
            <option value="">Todos</option>
            <option value="Norte">Norte</option>
            <option value="Sur">Sur</option>
            <option value="Externo">Externo</option>
        </select>

        <label for="filtrar-tipo" style="margin-left:20px;">Filtrar por tipo de evento:</label>
        <select id="filtrar-tipo" onchange="aplicarFiltros()">
            <option value="">Todos</option>
            <option value="Taller">Taller</option>
            <option value="Exposición">Exposición</option>
            <option value="Concurso">Concurso</option>
            <option value="Conferencia">Conferencia</option>
            <option value="Oportunidad Laboral">Oportunidad Laboral</option>
        </select>
        <button onclick="limpiarFiltros()">Limpiar Filtros</button>

    </div>


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
                <!-- Tipo de Evento (sin flotante) -->
                <div class="user-box no-floating select-box">
                    <label for="tipo_evento">Tipo de Evento</label>
                    <select name="tipo_evento" id="tipo_evento" required onchange="mostrarTipoEventoOtro(this.value)">
                        <option value="">Seleccione un tipo</option>
                        <?php foreach ($tipos_evento as $tipo): ?>
                            <option value="<?= htmlspecialchars($tipo['tipo']) ?>">
                                <?= htmlspecialchars($tipo['tipo']) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="otro">Otro</option>
                    </select>
                    <!-- Campo para especificar el nuevo tipo, oculto por defecto -->
                    <input type="text" name="tipo_evento_otro" id="tipo_evento_otro" placeholder="Especificar nuevo tipo" style="display:none;">
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

            <div class="two-column">
                <div class="user-box no-floating">
                    <label for="imagen">Imagen del Evento</label>
                    <input type="file" name="imagen" id="imagen" accept="image/*">
                </div>
                <div id="preview-imagen" class="user-box no-floating">
                    <em>No hay imagen asignada.</em>
                </div>
                <input type="hidden" name="remove_image" id="remove_image" value="0">
            </div>


            <!-- Comentario (con Quill) -->
            <div class="user-box no-floating textarea-box">
                <label for="comentario-editor">Comentario</label>
                <div id="comentario-editor" style="height: 150px;"></div>
                <input type="hidden" name="comentario" id="comentario">
            </div>

            <!-- Lineamientos (con Quill) -->
            <div class="user-box textarea-box">
                <label for="lineamientos-editor">Lineamientos</label>
                <div id="lineamientos-editor" style="height: 150px;"></div>
                <input type="hidden" name="lineamientos" id="lineamientos">
            </div>

            <!-- Dirección (con flotante) -->
            <div class="user-box textarea-box">
                <textarea name="direccion" required></textarea>
                <label>Dirección</label>
            </div>

            <!-- Expositor (con flotante) -->
            <div class="user-box">
                <input type="text" name="expositor" >
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
    <ul class="responsive-table" id="tabla-eventos">
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
            <?php
            // Se crea el objeto DateTime con zona fija CDMX (-06:00)
            $fechaHoraEvento = new DateTime($evento['fecha'] . ' ' . $evento['hora_inicio'] . ' -06:00');
            $ahora = new DateTime('now', new DateTimeZone('-06:00'));
            // Solo se muestran eventos que ya han iniciado o pasado
            if ($fechaHoraEvento < $ahora) {
                continue;
            }
            ?>
            <li class="table-row" data-fecha="<?= htmlspecialchars($evento['fecha']) ?>"
                data-hora_inicio="<?= htmlspecialchars($evento['hora_inicio']) ?>"
                data-campus="<?= htmlspecialchars($evento['campus']) ?>"
                data-tipo="<?= htmlspecialchars($evento['tipo_evento']) ?>">
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
    // Inicializar los editores Quill
    const quillComentario = new Quill('#comentario-editor', {
        theme: 'snow'
    });
    const quillLineamientos = new Quill('#lineamientos-editor', {
        theme: 'snow'
    });

    let eventos = <?= json_encode($eventos) ?>;

    // Función para aplicar todos los filtros: campus, tipo y hora actual
    function aplicarFiltros() {
        const filtroCampus = document.getElementById('filtrar-campus').value;
        const filtroTipo = document.getElementById('filtrar-tipo').value;
        const ahora = new Date();
        const selectCampus = document.getElementById("select-campus");
        const direccionInput = document.querySelector('[name="direccion"]');

        selectCampus.addEventListener("change", function () {
            const campus = selectCampus.value;
            if (campus === "Norte") {
                direccionInput.value = "Campus Norte\nAv. Universidad Anáhuac 46, Col. Lomas Anáhuac\nHuixquilucan, Estado de México,\nC.P. 52786. +52 (55) 5627 0210";
            } else if (campus === "Sur") {
                direccionInput.value = "Campus Sur\nAv. de los Tanques no. 865, Col. Torres de Potrero,\nCiudad de México, Alcaldía Álvaro Obregón,\nMéxico, C.P. 01840. +52 (55) 5628 8800";
            } else {
                direccionInput.value = "";
            }
        });
        document.querySelectorAll('#tabla-eventos .table-row').forEach(row => {
            const campus = row.getAttribute('data-campus');
            const tipo = row.getAttribute('data-tipo');
            const fechaEvento = row.getAttribute('data-fecha');
            const horaInicio = row.getAttribute('data-hora_inicio');
            const fechaHoraEvento = new Date(fechaEvento + 'T' + horaInicio);
            let mostrar = true;

            // Filtro por campus
            if (filtroCampus && campus !== filtroCampus) {
                mostrar = false;
            }
            // Filtro por tipo de evento
            if (filtroTipo && tipo !== filtroTipo) {
                mostrar = false;
            }
            // Filtro por hora actual (mostrar solo eventos futuros o en curso)
            if (fechaHoraEvento < ahora) {
                mostrar = false;
            }
            row.style.display = mostrar ? '' : 'none';
        });
    }

    // Función para ordenar eventos por fecha
    function ordenarEventos(orden = 'asc') {
        const container = document.getElementById('tabla-eventos');
        const rows = Array.from(container.querySelectorAll('.table-row'));
        rows.sort((a, b) => {
            const fechaA = new Date(a.getAttribute('data-fecha') + 'T' + a.getAttribute('data-hora_inicio'));
            const fechaB = new Date(b.getAttribute('data-fecha') + 'T' + b.getAttribute('data-hora_inicio'));
            return orden === 'asc' ? fechaA - fechaB : fechaB - fechaA;
        });
        // Remontar la lista de eventos
        rows.forEach(row => container.appendChild(row));
    }

    // Llamada inicial para aplicar todos los filtros al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        aplicarFiltros();
    });

    function mostrarFormulario() {
        document.getElementById("titulo-formulario").textContent = "Nuevo Evento";
        document.getElementById("form-evento").reset();
        document.querySelector("[name='id_evento']").value = "";
        document.getElementById("formulario-evento").style.display = "block";
        document.getElementById("select-lugar").style.display = "none"; // Oculto por defecto
        document.getElementById("lugar_otro").style.display = "none";
        // Limpiar campos de Quill
        quillComentario.setContents([]);
        quillLineamientos.setContents([]);
        // Insertar el texto por defecto al crear nuevo evento
        quillComentario.clipboard.dangerouslyPasteHTML('<p>Comentarios</p>');
        quillLineamientos.clipboard.dangerouslyPasteHTML('<p>Lineamientos</p>');
    }

    function ocultarFormulario() {
        document.getElementById("formulario-evento").style.display = "none";
    }

    // Al cambiar el campus en el formulario, cargamos salones o mostramos "otro"
    document.getElementById("select-campus").addEventListener("change", function() {
        let campus = this.value;
        let selectLugar = document.getElementById("select-lugar");
        let otroInput = document.getElementById("lugar_otro");

        if (campus === "Externo") {
            // Forzamos a que el select no tenga salones previos
            selectLugar.innerHTML = '<option value="otro" selected>Otro</option>';
            selectLugar.style.display = "none";
            selectLugar.required = false;

            // Mostramos y requerimos el input "lugar_otro"
            otroInput.style.display = "block";
            otroInput.required = true;
            otroInput.value = "";
        } else {
            // Ocultamos el input "lugar_otro"
            otroInput.style.display = "none";
            otroInput.required = false;
            otroInput.value = "";

            // Mostramos el select y lo marcamos como requerido
            selectLugar.style.display = "block";
            selectLugar.required = true;

            // Cargamos salones vía fetch
            fetch(`actions/obtener_salones.php?campus=${campus}`)
                .then(response => response.json())
                .then(data => {
                    let opciones = data.map(salon =>
                        `<option value="${salon.id_salon}">${salon.id_salon} - Capacidad ${salon.capacidad}</option>`
                    );
                    opciones.push('<option value="otro">Otro</option>');
                    selectLugar.innerHTML = opciones.join('');
                })
                .catch(error => console.error("Error al obtener salones:", error));
        }
    });

    // Al cambiar el lugar, si es "otro", mostramos el input
    document.getElementById("select-lugar").addEventListener("change", function() {
        let otroInput = document.getElementById("lugar_otro");
        if (this.value === "otro") {
            otroInput.style.display = "block";
            otroInput.required = true;
            otroInput.value = "";
        } else {
            otroInput.style.display = "none";
            otroInput.required = false;
            otroInput.value = "";
        }
    });

    // Guardar evento (submit)
    // Guardar evento (submit)
    document.getElementById("form-evento").addEventListener("submit", function(e) {
        e.preventDefault();
        document.getElementById('comentario').value = quillComentario.root.innerHTML;
        document.getElementById('lineamientos').value = quillLineamientos.root.innerHTML;
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
        const evento = eventos.find(e => e.id == id);
        if (!evento) {
            return alert("Error: No se encontró el evento.");
        }

        // 1) Título y campos básicos
        document.getElementById("titulo-formulario").textContent = "Editar Evento";
        document.querySelector("[name='id_evento']").value       = evento.id;
        document.querySelector("[name='nombre']").value         = evento.nombre;

        // Tipo de evento (+ campo “otro”)
        const selectTipo = document.getElementById("tipo_evento");
        const inputTipoOtro = document.getElementById("tipo_evento_otro");
        if (selectTipo.querySelector(`option[value="${evento.tipo_evento}"]`)) {
            selectTipo.value = evento.tipo_evento;
            inputTipoOtro.style.display = "none";
            inputTipoOtro.required = false;
        } else {
            selectTipo.value = "otro";
            inputTipoOtro.style.display = "block";
            inputTipoOtro.required = true;
            inputTipoOtro.value = evento.tipo_evento;
        }

        document.querySelector("[name='capacidad']").value = evento.capacidad;
        document.querySelector("[name='fecha']").value     = evento.fecha;
        document.querySelector("[name='hora_inicio']").value = evento.hora_inicio;
        document.querySelector("[name='hora_fin']").value    = evento.hora_fin;

        // Campus y Lugar
        document.getElementById("select-campus").value = evento.campus;
        const selectLugar = document.getElementById("select-lugar");
        const inputLugarOtro = document.getElementById("lugar_otro");
        if (evento.campus === "Externo") {
            selectLugar.style.display = "none";
            selectLugar.required = false;
            inputLugarOtro.style.display = "block";
            inputLugarOtro.required = true;
            inputLugarOtro.value = evento.lugar;
        } else {
            inputLugarOtro.style.display = "none";
            inputLugarOtro.required = false;
            // recarga salones
            fetch(`actions/obtener_salones.php?campus=${evento.campus}`)
                .then(r => r.json())
                .then(data => {
                    selectLugar.innerHTML = data.map(salon =>
                        `<option value="${salon.id_salon}">${salon.id_salon} - Capacidad ${salon.capacidad}</option>`
                    ).join("") + '<option value="otro">Otro</option>';
                    selectLugar.style.display = "block";
                    selectLugar.required = true;
                    if (selectLugar.querySelector(`option[value="${evento.lugar}"]`)) {
                        selectLugar.value = evento.lugar;
                    } else {
                        selectLugar.value = "otro";
                        inputLugarOtro.style.display = "block";
                        inputLugarOtro.required = true;
                        inputLugarOtro.value = evento.lugar;
                    }
                });
        }

        // Quill editors
        quillComentario.root.innerHTML   = evento.comentario   || "";
        quillLineamientos.root.innerHTML = evento.lineamientos || "";

        document.querySelector("[name='direccion']").value = evento.direccion;
        document.querySelector("[name='expositor']").value = evento.expositor;

        // 1) Limpiar preview y reset remove flag
        const preview = document.getElementById("preview-imagen");
        preview.innerHTML = "";
        document.getElementById("remove_image").value = "0";

        // 2) Si ya hay imagen en BD, la mostramos
        if (evento.imagen_path) {
            // Miniatura (o un enlace si prefieres)
            const img = document.createElement("img");
            img.src = evento.imagen_path;
            img.alt = "Preview imagen";
            img.style.maxWidth = "150px";
            img.style.display = "block";
            img.style.marginBottom = "8px";
            preview.appendChild(img);

            // Botón eliminar
            const btnDel = document.createElement("button");
            btnDel.type = "button";
            btnDel.textContent = "🗑️ Eliminar imagen";
            btnDel.onclick = () => {
                document.getElementById("remove_image").value = "1";
                preview.innerHTML = "<em>Imagen eliminada.</em>";
                // limpiar input file para permitir re-subir si quiere
                document.getElementById("imagen").value = "";
            };
            preview.appendChild(btnDel);

        } else {
            // Si no hay imagen, dejamos el mensaje por defecto
            preview.innerHTML = "<em>No hay imagen asignada.</em>";
        }

        // 3) Si el usuario elige un nuevo archivo, marcamos remove_image=1 y mostramos su nombre
        const fileInput = document.getElementById("imagen");
        fileInput.value = "";
        fileInput.onchange = () => {
            if (fileInput.files.length) {
                document.getElementById("remove_image").value = "1";
                preview.innerHTML = `<em>Reemplazar con: ${fileInput.files[0].name}</em>`;
            }
        };

        // 4) Mostrar el formulario…
        document.getElementById("formulario-evento").style.display = "block";
        formBox.style.display = "block";
        formBox.scrollIntoView({ behavior: "smooth" });
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

    function limpiarFiltros() {
        document.getElementById('filtrar-campus').value = '';
        document.getElementById('filtrar-tipo').value = '';
        aplicarFiltros();
    }
    function mostrarTipoEventoOtro(valor) {
        const inputOtro = document.getElementById('tipo_evento_otro');
        if (valor === 'otro') {
            inputOtro.style.display = 'block';
            inputOtro.required = true;
        } else {
            inputOtro.style.display = 'none';
            inputOtro.required = false;
            inputOtro.value = '';
        }
    }



</script>

<style>
    /* Estilos para los editores Quill */
    #comentario-editor .ql-toolbar,
    #lineamientos-editor .ql-toolbar,
    #comentario-editor .ql-container,
    #lineamientos-editor .ql-container {
        all: initial;
        font-family: sans-serif;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
        color: #ffffff;
    }
    #comentario-editor .ql-editor,
    #lineamientos-editor .ql-editor {
        padding: 12px;
        color: #ffffff;
        font-size: 14px;
        min-height: 120px;
        box-sizing: border-box;
        text-align: left !important;
        background-color: #1e1e1e;
    }
    #comentario-editor .ql-toolbar,
    #lineamientos-editor .ql-toolbar {
        background-color: #2c2c2c;
        border: none;
    }
    /* Estilo para el select */
    select {
        background-color: #333; /* Fondo oscuro */
        color: white; /* Texto blanco */
        border: 1px solid #ccc;
        padding: 5px;
        border-radius: 4px;
    }

    /* Estilo para las opciones del select */
    select option {
        background-color: #333; /* Fondo oscuro */
        color: white; /* Texto blanco */
    }

    /* Hacer el texto de la previsualización blanco */
    #preview-imagen {
        color: #fff;
    }
    /* Si usas <em> para el texto de “No hay imagen…”, también blanco o un gris claro */
    #preview-imagen em {
        color: #ccc;
    }
    /* Y si quieres que los botones ahí dentro sean acordes */
    #preview-imagen button {
        background: transparent;
        color: #fff;
        border: 1px solid #fff;
        padding: 4px 8px;
        cursor: pointer;
    }
</style>


