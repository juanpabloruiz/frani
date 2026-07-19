<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/categorias');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');

if ($id <= 0 || $nombre === '') {
    redireccionar('panel/categorias');
}

$db = conexion();

$stmt = $db->prepare("UPDATE categorias SET nombre = ? WHERE id = ?");
$stmt->bind_param('si', $nombre, $id);
$stmt->execute();
$stmt->close();

redireccionar('panel/categorias');
