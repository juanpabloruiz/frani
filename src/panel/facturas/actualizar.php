<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/facturas');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$metodo = trim($_POST['metodo'] ?? '');
$total = (float) ($_POST['total'] ?? '0');
$deudaRaw = $_POST['deuda'] ?? '';
$deuda = $deudaRaw !== '' && $deudaRaw !== '0' ? (float) $deudaRaw : null;
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

if ($id <= 0 || $nombre === '' || $metodo === '' || $detalleItems === []) {
    redireccionar('panel/facturas');
}

$detalle = implode(', ', $detalleItems);

$stmt = $db->prepare(
    "UPDATE facturas
    SET nombre = ?, metodo = ?, detalle = ?, total = ?, deuda = ?
    WHERE id = ?"
);
$stmt->bind_param('sssddi', $nombre, $metodo, $detalle, $total, $deuda, $id);
$stmt->execute();
$stmt->close();

redireccionar('panel/facturas');
