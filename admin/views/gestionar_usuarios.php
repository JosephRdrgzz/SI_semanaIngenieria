<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Verificar que el usuario es administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header("Location: index.php?view=login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $handle = fopen($file, 'r');
    if ($handle) {
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $exp = $data[0];
            $nombre = $data[1];
            $idprograma = $data[2];

            // Verificar si el id ya existe
            $checkQuery = $pdo->prepare("SELECT COUNT(*) FROM alumnos WHERE exp = ?");
            $checkQuery->execute([$exp]);
            $exists = $checkQuery->fetchColumn();

            if ($exists == 0) {
                $query = $pdo->prepare("INSERT INTO alumnos (exp, nombre, idprograma, mail, campus, semestre, celular, telefono, responsable, alta) VALUES (?, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL)");
                $query->execute([$exp, $nombre, $idprograma]);
            }
        }
        fclose($handle);
        $message = "Datos insertados correctamente.";
    } else {
        $message = "Error al abrir el archivo.";
    }
}
?>

<div class="container">
    <h2>Gestión de Usuarios</h2>
    <?php if (isset($message)): ?>
        <div class="modern-success-message">
            <button class="close-btn" onclick="this.parentElement.style.display='none';">×</button>
            <div class="icon-wrapper">
                <svg stroke-linejoin="round" stroke-linecap="round" stroke-width="2" stroke="currentColor" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="success-icon">
                    <path d="M9 12l2 2 4-4"></path>
                    <circle r="10" cy="12" cx="12"></circle>
                </svg>
            </div>
            <div class="text-wrapper">
                <div class="title">Success</div>
                <div class="message"><?= htmlspecialchars($message) ?></div>
            </div>
        </div>
    <br>
    <br>
    <?php endif; ?>

    <form class="form" action="index.php?view=gestionar_usuarios" method="post" enctype="multipart/form-data">
        <span class="form-title">Upload your file</span>
        <p class="form-paragraph">File should be a CSV</p>

        <label for="file-input" class="drop-container">
            <span class="drop-title">Drop files here</span>
            or
            <input type="file" name="file" accept=".csv" required id="file-input">
        </label>

        <button type="submit" class="btn waves-effect waves-light">Subir</button>
    </form>
</div>

<br>
<br>
<style>
    .form {
        background-color: #fff;
        box-shadow: 0 10px 60px rgb(218, 229, 255);
        border: 1px solid rgb(159, 159, 160);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        font-size: 1.125rem;
        max-width: 500px;
        width: 100%;
        margin: auto;
    }

    .form-title {
        color: #000000;
        font-size: 1.8rem;
        font-weight: 500;
    }

    .form-paragraph {
        margin-top: 10px;
        font-size: 0.9375rem;
        color: rgb(105, 105, 105);
    }

    .drop-container {
        background-color: #fff;
        position: relative;
        display: flex;
        gap: 10px;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 10px;
        margin-top: 2.1875rem;
        border-radius: 10px;
        border: 2px dashed rgb(171, 202, 255);
        color: #444;
        cursor: pointer;
        transition: background .2s ease-in-out, border .2s ease-in-out;
        width: 90%;
        max-width: 450px;
    }

    .drop-container:hover {
        background: rgba(0, 140, 255, 0.164);
        border-color: rgba(17, 17, 17, 0.616);
    }

    .drop-container:hover .drop-title {
        color: #222;
    }

    .drop-title {
        color: #444;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        transition: color .2s ease-in-out;
    }

    #file-input {
        width: 100%;
        color: #444;
        padding: 2px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid rgba(8, 8, 8, 0.288);
    }

    #file-input::file-selector-button {
        margin-right: 20px;
        border: none;
        background: #084cdf;
        padding: 10px 20px;
        border-radius: 10px;
        color: #fff;
        cursor: pointer;
        transition: background .2s ease-in-out;
    }

    #file-input::file-selector-button:hover {
        background: #0d45a5;
    }




    .modern-success-message {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        background: linear-gradient(135deg, #616161, #0d0e0e);
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        color: white;
        font-family: "Poppins", sans-serif;
        position: relative;
        overflow: hidden;
        transition:
                transform 0.3s ease-in-out,
                box-shadow 0.3s ease-in-out;
    }

    .modern-success-message:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    }

    .close-btn {
        position: absolute;
        top: 12px;
        right: 20px;
        background: none;
        border: none;
        font-size: 30px;
        color: white;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.3s;
    }

    .close-btn:hover {
        opacity: 1;
    }

    .icon-wrapper {
        background-color: rgba(255, 255, 255, 0.15);
        padding: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .success-icon {
        width: 40px;
        height: 40px;
    }

    .text-wrapper .title {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }

    .text-wrapper .message {
        margin-top: 6px;
        font-size: 14px;
        opacity: 0.85;
    }
</style>



















