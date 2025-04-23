<?php
session_start();
require_once __DIR__ . '/../config/conexion.php'; // Asegúrate de que este archivo define $pdo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Capturamos el término de búsqueda si existe
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Consulta de alumnos
if ($busqueda === '') {
    $query_alumnos = "SELECT * FROM alumnos ORDER BY exp ASC";
    $stmt_alumnos = $pdo->query($query_alumnos);
} else {
    $query_alumnos = "SELECT * FROM alumnos WHERE LOWER(exp) LIKE LOWER(:busqueda) OR LOWER(nombre) LIKE LOWER(:busqueda) ORDER BY exp ASC";
    $stmt_alumnos = $pdo->prepare($query_alumnos);
    $stmt_alumnos->execute([':busqueda' => "%{$busqueda}%"]);
}
$alumnos = $stmt_alumnos->fetchAll(PDO::FETCH_ASSOC);

// Consulta de programas
$query_programas = "SELECT clave, descripcion FROM catalogo_programas";
$stmt_programas = $pdo->query($query_programas);
$programas = $stmt_programas->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Agregar estilos y scripts de DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<div class="admin-events-container">
    <h2>Gestión de Alumnos</h2>

    <!-- Botón para agregar un nuevo alumno -->
    <button class="agregar" onclick="mostrarFormulario()">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none" class="svg-icon">
            <g stroke-width="2" stroke-linecap="round" stroke="#fff">
                <rect y="5" x="4" width="16" rx="2" height="16"></rect>
                <path d="m8 3v4"></path>
                <path d="m16 3v4"></path>
                <path d="m4 11h16"></path>
            </g>
        </svg>
        <span class="lable">Agregar Nuevo Alumno</span>
    </button>

    <table id="tabla-alumnos" class="display">
        <thead>
        <tr>
            <th>Expediente</th>
            <th>Nombre</th>
            <th>Programa</th>
            <th>Mail</th>
            <th>Campus</th>
            <th>Semestre</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($alumnos as $alumno): ?>
            <tr>
                <td><?= htmlspecialchars($alumno['exp'] ?? '') ?></td>
                <td><?= htmlspecialchars($alumno['nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars($alumno['idprograma'] ?? '') ?></td>
                <td><?= htmlspecialchars($alumno['mail'] ?? '') ?></td>
                <td><?= htmlspecialchars($alumno['campus'] ?? '') ?></td>
                <td><?= htmlspecialchars($alumno['semestre'] ?? '') ?></td>
                <td>
                    <button class="Btn" onclick="editarAlumno('<?= $alumno['exp'] ?>')">Editar
                        <svg class="svg" viewBox="0 0 512 512">
                            <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Formulario para agregar/editar un alumno -->
<div class="login-box" id="formulario-alumno" style="display: none;">
    <p id="titulo-formulario">Nuevo Alumno</p>
    <form id="form-alumno">
        <input type="hidden" name="exp_oculto">
        <div class="user-box">
            <label>Expediente</label>
            <input type="text" name="exp" required>
        </div>
        <div class="user-box">
            <label>Nombre</label>
            <input type="text" name="nombre" required>
        </div>
        <div class="user-box no-floating select-box">
            <label for="idprograma">Programa</label>
            <select name="idprograma" id="idprograma" required>
                <option value="">Seleccione un programa</option>
                <?php foreach ($programas as $p): ?>
                    <option value="<?= htmlspecialchars($p['clave']) ?>">
                        <?= htmlspecialchars($p['clave']) ?> - <?= htmlspecialchars($p['descripcion']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="editing-fields" style="display: none;">
            <div class="two-column">
                <div class="user-box">
                    <label>Email</label>
                    <input type="email" name="mail">
                </div>
                <div class="user-box no-floating select-box">
                    <label for="campus">Campus</label>
                    <select name="campus" id="campus">
                        <option value="">Seleccione campus</option>
                        <option value="Norte">Norte</option>
                        <option value="Sur">Sur</option>
                    </select>
                </div>
            </div>
            <div class="two-column">
                <div class="user-box">
                    <label>Semestre</label>
                    <input type="text" name="semestre" placeholder="Ej: 8">
                </div>
                <div class="user-box">
                    <label>Celular</label>
                    <input type="text" name="celular">
                </div>
            </div>
            <div class="two-column">
                <div class="user-box">
                    <label>Teléfono</label>
                    <input type="text" name="telefono">
                </div>
                <div class="user-box">
                    <label>Responsable</label>
                    <input type="text" name="responsable">
                </div>
            </div>
        </div>
        <button type="submit" class="styled-button">
            Guardar
        </button>
        <button type="button" class="styled-button" onclick="ocultarFormulario()">
            Cancelar
        </button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#tabla-alumnos').DataTable({
            "pageLength": 10,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron alumnos",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    });

    function mostrarFormulario() {
        document.getElementById("titulo-formulario").textContent = "Nuevo Alumno";
        document.getElementById("form-alumno").reset();
        document.querySelector("[name='exp_oculto']").value = "";
        document.querySelector(".editing-fields").style.display = "none";
        document.getElementById("formulario-alumno").style.display = "block";
    }

    function ocultarFormulario() {
        document.getElementById("formulario-alumno").style.display = "none";
    }

    function editarAlumno(exp) {
        fetch('actions/obtener_alumno.php?exp=' + encodeURIComponent(exp))
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.error);
                    return;
                }
                document.getElementById("titulo-formulario").textContent = "Editar Alumno";
                document.querySelector("[name='exp_oculto']").value = data.exp;
                document.querySelector("[name='exp']").value = data.exp ?? '';
                document.querySelector("[name='nombre']").value = data.nombre ?? '';
                document.querySelector("[name='idprograma']").value = data.idprograma ?? '';
                document.querySelector("[name='mail']").value = data.mail ?? '';
                document.querySelector("[name='campus']").value = data.campus ?? '';
                document.querySelector("[name='semestre']").value = data.semestre ?? '';
                document.querySelector("[name='celular']").value = data.celular ?? '';
                document.querySelector("[name='telefono']").value = data.telefono ?? '';
                document.querySelector("[name='responsable']").value = data.responsable ?? '';

                document.querySelector(".editing-fields").style.display = "block";
                document.getElementById("formulario-alumno").style.display = "block";
                document.getElementById("formulario-alumno").scrollIntoView({ behavior: "smooth" });
            })
            .catch(error => {
                console.error(error);
                alert("Error de comunicación con el servidor");
            });
    }

    document.getElementById("form-alumno").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const expOculto = formData.get("exp_oculto");
        let url = expOculto ? "actions/editar_alumno.php" : "actions/agregar_alumno.php";

        fetch(url, {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success);
                    ocultarFormulario();
                    actualizarTablaAlumnos();
                } else if (data.error) {
                    alert("Error: " + data.error);
                }
            })
            .catch(error => {
                console.error("Error en la petición:", error);
                alert("Ocurrió un error al guardar los datos.");
            });
    });
    function actualizarTablaAlumnos() {
        fetch('actions/listar_alumnos.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert("Error: " + data.error);
                    return;
                }
                const table = $('#tabla-alumnos').DataTable();
                table.clear();
                data.alumnos.forEach(alumno => {
                    table.row.add([
                        alumno.exp,
                        alumno.nombre,
                        alumno.idprograma,
                        alumno.mail,
                        alumno.campus,
                        alumno.semestre,
                        `<button class="Btn" onclick="editarAlumno('${alumno.exp}')">Editar
                        <svg class="svg" viewBox="0 0 512 512">
                            <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                        </svg>
                    </button>`
                    ]).draw();
                });
            })
            .catch(error => {
                console.error("Error al actualizar la tabla:", error);
                alert("Ocurrió un error al actualizar la tabla: " + error.message);
            });
    }


</script>

<style>
    /* Estilo para los botones de paginación */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background-color: #FF3228;
        color: white !important;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        margin: 2px;
        transition: background-color 0.3s ease;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #FF3228;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #000000;
        font-weight: bold;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        background-color: #cccccc;
        color: #666666 !important;
    }
</style>

