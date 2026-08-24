<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/facturas');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$efectivoRaw = $_POST['efectivo'] ?? '';
$efectivo = $efectivoRaw !== '' ? (float) $efectivoRaw : null;
$transferenciaRaw = $_POST['transferencia'] ?? '';
$transferencia = $transferenciaRaw !== '' ? (float) $transferenciaRaw : null;
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

if ($id <= 0 || $nombre === '' || $detalleItems === []) {
    redireccionar('panel/facturas');
}

$detalle = implode(', ', $detalleItems);

$stmt = $db->prepare(
    "UPDATE facturas
    SET nombre = ?, detalle = ?, total = ?, efectivo = ?, transferencia = ?, deuda = ?
    WHERE id = ?"
);
$stmt->bind_param('ssddddi', $nombre, $detalle, $total, $efectivo, $transferencia, $deuda, $id);
$stmt->execute();
$stmt->close();

redireccionar('panel/facturas');
