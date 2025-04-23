<?php
// Iniciar sesión para mantener el estado del usuario
session_start();

// Importar el archivo de conexión
require_once '../config/conexion.php';
// Nota: La conexión $pdo ya está disponible desde el archivo importado

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y sanear los datos enviados
    $correo = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // No sanear contraseñas para evitar modificarlas
    
    // Validar el correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        // Redirigir con error si el correo no es válido
        header("Location: index.php?success=false");
        exit;
    }
    
    // Verificar que el correo termine con @anahuac.mx
    if (!preg_match('/@anahuac\.mx$/', $correo)) {
        // Redirigir con error si el correo no pertenece al dominio anahuac.mx
        header("Location: index.php?success=false");
        exit;
    }
    
    try {
        // Preparar la consulta para buscar el usuario
        $stmt = $pdo->prepare("SELECT * FROM entradas WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        
        // Verificar si existe el usuario
        if ($stmt->rowCount() === 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar la contraseña
            // Nota: Asumiendo que la contraseña está en texto plano en la BD según tu esquema
            // En un entorno de producción, siempre deberías usar password_hash/password_verify
            if ($usuario['contraseña'] === $password) {
                // Login exitoso
                $_SESSION['registrar_entrada'] = true;
                $_SESSION['correo'] = $correo;
                
                // Redirigir al index con success=true
                header("Location: index.php?success=true");
                exit;
            }
        }
        
        // Si llegamos aquí, el login falló
        header("Location: index.php?success=false");
        exit;
        
    } catch (PDOException $e) {
        // En caso de error en la consulta
        // En un entorno de producción, este error debería ser registrado, no mostrado
        error_log("Error en login: " . $e->getMessage());
        header("Location: index.php?success=false");
        exit;
    }
} else {
    // Si no es una solicitud POST, redirigir a la página principal
    header("Location: index.php");
    exit;
}
?>
