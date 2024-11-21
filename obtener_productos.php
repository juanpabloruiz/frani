<?php
include('conexion.php');
$query = "SELECT id, producto, precio FROM productos";
$result = mysqli_query($conexion, $query);

$productos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $productos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($productos);
?>
