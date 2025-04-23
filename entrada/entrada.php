<?php
// Iniciar sesión para verificar si el usuario está autorizado
session_start();

// Verificar si el usuario está autorizado para registrar entradas
if (!isset($_SESSION['registrar_entrada']) || $_SESSION['registrar_entrada'] !== true) {
    // Si no está autorizado, redirigir al login
    header("Location: index.php");
    exit;
}

// Verificar si se ha seleccionado un evento
if (!isset($_SESSION['evento']) || empty($_SESSION['evento'])) {
    // Si no hay evento seleccionado, redirigir al index
    header("Location: index.php?success=true");
    exit;
}

// Importar el archivo de conexión
require_once '../config/conexion.php';

// Función para obtener la fecha y hora actual en formato deseado
function obtenerHoraActual() {
    return date('H:i:s');
}

// Obtener el nombre del evento seleccionado
$nombreEvento = $_SESSION['evento'];

// Verificar si existe el parámetro id_alumno en la URL
if (isset($_GET['id_alumno'])) {
    $id_alumno = $_GET['id_alumno'];
    $horaActual = obtenerHoraActual();
    $asistenciaRegistrada = false;
    $mensajeAsistencia = "";
    $colorFondo = "";
    
    try {
        // Obtener los datos actuales de asistencia filtrados por el nombre del evento
        $stmt = $pdo->prepare("SELECT asistencia FROM evento WHERE nombre = :nombreEvento");
        $stmt->bindParam(':nombreEvento', $nombreEvento);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            // Decodificar el JSON de asistencia
            $asistencia = json_decode($resultado['asistencia'], true);
            
            // Verificar si el id_alumno existe en la asistencia
            if (isset($asistencia[$id_alumno])) {
                // Verificar si ya tiene hora de entrada registrada
                if (isset($asistencia[$id_alumno][0]) && $asistencia[$id_alumno][0] !== "") {
                    // Si tiene hora de entrada, registrar hora de salida
                    $asistencia[$id_alumno][1] = $horaActual;
                    $mensajeAsistencia = "Salida registrada correctamente";
                } else {
                    // Si no tiene hora de entrada, registrarla
                    $asistencia[$id_alumno][0] = $horaActual;
                    $mensajeAsistencia = "Entrada registrada correctamente";
                }
                
                // Actualizar el registro en la base de datos
                $asistenciaJSON = json_encode($asistencia);
                $stmt = $pdo->prepare("UPDATE evento SET asistencia = :asistencia WHERE nombre = :nombreEvento");
                $stmt->bindParam(':asistencia', $asistenciaJSON);
                $stmt->bindParam(':nombreEvento', $nombreEvento);
                $stmt->execute();
                
                $asistenciaRegistrada = true;
                $colorFondo = "#4CAF50"; // Verde
            } else {
                // El id_alumno no existe en la asistencia
                $mensajeAsistencia = "Asistencia incorrecta, no puede pasar";
                $colorFondo = "#F44336"; // Rojo
            }
        } else {
            // No se encontró registro de evento
            $mensajeAsistencia = "Error: No se encontró el evento '{$nombreEvento}'";
            $colorFondo = "#F44336"; // Rojo
        }
    } catch (PDOException $e) {
        // Error en la base de datos
        $mensajeAsistencia = "Error al procesar la asistencia";
        $colorFondo = "#F44336"; // Rojo
        error_log("Error en entrada.php: " . $e->getMessage());
    }
} else {
    // No se proporcionó id_alumno
    $mensajeAsistencia = "Error: No se proporcionó identificación de alumno";
    $colorFondo = "#F44336"; // Rojo
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia - <?php echo htmlspecialchars($nombreEvento); ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: <?php echo $colorFondo; ?>;
            transition: background-color 0.5s ease;
        }
        
        .mensaje-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            max-width: 90%;
            width: 400px;
        }
        
        .icono {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .mensaje {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: <?php echo $asistenciaRegistrada ? '#4CAF50' : '#F44336'; ?>;
        }
        
        .detalles {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }
        
        .evento {
            font-size: 16px;
            color: #3498db;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .boton {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .cambiar {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .tiempo {
            font-size: 14px;
            color: #777;
        }
        
        @media (max-width: 480px) {
            .mensaje-container {
                padding: 20px;
            }
            
            .mensaje {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="mensaje-container">
        <div class="icono">
            <?php echo $asistenciaRegistrada ? '✓' : '✗'; ?>
        </div>
        <div class="evento">
            Evento: <?php echo htmlspecialchars($nombreEvento); ?>
        </div>
        <div class="mensaje">
            <?php 
            if ($asistenciaRegistrada) {
                echo "Asistencia correcta: puede pasar";
            } else {
                echo $mensajeAsistencia;
            }
            ?>
        </div>
        <?php if ($asistenciaRegistrada): ?>
        <div class="detalles">
            ID: <?php echo htmlspecialchars($id_alumno); ?>
        </div>
        <div class="tiempo">
            Registrado a las <?php echo $horaActual; ?>
        </div>
        <?php endif; ?>
        <div class="boton">
            <a class="cambiar" href="index.php">Cambiar Evento</a>
        </div>
    </div>
</body>
</html>
