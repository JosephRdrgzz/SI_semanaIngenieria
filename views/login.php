<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Si ya hay una sesión iniciada, redirigir al home
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php?view=home");
    exit();
}

$error = $_SESSION['error'] ?? ""; // Inicializar variable de error
unset($_SESSION['error']); // Borrar error tras mostrarlo

?>

<h2>Iniciar Sesión</h2>
<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="actions/login.php">
    <label>Expediente:</label>
    <input type="text" name="exp" id="exp" required><br>

    <!-- Campo de contraseña oculto inicialmente -->
    <div id="password-container" style="display: none;">
        <label>Contraseña:</label>
        <input type="password" name="contraseña" id="password-field"><br>
    </div>

    <input type="submit" value="Ingresar">
</form>

<script>
    document.getElementById("exp").addEventListener("blur", function () {
        let exp = this.value.trim();
        if (exp !== "") {
            fetch("actions/verificar_admin.php?exp=" + exp)
                .then(response => response.json())
                .then(data => {
                    let passwordContainer = document.getElementById("password-container");
                    if (data.es_admin) {
                        passwordContainer.style.display = "block";
                        document.getElementById("password-field").setAttribute("required", "true");
                    } else {
                        passwordContainer.style.display = "none";
                        document.getElementById("password-field").removeAttribute("required");
                    }
                });
        }
    });
</script>
