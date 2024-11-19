<?php
include('conexion.php');
$id = $_GET['id'];
mysqli_query($conexion, "DELETE FROM productos WHERE id = '$id'");
header('location:./');
?>