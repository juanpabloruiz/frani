<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/categorias');
}

verificar_CSRF();

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre === '') {
    redireccionar('panel/categorias/nuevo');
}

$db = conexion();

$stmt = $db->prepare("INSERT IGNORE INTO categorias (nombre) VALUES (?)");
$stmt->bind_param('s', $nombre);
$stmt->execute();
$stmt->close();

redireccionar('panel/categorias');
