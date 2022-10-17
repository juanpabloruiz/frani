<?php
include('conexion.php');
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
if ($_POST['producto'] == 'Elegir producto') {
    $producto = $_POST['accesorio'];
} else {
    $producto = $_POST['producto'];
}
$cantidad = $_POST['cantidad'];
$consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE producto = '$producto'");
$campo = mysqli_fetch_array($consulta);
if ($_POST['precio'] == NULL) {
    $unitario = $campo['precio'];
} else {
    $unitario = $_POST['precio'];
}
$total = $unitario * $cantidad;
mysqli_query($conexion, "INSERT INTO ventas (producto, cantidad, unitario, total, registro) VALUES ('$producto', '$cantidad', '$unitario', '$total', NOW())");
echo '<script>window.location="ventas"</script>';
?>