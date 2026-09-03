<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/clientes');
}

verificar_CSRF();

$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if ($nombre === '') {
    redireccionar('panel/clientes');
}

$db = conexion();
$telefonoDB = $telefono !== '' ? $telefono : null;

$foto = null;
if (!empty($_FILES['foto']['name'])) {
    $directorio = __DIR__ . '/../../img/clientes';
    $foto = subir_foto($_FILES['foto'], $directorio);
}

$stmt = $db->prepare(
    "INSERT INTO clientes (nombre, telefono, foto)
    VALUES (?, ?, ?)"
);
$stmt->bind_param('sss', $nombre, $telefonoDB, $foto);
$stmt->execute();
$stmt->close();

respaldar_bd();

redireccionar('panel/clientes');
