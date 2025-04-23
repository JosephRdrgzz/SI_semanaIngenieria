<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?view=login");
    exit();
}

$usuario = $_SESSION['usuario'];
$exp = $usuario['exp'];

// Incluimos el campo idprograma en la consulta
$query = $pdo->prepare("SELECT idprograma, mail, campus, semestre, celular, telefono, responsable 
                        FROM alumnos WHERE exp = :exp");
$query->execute(['exp' => $exp]);
$userData = $query->fetch(PDO::FETCH_ASSOC);

// Si el usuario es profesor, se marca el perfil como completo y se redirige
if ($userData['idprograma'] === 'PROFESOR') {
    $_SESSION['perfil_completo'] = true;
    header("Location: index.php?view=eventos");
    exit();
}

$datosFaltantes = [];

// Validar que no haya campos vacíos (excluimos idprograma de la validación)
foreach ($userData as $campo => $valor) {
    if ($campo !== 'idprograma' && empty($valor)) {
        $datosFaltantes[] = $campo;
    }
}

// Si todos los datos están completos, se marca el perfil como completo y se redirige
if (empty($datosFaltantes)) {
    $_SESSION['perfil_completo'] = true;
    header("Location: index.php?view=eventos");
    exit();
}
?>
<title>Completa tu información</title>
<style>
    .profile-update {
        position: relative;
        width: 320px;
        padding: 20px;
        background-color: #FFF;
        border-radius: 4px;
        color: #333;
        box-shadow: 0px 0px 60px 5px rgba(0,0,0,0.4);
        margin: 50px auto;
    }
    .profile-update:after {
        position: absolute;
        content: "";
        right: -10px;
        bottom: 18px;
        width: 0;
        height: 0;
        border-left: 0px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid #1a044e;
    }
    .profile-update h2 {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        letter-spacing: 4px;
        line-height: 28px;
        margin-bottom: 20px;
    }
    .profile-update form {
        display: flex;
        flex-direction: column;
    }
    .profile-update label {
        margin-top: 10px;
    }
    .profile-update .email-container {
        display: flex;
        align-items: center;
    }
    .profile-update input[type="text"],
    .profile-update input[type="number"],
    .profile-update select {
        border: none;
        border-bottom: 1px solid #d4d4d4;
        padding: 10px;
        background: transparent;
        transition: all .25s ease;
        margin-bottom: 10px;
    }
    .profile-update input:focus,
    .profile-update select:focus {
        outline: none;
        border-bottom: 1px solid #0d095e;
        font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', 'sans-serif';
    }
    .profile-update .profile-update-btn {
        border-radius: 30px;
        border-bottom-right-radius: 0;
        border-top-right-radius: 0;
        background-color: #0f0092;
        color: #FFF;
        padding: 12px 25px;
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 5px;
        cursor: pointer;
        transition: all .25s ease;
        box-shadow: -5px 6px 20px 0px rgba(26,26,26,0.4);
        margin: 20px auto 0 auto;
        border: none;
        text-align: center;
    }
    .profile-update .profile-update-btn:hover {
        background-color: #07013d;
        box-shadow: -5px 6px 20px 0px rgba(88,88,88,0.569);
    }
    .profile-update .email-domain {
        margin-left: 5px;
        font-size: 16px;
        color: #333;
    }
</style>
<script>
    function validateEmail(event) {
        const emailInput = document.getElementById('mail');
        const emailValue = emailInput.value;
        if (emailValue.includes('@')) {
            alert('No incluyas el símbolo @ en el correo institucional.');
            emailInput.value = emailValue.split('@')[0];
            event.preventDefault(); // Prevenir el envío del formulario
            return false; // Asegurar que el formulario no se envíe
        }
        return true;
    }

    function validateForm(event) {
        const celular = document.getElementById('celular').value;
        const responsable = document.getElementById('responsable').value;

        if (celular === responsable) {
            alert('El número de celular y el de emergencia no pueden ser iguales.');
            event.preventDefault(); // Prevenir el envío del formulario
            return false; // Asegurar que el formulario no se envíe
        }

        return validateEmail(event); // Llamar a la validación del correo
    }
</script>
</head>
<body>
<div class="profile-update">
    <h2>Completa tu información</h2>
    <form action="actions/actualizar_perfil.php" method="POST" onsubmit="return validateForm(event)">
        <label>No podras continuar si no completas tu información</label>
        <label for="mail">Correo institucional (solo ingresa lo que está antes del @anahuac.mx):</label>
        <div class="email-container">
            <input type="text" name="mail" id="mail" required pattern="^[a-zA-Z0-9._%+-]+$" />
            <span>@anahuac.mx</span>
        </div>

        <label for="campus">Campus:</label>
        <select name="campus" id="campus" required>
            <option value="">Selecciona</option>
            <option value="Norte" <?= ($userData['campus'] == 'Norte') ? 'selected' : '' ?>>Norte</option>
            <option value="Sur" <?= ($userData['campus'] == 'Sur') ? 'selected' : '' ?>>Sur</option>
        </select>

        <label for="semestre">Semestre:</label>
        <input type="number" name="semestre" id="semestre" required min="1" max="12"
               value="<?= htmlspecialchars($userData['semestre'] ?? '') ?>">

        <label for="celular">Celular (10 dígitos):</label>
        <input type="text" name="celular" id="celular" required pattern="\d{10}"
               value="<?= htmlspecialchars($userData['celular'] ?? '') ?>">

        <label for="telefono">Teléfono (10 dígitos):</label>
        <input type="text" name="telefono" id="telefono" required pattern="\d{10}"
               value="<?= htmlspecialchars($userData['telefono'] ?? '') ?>">

        <label for="responsable">En caso de emergencia llamar a: (10 dígitos):</label>
        <input type="text" name="responsable" id="responsable" required pattern="\d{10}"
               value="<?= htmlspecialchars($userData['responsable'] ?? '') ?>">

        <input type="submit" class="profile-update-btn" value="Actualizar">
    </form>
</div>
</body>
</html>

