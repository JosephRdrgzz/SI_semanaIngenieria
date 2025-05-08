<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sólo super-admin
if (empty($_SESSION['is_super'])) {
    header("Location: index.php?view=home");
    exit();
}

// Traer todos los administradores (no super-admin)
$stmt = $pdo->query("
    SELECT exp, nombre, correo, creado_en
      FROM administradores
     WHERE is_super = FALSE
  ORDER BY exp
");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<div class="admin-events-container">
    <h2>Gestión de Administradores</h2>
    <button class="agregar" onclick="mostrarFormulario()">
        <!-- ícono + texto igual que en alumnos -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" class="svg-icon">
            <g stroke="#fff" stroke-width="2" stroke-linecap="round">
                <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                <path d="M8 3v4M16 3v4M4 11h16"></path>
            </g>
        </svg>
        <span class="lable">Agregar Nuevo Administrador</span>
    </button>

    <table id="tabla-admins" class="display">
        <thead>
        <tr>
            <th>Expediente</th><th>Nombre</th><th>Correo</th><th>Creado En</th><th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($admins as $a): ?>
            <tr>
                <td><?=htmlspecialchars($a['exp'])?></td>
                <td><?=htmlspecialchars($a['nombre'])?></td>
                <td><?=htmlspecialchars($a['correo'])?></td>
                <td><?=htmlspecialchars($a['creado_en'])?></td>
                <td>
                    <button class="Btn" onclick="editarAdmin('<?=$a['exp']?>')">Editar</button>
                    <button class="Btn" onclick="eliminarAdmin('<?=$a['exp']?>')">Eliminar</button>
                    <a class="Btn" href="index.php?view=editar_permisos&exp=<?=$a['exp']?>">Permisos</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- formulario oculto -->
<div class="login-box" id="formulario-admin" style="display:none;">
    <p id="titulo-formulario">Nuevo Administrador</p>
    <form id="form-admin">
        <input type="hidden" name="exp_oculto">
        <div class="user-box">
            <label>Expediente</label>
            <input type="text" name="exp" required>
        </div>
        <div class="user-box">
            <label>Nombre</label>
            <input type="text" name="nombre" required>
        </div>
        <div class="user-box">
            <label>Correo</label>
            <input type="email" name="correo" required>
        </div>
        <div class="user-box">
            <label>Contraseña</label>
            <input type="password" name="contrasena" placeholder="Requerida sólo al crear">
        </div>
        <button type="submit" class="styled-button">Guardar</button>
        <button type="button" class="styled-button" onclick="ocultarFormulario()">Cancelar</button>
    </form>
</div>

<script>
    $(document).ready(()=>{
        $('#tabla-admins').DataTable({
            pageLength:10,
            language:{
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron administradores",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                paginate:{first:"«",previous:"‹",next:"›",last:"»"}
            }
        });
    });

    function mostrarFormulario(){
        $('#titulo-formulario').text('Nuevo Administrador');
        $('#form-admin')[0].reset();
        $('[name=exp_oculto]').val('');
        $('[name=exp]').prop('disabled',false);
        $('#formulario-admin').slideDown('fast');
    }

    function ocultarFormulario(){
        $('#formulario-admin').slideUp('fast');
    }

    function editarAdmin(exp){
        fetch('actions/obtener_admin.php?exp='+exp)
            .then(r=>r.json())
            .then(d=>{
                $('#titulo-formulario').text('Editar Administrador');
                $('[name=exp_oculto]').val(d.exp);
                $('[name=exp]').val(d.exp).prop('disabled',true);
                $('[name=nombre]').val(d.nombre);
                $('[name=correo]').val(d.correo);
                $('#formulario-admin').slideDown('fast');
            })
            .catch(()=>alert('Error al obtener datos'));
    }

    $('#form-admin').on('submit', function(e){
        e.preventDefault();
        // Validación rápida de dominio
        const email = $('[name=correo]').val().toLowerCase();
        if (!email.endsWith('@anahuac.mx')){
            alert('El correo debe terminar en @anahuac.mx');
            return;
        }
        const fd = new FormData(this);
        const url = fd.get('exp_oculto')
            ? 'actions/editar_admin.php'
            : 'actions/agregar_admin.php';

        fetch(url, { method:'POST', body: fd })
            .then(r=>r.json())
            .then(res=>{
                alert(res.success||res.error);
                if (res.success) location.reload();
            })
            .catch(()=>alert('Error de comunicación'));
    });

    function eliminarAdmin(exp){
        if (!confirm('Eliminar administrador '+exp+'?')) return;
        fetch('actions/eliminar_admin.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body: JSON.stringify({exp})
        })
            .then(r=>r.json())
            .then(res=>{
                alert(res.success||res.error);
                if (res.success) location.reload();
            });
    }
</script>

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background-color:#f0f0f0!important;
        color:#000!important;
        border:none!important;
        border-radius:4px;
        padding:5px 10px;
        margin:2px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color:#000!important;
        color:#fff!important;
        font-weight:bold;
    }
</style>
