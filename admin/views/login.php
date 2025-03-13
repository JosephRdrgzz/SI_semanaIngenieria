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

<div id="form-ui">
    <form action="actions/login.php" method="post" id="form">
        <div id="form-body">
            <div id="welcome-lines">
                <div id="welcome-line-1">ADMIN</div>
            </div>
            <div id="input-area">
                <div class="form-inp">
                    <input placeholder="Email Address" type="text" name="exp" required>
                </div>
                <div class="form-inp">
                    <input placeholder="Password" type="password" name="contraseña" required>
                </div>
            </div>
            <div id="submit-button-cvr">
                <button id="submit-button" type="submit">Login</button>
            </div>
            <div id="forgot-pass">
                <a href="#">Forgot password?</a>
            </div>
            <div id="bar"></div>
        </div>
    </form>
</div>

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
