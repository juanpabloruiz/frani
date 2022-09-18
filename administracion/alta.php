<?php
include('conexion.php');
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
$producto = $_POST['producto'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
$status = ($_POST['status'] == 1) ? 1 : 0;
mysqli_query($conexion, "INSERT INTO productos (producto, precio, costo, status) VALUES ('$producto', '$precio', '$costo', '$status')");
echo '<script>window.location="./"</script>';
?>