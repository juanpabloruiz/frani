<?php
include('conexion.php');
$detalle = $_POST['detalle'];
$cantidad = $_POST['cantidad'];
$precio = $_POST['precio'];
$metodo = $_POST['metodo'];
mysqli_query($conexion, "INSERT INTO ventas (detalle, cantidad, precio, metodo) VALUES ('$detalle', '$cantidad', '$precio', '$metodo')");
header('location:ventas');
?>