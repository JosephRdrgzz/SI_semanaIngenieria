<?php
require_once __DIR__ . '/../config/conexion.php';

// Función para verificar si el expediente existe en la base de datos
function verificarUsuario($exp) {
    global $pdo;
    $sql = "SELECT exp, nombre FROM alumnos WHERE exp = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$exp]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve el usuario si existe
}
?>
