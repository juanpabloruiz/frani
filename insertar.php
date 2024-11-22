<?php
include('conexion.php');
$producto = $_POST['producto'];
$stock = $_POST['stock'];
$categoria = $_POST['categoria'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
mysqli_query($conexion, "INSERT INTO productos (producto, stock, categoria, precio, costo, fecha) VALUES ('$producto', '$stock', '$categoria', '$precio', '$costo', NOW())");
header('location:./');
?>