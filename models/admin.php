<?php
require_once __DIR__ . '/../config/conexion.php';

/**
 * Verifica si un expediente pertenece a un administrador
 */
function verificarAdmin($exp) {
    global $pdo;
    $query = $pdo->prepare("SELECT COUNT(*) FROM administradores WHERE exp = :exp");
    $query->execute(['exp' => $exp]);
    return $query->fetchColumn() > 0;
}

/**
 * Verifica las credenciales de un administrador
 */
function verificarCredencialesAdmin($exp, $contraseña) {
    global $pdo;
    $query = $pdo->prepare("SELECT exp, nombre, contraseña FROM administradores WHERE exp = :exp");
    $query->execute(['exp' => $exp]);
    $admin = $query->fetch(PDO::FETCH_ASSOC);

    if ($admin && hash('sha256', $contraseña) === $admin['contraseña']) {
        return ["exp" => $admin['exp'], "nombre" => $admin['nombre']];
    }

    return false;
}
?>
