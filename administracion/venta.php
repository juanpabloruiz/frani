<?php
include('conexion.php');
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
$producto = $_POST['producto'];
$cantidad = $_POST['cantidad'];
$consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE producto = '$producto'");
$campo = mysqli_fetch_array($consulta);
$unitario = $campo['precio'];
$total = $unitario * $cantidad;
mysqli_query($conexion, "INSERT INTO ventas (producto, cantidad, unitario, total, registro) VALUES ('$producto', '$cantidad', '$unitario', '$total', NOW())");
echo '<script>window.location="ventas"</script>';
?>