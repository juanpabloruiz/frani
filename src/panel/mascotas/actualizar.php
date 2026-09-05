<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/mascotas');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$tipo = trim($_POST['tipo'] ?? '');
$talle = trim($_POST['talle'] ?? '');
$precio = (float) ($_POST['precio'] ?? '0');

if ($id <= 0 || $tipo === '') {
    redireccionar('panel/mascotas');
}

$db = conexion();
$talleDB = $talle !== '' ? $talle : null;

$stmt = $db->prepare(
    "UPDATE mascotas
    SET tipo = ?, talle = ?, precio = ?
    WHERE id = ?"
);
$stmt->bind_param('ssdi', $tipo, $talleDB, $precio, $id);
$stmt->execute();
$stmt->close();

respaldar_bd();

redireccionar('panel/mascotas#mascota-' . $id);