<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?view=login");
    exit();
}

$usuario = $_SESSION['usuario'];
$exp = $usuario['exp'];

// Verificar si tiene datos faltantes
$query = $pdo->prepare("SELECT mail, campus, semestre, celular, telefono, responsable 
                        FROM alumnos WHERE exp = :exp");
$query->execute(['exp' => $exp]);
$userData = $query->fetch(PDO::FETCH_ASSOC);

$datosFaltantes = [];

// Validar que no haya campos vacíos
foreach ($userData as $campo => $valor) {
    if (empty($valor)) {
        $datosFaltantes[] = $campo;
    }
}

// Si todos los datos están completos, redirigir a eventos
if (empty($datosFaltantes)) {
    header("Location: index.php?view=eventos");
    exit();
}

?>

<h2>Completa tu información</h2>

<form action="actions/actualizar_perfil.php" method="POST">
    <label for="mail">Correo institucional (@anahuac.mx):</label>
    <input type="email" name="mail" id="mail" required pattern=".+@anahuac\.mx"
           value="<?= htmlspecialchars($userData['mail'] ?? '') ?>">
    <br>

    <label for="campus">Campus:</label>
    <select name="campus" id="campus" required>
        <option value="">Selecciona</option>
        <option value="Norte" <?= ($userData['campus'] == 'Norte') ? 'selected' : '' ?>>Norte</option>
        <option value="Sur" <?= ($userData['campus'] == 'Sur') ? 'selected' : '' ?>>Sur</option>
    </select>
    <br>

    <label for="semestre">Semestre:</label>
    <input type="number" name="semestre" id="semestre" required min="1" max="12"
           value="<?= htmlspecialchars($userData['semestre'] ?? '') ?>">
    <br>

    <label for="celular">Celular (10 dígitos):</label>
    <input type="text" name="celular" id="celular" required pattern="\d{10}"
           value="<?= htmlspecialchars($userData['celular'] ?? '') ?>">
    <br>

    <label for="telefono">Teléfono (10 dígitos):</label>
    <input type="text" name="telefono" id="telefono" required pattern="\d{10}"
           value="<?= htmlspecialchars($userData['telefono'] ?? '') ?>">
    <br>

    <label for="responsable">Número de contacto de emergencia (10 dígitos):</label>
    <input type="text" name="responsable" id="responsable" required pattern="\d{10}"
           value="<?= htmlspecialchars($userData['responsable'] ?? '') ?>">
    <br>

    <input type="submit" value="Actualizar">
</form>
