<?php
include('conexion.php');

// Datos de la factura
$nombre = $_POST['nombre'];
$metodo = $_POST['metodo'];
$total = $_POST['total'];

// Insertar factura
$query = "INSERT INTO facturas (nombre, metodo, total) VALUES ('$nombre', '$metodo', '$total')";
mysqli_query($conexion, $query);

// Obtener el ID de la factura creada
$id_factura = mysqli_insert_id($conexion);

// Insertar ítems
$items = $_POST['items'];
foreach ($items as $item) {
    $id_producto = $item['id'];
    $cantidad = $item['cantidad'];
    $precio = $item['precio'];
    $subtotal = $item['subtotal'];

    $query = "INSERT INTO items_factura (id_factura, detalle, cantidad, precio, subtotal)
              VALUES ('$id_factura', (SELECT nombre FROM productos WHERE id = '$id_producto'), '$cantidad', '$precio', '$subtotal')";
    mysqli_query($conexion, $query);
}

header('Location: ./');
?>
