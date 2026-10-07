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
// Los campos de pago vacíos se guardan como NULL, nunca como 0.00.
$efectivo = sumar_montos([$_POST['efectivo'] ?? '', $_POST['efectivo2'] ?? '']);
$transferencia = sumar_montos([$_POST['transferencia'] ?? '', $_POST['transferencia2'] ?? '']);
$descuentoSolicitado = max(0, round((float) (monto_post($_POST['descuento'] ?? '') ?? 0), 2));
$subtotal = 0.0;
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
        $precio = max(0, round($precio, 2));
        $subtotal += $cantidad * $precio;
        $detalleItems[] = sprintf('%s (%d x %.2f)', $nombreProducto, $cantidad, $precio);
    }

    $stmtProducto->free_result();
}

$stmtProducto->close();

if ($id <= 0 || $detalleItems === []) {
    redireccionar('panel/facturas');
}

// Recalcular en el servidor para guardar el mismo importe que muestra el formulario.
$subtotal = round($subtotal, 2);
$descuento = min($subtotal, $descuentoSolicitado);
$total = round(max(0, $subtotal - $descuento), 2);
$descuento = $descuento > 0 ? $descuento : null;
$deuda = round(max(0, $total - (float) $efectivo - (float) $transferencia), 2);
$deuda = $deuda > 0 ? $deuda : null;

$detalle = implode(', ', $detalleItems);

$stmt = $db->prepare(
    "UPDATE facturas
    SET nombre = ?, detalle = ?, total = ?, efectivo = ?, transferencia = ?, deuda = ?, descuento = NULL, descuento_importe = ?, observaciones = ?
    WHERE id = ?"
);
$stmt->bind_param('ssdddddsi', $nombre, $detalle, $total, $efectivo, $transferencia, $deuda, $descuento, $observacionesDB, $id);
$stmt->execute();
$stmt->close();

respaldar_bd();

redireccionar('panel/facturas');
