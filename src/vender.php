<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$detalle = trim($_POST['detalle'] ?? '');
$cantidad = (int) ($_POST['cantidad'] ?? 0);
$precio = (float) numero($_POST['precio'] ?? '0');
$metodo = trim($_POST['metodo'] ?? '');

if ($detalle !== '' && $cantidad > 0 && $metodo !== '') {
    $sentencia = $conexion->prepare(
        "INSERT INTO ventas (detalle, cantidad, precio, metodo) VALUES (?, ?, ?, ?)"
    );
    $sentencia->bind_param('sids', $detalle, $cantidad, $precio, $metodo);
    $sentencia->execute();
    $sentencia->close();
}

redireccionar('ventas');
