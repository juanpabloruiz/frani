<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/productos');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$producto = trim($_POST['producto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$stock = $_POST['stock'] !== '' && $_POST['stock'] !== null ? (int) $_POST['stock'] : null;
$costo = (float) ($_POST['costo'] ?? '0');
$precio = (float) ($_POST['precio'] ?? '0');
$idCategoria = (int) ($_POST['id_categoria'] ?? 0);

if ($id <= 0 || $producto === '' || $idCategoria <= 0) {
    redireccionar('panel/productos');
}

$db = conexion();
$descripcionDB = $descripcion !== '' ? $descripcion : null;

$stmt = $db->prepare(
    "UPDATE productos
    SET producto = ?, descripcion = ?, costo = ?, precio = ?, stock = ?, id_categoria = ?
    WHERE id = ?"
);
$stmt->bind_param('ssddiii', $producto, $descripcionDB, $costo, $precio, $stock, $idCategoria, $id);
$stmt->execute();
$stmt->close();

redireccionar('panel/productos');
