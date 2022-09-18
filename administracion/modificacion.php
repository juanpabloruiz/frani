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
$costo = $_POST['costo'];
if ($_POST['estado'] == 'on') {
    $estado = 'publico';
} else {
    $estado = 'privado';
}
mysqli_query($conexion, "UPDATE productos SET producto = '$producto', precio = '$precio', costo = '$costo', estado = '$estado' WHERE id = '$id'");
echo '<script>window.location="./"</script>';
?>