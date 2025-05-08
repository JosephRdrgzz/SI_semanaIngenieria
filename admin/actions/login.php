<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1) Verificar campo usuario
if (empty(trim($_POST['usuario']))) {
    $_SESSION['error'] = "Se requiere ID o correo.";
    header("Location: ../index.php?view=login");
    exit();
}
$usuario = trim($_POST['usuario']);
$isEmail = strpos($usuario, '@') !== false;

// 2) Intentar login como alumno
if ($isEmail) {
    $sql = "SELECT exp,nombre,mail,campus,semestre,celular,telefono,responsable
            FROM alumnos WHERE mail = :u";
} else {
    $sql = "SELECT exp,nombre,mail,campus,semestre,celular,telefono,responsable
            FROM alumnos WHERE exp = :u";
}
$stmt = $pdo->prepare($sql);
$stmt->execute(['u' => $usuario]);
if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $_SESSION['id_usuario']   = $user['exp'];
    $_SESSION['nombre']       = $user['nombre'];
    $_SESSION['tipo_usuario'] = 'alumno';
    header("Location: ../index.php?view=home");
    exit;
}

// 3) Intentar login como administrador
if ($isEmail) {
    $sql = "SELECT exp,nombre,correo,contraseña,is_super
            FROM administradores WHERE correo = :u";
} else {
    $sql = "SELECT exp,nombre,correo,contraseña,is_super
            FROM administradores WHERE exp = :u";
}
$stmt = $pdo->prepare($sql);
$stmt->execute(['u' => $usuario]);
$adm = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$adm) {
    $_SESSION['error'] = "Usuario no encontrado.";
    header("Location: ../index.php?view=login");
    exit;
}

// 4) Verificar contraseña
if (empty(trim($_POST['contraseña']))) {
    $_SESSION['error'] = "Contraseña requerida.";
    header("Location: ../index.php?view=login");
    exit;
}
$hashIn = hash('sha256', trim($_POST['contraseña']));
if ($hashIn !== $adm['contraseña']) {
    $_SESSION['error'] = "Contraseña incorrecta.";
    header("Location: ../index.php?view=login");
    exit;
}

// 5) Sesión de administrador
$_SESSION['id_usuario']   = $adm['exp'];
$_SESSION['nombre']       = $adm['nombre'];
$_SESSION['tipo_usuario'] = 'admin';               // SIEMPRE 'admin'
$_SESSION['is_super']     = (bool)$adm['is_super']; // true/false limpio

// 6) Cargar permisos
if ($_SESSION['is_super']) {
    // Súper-admin ve TODO
    $_SESSION['permisos'] = $pdo
      ->query("SELECT nombre FROM vistas")
      ->fetchAll(PDO::FETCH_COLUMN);
} else {
    // Admin normal: sólo los suyos
    $p = $pdo->prepare("
      SELECT v.nombre
        FROM vistas v
        JOIN admin_vistas av ON av.vista_id = v.id
       WHERE av.admin_exp = :e
    ");
    $p->execute(['e' => $adm['exp']]);
    $_SESSION['permisos'] = $p->fetchAll(PDO::FETCH_COLUMN);
}

// 7) Redirigir al panel
header("Location: ../index.php?view=panel_admin");
exit;
