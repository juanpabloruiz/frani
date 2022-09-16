<?php
include('conexion.php');
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
$producto = $_POST['producto'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
$estado = $_POST['estado'];
mysqli_query($conexion, "INSERT INTO productos (producto, precio, costo, estado) VALUES ('$producto', '$precio', '$costo', '$estado')");
echo '<script>window.location="./"</script>';
?>