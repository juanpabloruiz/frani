<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/mascotas');
}

verificar_CSRF();

$tipo = trim($_POST['tipo'] ?? '');
$talle = trim($_POST['talle'] ?? '');
$precio = (float) ($_POST['precio'] ?? '0');

if ($tipo === '') {
    redireccionar('panel/mascotas');
}

$db = conexion();
$talleDB = $talle !== '' ? $talle : null;

$stmt = $db->prepare(
    "INSERT INTO mascotas (tipo, talle, precio)
    VALUES (?, ?, ?)"
);
$stmt->bind_param('ssd', $tipo, $talleDB, $precio);
$stmt->execute();
$stmt->close();

respaldar_bd();

redireccionar('panel/mascotas');