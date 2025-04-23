<?php
// Iniciar sesión para mantener el estado del usuario
session_start();

// Verificar si hay un parámetro de success en la URL
$success = isset($_GET['success']) ? $_GET['success'] : '';

// Procesar la selección de evento si viene como POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['evento_seleccionado'])) {
    $_SESSION['evento'] = $_POST['evento_seleccionado'];
    // No redireccionamos, simplemente guardamos en sesión y continuamos
}

// Importar el archivo de conexión solo si estamos mostrando el selector de eventos
$eventos = [];
if ($_SESSION['registrar_entrada']) {
    require_once '../config/conexion.php';
    try {
        // Obtener todos los eventos de la base de datos
        $stmt = $pdo->prepare("SELECT nombre FROM evento ORDER BY nombre");
        $stmt->execute();
        $eventos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log("Error al cargar eventos: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Login - Ingeniería</title>
    <style>
        #loginForm {
            <?php if ($_SESSION['registrar_entrada']) { echo "display: none;"; } ?>
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .btn {
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 12px 15px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 15px;
            text-align: center;
            display: none;
        }
        
        .success-container {
            text-align: center;
            <?php if (!$_SESSION['registrar_entrada']) { echo "display: none;"; } ?>
        }
        
        .success-container h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .success-container .icon {
            font-size: 60px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        
        .evento-selector {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .evento-actual {
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 10px;
            margin-top: 15px;
            text-align: center;
            color: #2c3e50;
        }
        
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                width: 100%;
                border-radius: 0;
                box-shadow: none;
                padding: 15px;
            }
            
            .header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="loginForm" style="<?php echo ($success === 'true') ? 'display: none;' : ''; ?>">
            <div class="header">
                <h1>Acceso al Sistema</h1>
                <p>Portal de Ingeniería</p>
            </div>
            
            <form id="formLogin" action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@anahuac.mx" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn" id="submitBtn" disabled>Iniciar Sesión</button>
                
                <?php if ($success === 'false'): ?>
                <div class="error-message" style="display: block;">
                    Correo electrónico o contraseña incorrectos.
                </div>
                <?php endif; ?>
            </form>
        </div>
        
        <div id="successContainer" class="success-container" style="<?php echo ($success === 'true') ? 'display: block;' : ''; ?>">
            <div class="icon">✓</div>
            <h2>Listo para escanear QRs</h2>
            
            <!-- Selector de eventos -->
            <div class="evento-selector">
                <form id="formEvento" action="index.php?success=true" method="post">
                    <div class="form-group">
                        <label for="evento_seleccionado">Seleccione un Evento:</label>
                        <select class="form-control" id="evento_seleccionado" name="evento_seleccionado" required>
                            <option value="">-- Seleccionar Evento --</option>
                            <?php foreach ($eventos as $evento): ?>
                            <option value="<?php echo htmlspecialchars($evento); ?>" <?php echo (isset($_SESSION['evento']) && $_SESSION['evento'] === $evento) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($evento); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn">Seleccionar Evento</button>
                </form>
                
                <?php if (isset($_SESSION['evento'])): ?>
                <div class="evento-actual">
                    <strong>Evento actual:</strong> <?php echo htmlspecialchars($_SESSION['evento']); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Función para validar el formulario
        function validateForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (email.trim() !== '' && password.trim() !== '') {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        }
        
        // Agregar eventos de escucha para los campos
        document.getElementById('email').addEventListener('input', validateForm);
        document.getElementById('password').addEventListener('input', validateForm);
        
        // Comprobar estado inicial del formulario
        document.addEventListener('DOMContentLoaded', validateForm);
    </script>
</body>
</html>
