<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/facturas');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');
$observacionesDB = $observaciones !== '' ? $observaciones : null;
$efectivo1 = ($_POST['efectivo'] ?? '') !== '' ? (float) $_POST['efectivo'] : 0;
$efectivo2 = ($_POST['efectivo2'] ?? '') !== '' ? (float) $_POST['efectivo2'] : 0;
$transf1 = ($_POST['transferencia'] ?? '') !== '' ? (float) $_POST['transferencia'] : 0;
$transf2 = ($_POST['transferencia2'] ?? '') !== '' ? (float) $_POST['transferencia2'] : 0;
$efectivo = $efectivo1 + $efectivo2;
$transferencia = $transf1 + $transf2;
$total = (float) ($_POST['total'] ?? '0');
$deuda = $total - $efectivo - $transferencia;
if ($deuda <= 0) {
    $deuda = null;
}
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

if ($id <= 0 || $detalleItems === []) {
    redireccionar('panel/facturas');
}

$detalle = implode(', ', $detalleItems);

$stmt = $db->prepare(
    "UPDATE facturas
    SET nombre = ?, detalle = ?, total = ?, efectivo = ?, transferencia = ?, deuda = ?, observaciones = ?
    WHERE id = ?"
);
$stmt->bind_param('ssddddss', $nombre, $detalle, $total, $efectivo, $transferencia, $deuda, $observacionesDB, $id);
$stmt->execute();
$stmt->close();

respaldar_bd();

redireccionar('panel/facturas');
