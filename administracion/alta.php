<?php
session_start();
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
include('conexion.php');
$producto = $_POST['producto'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
mysqli_query($conexion, "INSERT INTO productos (producto, precio, costo) VALUES ('$producto', '$precio', '$costo')");
echo '<script>window.location="./"</script>';
?>