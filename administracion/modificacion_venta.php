<?php
session_start();
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
include('conexion.php');
$id = $_POST['id'];
if ($_POST['producto'] == 'Elegir producto') {
    $producto = $_POST['accesorio'];
    $unitario = $_POST['precio'];
} else {
    $producto = $_POST['producto'];
    $consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE producto = '$producto'");
    $precio = mysqli_fetch_array($consulta);
    $unitario = $precio['precio'];
}
$cantidad = $_POST['cantidad'];
$total = $unitario * $cantidad;
mysqli_query($conexion, "UPDATE ventas SET producto = '$producto', cantidad = '$cantidad', unitario = '$unitario', total = '$total', registro = NOW() WHERE id = '$id'");
echo '<script>window.location="ventas"</script>';
?>