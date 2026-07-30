<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/productos');
}

verificar_CSRF();

$producto = trim($_POST['producto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$stock = $_POST['stock'] !== '' && $_POST['stock'] !== null ? (int) $_POST['stock'] : null;
$costo = (float) ($_POST['costo'] ?? '0');
$precio = (float) ($_POST['precio'] ?? '0');
$idCategoria = (int) ($_POST['id_categoria'] ?? 0);

if ($producto === '' || $idCategoria <= 0) {
    redireccionar('panel/productos/nuevo');
}

$db = conexion();
$descripcionDB = $descripcion !== '' ? $descripcion : null;

$stmt = $db->prepare(
    "INSERT INTO productos (producto, descripcion, costo, precio, stock, id_categoria)
    VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('ssddii', $producto, $descripcionDB, $costo, $precio, $stock, $idCategoria);
$stmt->execute();
$stmt->close();

redireccionar('panel/productos');
