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

// Traer salones
$sql  = "SELECT id_salon, nombre, campus, capacidad FROM salones ORDER BY id_salon";
$stmt = $pdo->query($sql);
$salones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<div class="admin-events-container">
    <h2>Gestión de Salones</h2>

    <!-- Botón para agregar un nuevo salón -->
    <button class="agregar" onclick="mostrarFormulario()">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
            <path d="M8 3v4M16 3v4M4 11h16"></path>
        </svg>
        <span class="lable">Agregar Nuevo Salón</span>
    </button>

    <table id="tabla-salones" class="display" style="width:100%">
        <thead>
        <tr>
            <th>ID Salón</th>
            <th>Nombre</th>
            <th>Campus</th>
            <th>Capacidad</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($salones as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['id_salon']) ?></td>
                <td><?= htmlspecialchars($s['nombre']) ?></td>
                <td><?= htmlspecialchars($s['campus']) ?></td>
                <td><?= htmlspecialchars($s['capacidad']) ?></td>
                <td>
                    <button class="Btn" onclick="editarSalon('<?= $s['id_salon'] ?>')">Editar</button>
                    <button class="Btn" onclick="eliminarSalon('<?= $s['id_salon'] ?>')">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Formulario para agregar/editar un salón -->
<div class="login-box" id="formulario-salon" style="display: none;">
    <p id="titulo-formulario">Nuevo Salón</p>
    <form id="form-salon">
        <input type="hidden" name="id_salon_oculto">

        <div class="user-box">
            <label>ID Salón</label>
            <input type="text" name="id_salon" required>
        </div>
        <div class="user-box">
            <label>Nombre</label>
            <input type="text" name="nombre" required>
        </div>
        <div class="user-box no-floating select-box">
            <label>Campus</label>
            <select name="campus" required>
                <option value="">Seleccione campus</option>
                <option value="Norte">Norte</option>
                <option value="Sur">Sur</option>
            </select>
        </div>
        <div class="user-box">
            <label>Capacidad</label>
            <input type="number" name="capacidad" min="1" required>
        </div>

        <button type="submit" class="styled-button">Guardar</button>
        <button type="button" class="styled-button" onclick="ocultarFormulario()">Cancelar</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#tabla-salones').DataTable({
            pageLength: 10,
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron salones",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                }
            }
        });
    });

    function mostrarFormulario() {
        $('#titulo-formulario').text('Nuevo Salón');
        $('#form-salon')[0].reset();
        $('[name="id_salon_oculto"]').val('');
        $('[name="id_salon"]').prop('disabled', false);
        $('#formulario-salon').slideDown('fast', function() {
            this.scrollIntoView({behavior: 'smooth'});
        });
    }

    function ocultarFormulario() {
        $('#formulario-salon').slideUp('fast');
    }

    function editarSalon(id) {
        fetch('actions/obtener_salon.php?id_salon=' + encodeURIComponent(id))
            .then(r => r.json())
            .then(d => {
                $('#titulo-formulario').text('Editar Salón');
                $('[name="id_salon_oculto"]').val(d.id_salon);
                $('[name="id_salon"]').val(d.id_salon).prop('disabled', true);
                $('[name="nombre"]').val(d.nombre);
                $('[name="campus"]').val(d.campus);
                $('[name="capacidad"]').val(d.capacidad);
                $('#formulario-salon').slideDown('fast', function() {
                    this.scrollIntoView({behavior: 'smooth'});
                });
            })
            .catch(() => alert('Error al obtener datos'));
    }

    $('#form-salon').on('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        const url = fd.get('id_salon_oculto')
            ? 'actions/editar_salon.php'
            : 'actions/agregar_salon.php';
        fetch(url, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => {
                alert(res.success || res.error);
                if (res.success) location.reload();
            });
    });

    function eliminarSalon(id) {
        if (!confirm('Eliminar salón ' + id + '?')) return;
        fetch('actions/eliminar_salon.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_salon: id })
        })
        .then(r => r.json())
        .then(res => {
            alert(res.success || res.error);
            if (res.success) location.reload();
        });
    }
</script>

<style>
    /* Paginación en gris/negro */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background-color: #f0f0f0 !important;
        color: #000 !important;
        border: none !important;
        border-radius: 4px;
        padding: 5px 10px;
        margin: 2px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: #000 !important;
        color: #fff !important;
        font-weight: bold;
    }
</style>
