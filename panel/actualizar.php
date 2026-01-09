<?php
include('conexion.php');
$id = $_POST['id'];
$producto = $_POST['producto'];
$stock = $_POST['stock'];
$categoria = isset($_POST['categoria']) ? $_POST['categoria'] : NULL;
$precio = $_POST['precio'];
$costo = $_POST['costo'];
$stmt = $conexion->prepare("UPDATE productos SET producto = ?, stock = ?, categoria = ?, precio = ?, costo = ?, fecha = NOW() WHERE id = ?");
$stmt->bind_param('sissdi', $producto, $stock, $categoria, $precio, $costo, $id);
$stmt->execute();
header('location:./');
?>