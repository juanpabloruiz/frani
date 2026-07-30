<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/productos');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    redireccionar('panel/productos');
}

$db = conexion();

$stmt = $db->prepare("DELETE FROM productos WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

redireccionar('panel/productos');
