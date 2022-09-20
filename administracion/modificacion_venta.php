<?php
session_start();
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
include('conexion.php');
$id = $_POST['id'];
$producto = $_POST['producto'];
$cantidad = $_POST['cantidad'];
$consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE producto = '$producto'");
$campo = mysqli_fetch_array($consulta);
$unitario = $campo['precio'];
$total = $unitario * $cantidad;
mysqli_query($conexion, "UPDATE ventas SET producto = '$producto', cantidad = '$cantidad', unitario = '$unitario', total = '$total', registro = NOW() WHERE id = '$id'");
echo '<script>window.location="ventas"</script>';
?>