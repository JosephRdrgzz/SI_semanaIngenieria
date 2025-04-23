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

// Consultar los tipos de evento existentes en el ENUM
$query_tipos = $pdo->query("SELECT unnest(enum_range(NULL::tipo_evento_enum)) AS tipo");
$tipos_evento = $query_tipos->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Se incluye Quill Editor para la edición -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>

<div class="admin-events-container">
    <h2>Reporte de Asistencia</h2>
    <!-- IFrame oculto para descarga -->
    <iframe name="downloadFrame" style="display:none;"></iframe>

    <!-- Formulario oculto para envío GET -->
    <form id="reporteForm"
          action="/semana/admin/actions/generar_reporte.php"
	  method="GET"
          target="downloadFrame"
          style="display:none;">
        <input type="hidden" name="id" id="reporteId">
    </form>
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
            <?php foreach ($tipos_evento as $tipo): ?>
                <option value="<?= htmlspecialchars($tipo['tipo']) ?>">
                    <?= htmlspecialchars($tipo['tipo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="buscar-eventos" style="margin-left:20px;">Buscar Evento:</label>
        <input type="text" id="buscar-eventos" placeholder="Buscar por nombre" onkeyup="aplicarFiltros()">
        <button onclick="limpiarFiltros()">Limpiar Filtros</button>
    </div>
</div>

<!-- Formulario para agregar/editar un evento (se reutiliza el mismo) -->
<div class="login-box" id="formulario-evento" style="display: none;">
    <p id="titulo-formulario">Nuevo Evento</p> <!-- Se cambiará a "Editar Evento" al cargar datos -->
    <form id="form-evento">
        <!-- ID oculto para edición -->
        <input type="hidden" name="id_evento">
        <!-- Filas de dos columnas -->
        <div class="two-column">
            <div class="user-box">
                <label>Nombre</label>
                <input type="text" name="nombre" required>
            </div>
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
                <input type="text" name="tipo_evento_otro" id="tipo_evento_otro" placeholder="Especificar nuevo tipo" style="display:none;">
            </div>
        </div>
        <div class="two-column">
            <div class="user-box no-floating">
                <label for="capacidad">Capacidad</label>
                <input type="number" name="capacidad" id="capacidad" min="1" required>
            </div>
            <div class="user-box no-floating">
                <label for="fecha">Fecha</label>
                <input type="date" name="fecha" id="fecha" required>
            </div>
        </div>
        <div class="two-column">
            <div class="user-box no-floating">
                <label for="hora_inicio">Hora Inicio</label>
                <input type="time" name="hora_inicio" id="hora_inicio" required>
            </div>
            <div class="user-box no-floating">
                <label for="hora_fin">Hora Fin</label>
                <input type="time" name="hora_fin" id="hora_fin" required>
            </div>
        </div>
        <div class="two-column">
            <div class="user-box no-floating select-box">
                <label for="select-campus">Campus</label>
                <select name="campus" id="select-campus" required>
                    <option value="">Seleccione un campus</option>
                    <option value="Norte">Norte</option>
                    <option value="Sur">Sur</option>
                    <option value="Externo">Externo</option>
                </select>
            </div>
            <div class="user-box no-floating select-box" id="lugar-container">
                <label for="select-lugar">Lugar</label>
                <select name="lugar" id="select-lugar" style="display:none;"></select>
                <input type="text" name="lugar_otro" id="lugar_otro" style="display:none;" placeholder="Especificar otro lugar">
            </div>
        </div>
        <!-- Comentario con Quill -->
        <div class="user-box no-floating textarea-box">
            <label for="comentario-editor">Comentario</label>
            <div id="comentario-editor" style="height: 150px;"></div>
            <input type="hidden" name="comentario" id="comentario">
        </div>
        <!-- Lineamientos con Quill -->
        <div class="user-box textarea-box">
            <label for="lineamientos-editor">Lineamientos</label>
            <div id="lineamientos-editor" style="height: 150px;"></div>
            <input type="hidden" name="lineamientos" id="lineamientos">
        </div>
        <!-- Dirección -->
        <div class="user-box textarea-box">
            <textarea name="direccion" required></textarea>
            <label>Dirección</label>
        </div>
        <!-- Expositor -->
        <div class="user-box">
            <input type="text" name="expositor">
            <label>Expositor</label>
        </div>
        <!-- Botones -->
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

<!-- Lista de eventos (sólo los que ya han iniciado) -->
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
            <div class="col col-8">Registro</div>
            <div class="col col-9">Asistencia</div>
            <div class="col col-10">Acciones</div>
        </li>
        <?php foreach ($eventos as $evento): ?>
            <?php
            // Se crea el objeto DateTime con zona fija CDMX (-06:00)
            $fechaHoraEvento = new DateTime($evento['fecha'] . ' ' . $evento['hora_inicio'] . ' -06:00');
            $ahora = new DateTime('now', new DateTimeZone('-06:00'));
            // Solo se muestran eventos que ya han iniciado o pasado
            if ($fechaHoraEvento > $ahora) {
                continue;
            }
            // Procesar el campo JSON 'asistencia'
            $jsonAsistencia = json_decode($evento['asistencia'], true);
            $registro = 0;
            $asistencia = 0;
            if (is_array($jsonAsistencia)) {
                foreach ($jsonAsistencia as $exp => $horas) {
                    $registro++; // Se cuenta el expediente registrado
                    if (is_array($horas) && count($horas) > 0) {
                        $asistencia++; // Se cuenta asistencia si hay al menos una hora registrada
                    }
                }
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
                <div class="col col-8" data-label="Registro"><?= $registro ?></div>
                <div class="col col-9" data-label="Asistencia"><?= $asistencia ?></div>
                <div class="col col-10" data-label="Acciones">
                    <button class="Btn" onclick="editarEvento(<?= $evento['id'] ?>)">Edit
                        <svg class="svg" viewBox="0 0 512 512">
                            <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231z"></path>
                        </svg>
                    </button>
                    <button class="unique-button" onclick="generarReporte(<?= $evento['id'] ?>)">
                        <span class="text">Reporte PDF</span>
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24">
                                <path d="M12 0C5.371 0 0 5.371 0 12c0 6.628 5.371 12 12 12 6.627 0 12-5.372 12-12 0-6.629-5.373-12-12-12zm1 17h-2v-2h2v2zm0-4h-2V7h2v6z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    // Inicializar Quill para la edición
    const quillComentario = new Quill('#comentario-editor', { theme: 'snow' });
    const quillLineamientos = new Quill('#lineamientos-editor', { theme: 'snow' });
    let eventos = <?= json_encode($eventos) ?>;
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

    // Función para aplicar filtros: campus, tipo y búsqueda
    function aplicarFiltros() {
        const filtroCampus = document.getElementById('filtrar-campus').value;
        const filtroTipo = document.getElementById('filtrar-tipo').value;
        const filtroBusqueda = document.getElementById('buscar-eventos').value.toLowerCase();
        document.querySelectorAll('#tabla-eventos .table-row').forEach(row => {
            const campus = row.getAttribute('data-campus');
            const tipo = row.getAttribute('data-tipo');
            const nombre = row.querySelector('.col-1').textContent.toLowerCase();
            let mostrar = true;
            if (filtroCampus && campus !== filtroCampus) { mostrar = false; }
            if (filtroTipo && tipo !== filtroTipo) { mostrar = false; }
            if (filtroBusqueda && nombre.indexOf(filtroBusqueda) === -1) { mostrar = false; }
            row.style.display = mostrar ? '' : 'none';
        });
    }

    // Función para ordenar eventos por fecha (zona fija CDMX -06:00)
    function ordenarEventos(orden = 'asc') {
        const container = document.getElementById('tabla-eventos');
        const rows = Array.from(container.querySelectorAll('.table-row'));
        rows.sort((a, b) => {
            const fechaA = new Date(`${a.getAttribute('data-fecha')}T${a.getAttribute('data-hora_inicio')}-06:00`);
            const fechaB = new Date(`${b.getAttribute('data-fecha')}T${b.getAttribute('data-hora_inicio')}-06:00`);
            return orden === 'asc' ? fechaA - fechaB : fechaB - fechaA;
        });
        rows.forEach(row => container.appendChild(row));
    }

    document.addEventListener('DOMContentLoaded', () => { aplicarFiltros(); });

    // Función para mostrar el formulario de edición con los datos del evento
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
        quillComentario.root.innerHTML = evento.comentario || "";
        quillLineamientos.root.innerHTML = evento.lineamientos || "";
        document.querySelector("[name='direccion']").value = evento.direccion;
        document.querySelector("[name='expositor']").value = evento.expositor;

        document.getElementById("formulario-evento").style.display = "block";
        let selectLugar = document.getElementById("select-lugar");
        let otroInput = document.getElementById("lugar_otro");

        if (evento.campus === "Externo") {
            selectLugar.style.display = "none";
            selectLugar.required = false;
            otroInput.style.display = "block";
            otroInput.required = true;
            otroInput.value = evento.lugar;
        } else {
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
                    if (selectLugar.querySelector(`option[value='${evento.lugar}']`)) {
                        selectLugar.value = evento.lugar;
                    } else {
                        selectLugar.value = "otro";
                        otroInput.style.display = "block";
                        otroInput.required = true;
                        otroInput.value = evento.lugar;
                    }
                })
                .catch(error => console.error("Error al obtener salones:", error));
        }
        document.getElementById("formulario-evento").scrollIntoView({ behavior: "smooth" });
    }

    // Función para generar el reporte PDF
    function generarReporte(id) {
        // inyecta el id y envía el formulario oculto al iframe
	console.log("Generando reporte para ID:", id);    
	document.getElementById('reporteId').value = id;
        document.getElementById('reporteForm').submit();
    }
    function ocultarFormulario() {
        document.getElementById("formulario-evento").style.display = "none";
    }

    function limpiarFiltros() {
        document.getElementById('filtrar-campus').value = '';
        document.getElementById('filtrar-tipo').value = '';
        aplicarFiltros();
    }

    // Función para manejar la opción "otro" en el tipo de evento
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
</script>

<style>
    /* Estilos para Quill */
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
    /* Estilos para select */
    select {
        background-color: #333;
        color: white;
        border: 1px solid #ccc;
        padding: 5px;
        border-radius: 4px;
    }
    select option {
        background-color: #333;
        color: white;
    }
</style>

