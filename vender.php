<?php
include('conexion.php');
$detalle = $_POST['detalle'];
$cantidad = $_POST['cantidad'];
$precio = $_POST['precio'];
$metodo = $_POST['metodo'];
mysqli_query($conexion, "INSERT INTO productos (detalle, cantidad, precio, metodo, fecha) VALUES ('$detalle', '$cantidad', '$precio', '$metodo', NOW())");
header('location:./');
?>