<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';


if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php?view=home");
    exit();
}
?>

<h2>Iniciar Sesión</h2>
<?php if (isset($_GET['error'])) echo "<p style='color:red;'>".htmlspecialchars($_GET['error'])."</p>"; ?>

<form method="POST" action="actions/login.php">
    <label>Expediente:</label>
    <input type="text" name="exp" required><br>
    <input type="submit" value="Ingresar">
</form>
