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
$deudaRaw = $_POST['deuda'] ?? '';
$deuda = $deudaRaw !== '' && $deudaRaw !== '0' ? (float) $deudaRaw : null;
$detalleItems = [];
$productosVendidos = [];

$db = conexion();
$stmtProducto = $db->prepare("SELECT producto FROM productos WHERE id = ?");
$stmtStockCheck = $db->prepare("SELECT stock FROM productos WHERE id = ? AND stock IS NOT NULL");

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

    $stmtStockCheck->bind_param('i', $idProducto);
    $stmtStockCheck->execute();
    $stmtStockCheck->bind_result($stockActual);
    if ($stmtStockCheck->fetch() && $cantidad > $stockActual) {
        redireccionar('panel/facturas/nueva');
    }
    $stmtStockCheck->free_result();

    $stmtProducto->bind_param('i', $idProducto);
    $stmtProducto->execute();
    $stmtProducto->bind_result($nombreProducto);

    if ($stmtProducto->fetch()) {
        $detalleItems[] = sprintf('%s (%d x %.2f)', $nombreProducto, $cantidad, $precio);
        $productosVendidos[] = ['id' => $idProducto, 'cantidad' => $cantidad];
    }

    $stmtProducto->free_result();
}

$stmtProducto->close();
$stmtStockCheck->close();

if ($nombre === '' || $metodo === '' || $detalleItems === []) {
    redireccionar('panel/facturas/nueva');
}

$detalle = implode(', ', $detalleItems);

$stmt = $db->prepare("INSERT INTO facturas (nombre, metodo, total, deuda, detalle) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssdds', $nombre, $metodo, $total, $deuda, $detalle);
$stmt->execute();
$stmt->close();

$stmtStock = $db->prepare("UPDATE productos SET stock = stock - ?, modificado = NOW() WHERE id = ? AND stock IS NOT NULL");
foreach ($productosVendidos as $prod) {
    $stmtStock->bind_param('ii', $prod['cantidad'], $prod['id']);
    $stmtStock->execute();
}
$stmtStock->close();

redireccionar('panel/facturas');
