<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Si ya hay una sesión iniciada, redirigir al home
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php?view=home");
    exit();
}

$error = $_SESSION['error'] ?? "";
unset($_SESSION['error']);
?>

<h2>Iniciar Sesión</h2>
<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div id="form-ui">
    <form action="actions/login.php" method="post" id="form">
        <div id="form-body">
            <div id="welcome-lines">
                <div id="welcome-line-1">LOGIN</div>
            </div>
            <div id="input-area">
                <div class="form-inp">
                    <!-- Campo para ID o correo -->
                    <input placeholder="ID o Correo" type="text" name="usuario" id="usuario" required>
                </div>
                <div class="form-inp" id="password-container">
                    <input placeholder="Contraseña" type="password" name="contraseña" id="password-field" required>
                    <span class="toggle-eye">
                        <i class="material-icons" id="toggle-password-icon">visibility_off</i>
                      </span>
                </div>
            </div>
            <div id="submit-button-cvr">
                <button id="submit-button" type="submit">Login</button>
            </div>
            <div id="bar"></div>
        </div>
    </form>
</div>

<script>
    const toggleIcon = document.getElementById('toggle-password-icon');
    const passwordField = document.getElementById('password-field');

    toggleIcon.addEventListener('click', function() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.textContent = 'visibility'; // Cambia el ícono a "ojo abierto"
        } else {
            passwordField.type = 'password';
            toggleIcon.textContent = 'visibility_off'; // Cambia el ícono a "ojo tachado"
        }
    });
</script>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

