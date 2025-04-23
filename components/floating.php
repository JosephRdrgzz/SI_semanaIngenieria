<?php 
   if (isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != ""):
      $id_usuario = $_SESSION['id_usuario']; // Guardamos el ID del usuario
      $qr_url = "https://api.qrserver.com/v1/create-qr-code/?data=https://sising.anahuac.mx/semana/entrada/entrada.php?id_alumno=$id_usuario";
?>

<div class="floating-button" onclick="toggleQR()">
    <img src="<?php echo $qr_url; ?>" alt="QR Code">
</div>

<?php 
   endif;
?>

