<?php
$passIngresada = "admin456";  // Usa la contraseña que estás ingresando en el login
$hashGenerado = hash('sha256', $passIngresada);

echo "Hash generado para 'admin456': " . $hashGenerado;
?>
