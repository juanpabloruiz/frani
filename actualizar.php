<?php
include('conexion.php');
$id = $_POST['id'];
$producto = $_POST['producto'];
$stock = $_POST['stock'];
$categoria = $_POST['categoria'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
mysqli_query($conexion, "UPDATE productos SET producto = '$producto', stock = '$stock', categoria = '$categoria', precio = '$precio', costo = '$costo', fecha = NOW() WHERE id = '$id'");
header('location:./');
?>