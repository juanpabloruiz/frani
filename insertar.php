<?php
include('conexion.php');
$producto = $_POST['producto'];
$categoria = $_POST['categoria'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
mysqli_query($conexion, "INSERT INTO productos (producto, categoria, precio, costo, fecha) VALUES ('$producto', '$categoria', '$precio', '$costo', NOW())");
header('location:./');
?>