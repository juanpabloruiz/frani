<?php
session_start();
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
include('conexion.php');
$id = $_POST['id'];
$producto = $_POST['producto'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
$status = ($_POST['status'] == 1) ? 1 : 0;
mysqli_query($conexion, "UPDATE productos SET producto = '$producto', precio = '$precio', costo = '$costo', status = '$status' WHERE id = '$id'");
echo '<script>window.location="./"</script>';
?>