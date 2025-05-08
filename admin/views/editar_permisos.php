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

// Obtener expediente del admin
$exp = $_GET['exp'] ?? '';
if (!$exp) {
    header("Location: index.php?view=gestionar_administradores");
    exit();
}

// Datos del administrador
$stmt = $pdo->prepare("SELECT exp, nombre FROM administradores WHERE exp = :exp");
$stmt->execute([':exp' => $exp]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$admin) {
    header("Location: index.php?view=gestionar_administradores");
    exit();
}

// Todas las vistas disponibles
$vRes    = $pdo->query("SELECT id, nombre FROM vistas ORDER BY nombre");
$vistas  = $vRes->fetchAll(PDO::FETCH_ASSOC);

// Vistas que ya tiene asignadas
$pStmt   = $pdo->prepare("SELECT vista_id FROM admin_vistas WHERE admin_exp = :exp");
$pStmt->execute([':exp' => $exp]);
$current = array_column($pStmt->fetchAll(PDO::FETCH_ASSOC), 'vista_id');
?>
<!-- DataTables CSS/JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<div class="admin-events-container">
    <h2>Permisos de <?= htmlspecialchars($admin['nombre']) ?></h2>

    <form id="form-permisos">
        <input type="hidden" name="exp" value="<?= htmlspecialchars($exp) ?>">

        <table id="tabla-vistas" class="display">
            <thead>
            <tr><th>Vista</th><th>Acceso</th></tr>
            </thead>
            <tbody>
            <?php foreach ($vistas as $v): ?>
                <tr>
                    <td><?= htmlspecialchars($v['nombre']) ?></td>
                    <td>
                        <label>
                            <input
                                    type="checkbox"
                                    name="vistas[]"
                                    value="<?= $v['id'] ?>"
                                <?= in_array($v['id'], $current) ? 'checked' : '' ?>
                            >
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="styled-button">Guardar permisos</button>
        <button
                type="button"
                class="styled-button"
                onclick="location.href='index.php?view=gestionar_administradores'"
        >Cancelar</button>
    </form>
</div>

<script>
    $(document).ready(() => {
        $('#tabla-vistas').DataTable({
            pageLength: 10,
            language: {
                lengthMenu: "Mostrar _MENU_ registros por página",
                zeroRecords: "No se encontraron vistas",
                info: "Mostrando página _PAGE_ de _PAGES_",
                infoEmpty: "No hay registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                paginate: { first: "«", previous: "‹", next: "›", last: "»" }
            }
        });
    });

    document.getElementById('form-permisos').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fetch('actions/editar_permisos.php', {
            method: 'POST',
            body: fd
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    alert(res.success);
                    location.href = 'index.php?view=gestionar_administradores';
                } else {
                    alert(res.error || 'Error al guardar permisos');
                }
            })
            .catch(() => alert('Error de comunicación con el servidor'));
    });
</script>

<style>
    .dataTables_wrapper .paginate_button {
        background-color: #f0f0f0 !important;
        color: #000 !important;
        border: none !important;
        border-radius: 4px;
        padding: 5px 10px;
        margin: 2px;
    }
    .dataTables_wrapper .paginate_button.current {
        background-color: #000 !important;
        color: #fff !important;
        font-weight: bold;
    }
</style>
