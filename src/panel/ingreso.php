<?php
require_once __DIR__ . '/../conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel');
}

verificar_CSRF();

$correo = trim($_POST['correo'] ?? '');
$clave = $_POST['clave'] ?? '';

if ($correo === '' || $clave === '') {
    redireccionar('panel?error=Complete todos los campos.');
}

$db = conexion();
$stmt = $db->prepare("SELECT id, nombre, clave FROM usuarios WHERE correo = ?");
$stmt->bind_param('s', $correo);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if ($usuario === null || !password_verify($clave, $usuario['clave'])) {
    redireccionar('panel?error=Correo o contraseña incorrectos.');
}

$_SESSION['usuario_id'] = (int) $usuario['id'];
$_SESSION['usuario_nombre'] = $usuario['nombre'];

$update = $db->prepare("UPDATE usuarios SET ingreso = NOW() WHERE id = ?");
$update->bind_param('i', $usuario['id']);
$update->execute();
$update->close();

redireccionar('panel/inicio');
