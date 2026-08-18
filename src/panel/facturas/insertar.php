<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/facturas');
}

verificar_CSRF();

$nombre = trim($_POST['nombre'] ?? '');
$metodo = trim($_POST['metodo'] ?? '');
$total = (float) ($_POST['total'] ?? '0');
$detalleItems = [];

$db = conexion();
$stmtProducto = $db->prepare("SELECT producto FROM productos WHERE id = ?");

foreach ($_POST as $key => $value) {
    if (strpos($key, 'producto_') !== 0) {
        continue;
    }

    $index = str_replace('producto_', '', $key);
    $idProducto = (int) ($_POST["producto_{$index}"] ?? 0);
    $cantidad = (int) ($_POST["cantidad_{$index}"] ?? 0);
    $precio = (float) ($_POST["precio_{$index}"] ?? '0');

    if ($idProducto <= 0 || $cantidad <= 0) {
        continue;
    }

    $stmtProducto->bind_param('i', $idProducto);
    $stmtProducto->execute();
    $stmtProducto->bind_result($nombreProducto);

    if ($stmtProducto->fetch()) {
        $detalleItems[] = sprintf('%s (%d x %.2f)', $nombreProducto, $cantidad, $precio);
    }

    $stmtProducto->free_result();
}

$stmtProducto->close();

if ($nombre === '' || $metodo === '' || $detalleItems === []) {
    redireccionar('panel/facturas/nueva');
}

$detalle = implode(', ', $detalleItems);

$stmt = $db->prepare("INSERT INTO facturas (nombre, metodo, total, detalle) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssds', $nombre, $metodo, $total, $detalle);
$stmt->execute();
$stmt->close();

redireccionar('panel/facturas');
