<?php
include('conexion.php');

// Obtener datos del formulario
$nombre = $_POST['nombre'];
$metodo = $_POST['metodo'];
$total = $_POST['total'];

// Construir el detalle de los ítems
$detalle = ''; // Cadena para almacenar el detalle
foreach ($_POST as $key => $value) {
    if (strpos($key, 'producto_') === 0) {
        // Extraer el índice del ítem
        $index = str_replace('producto_', '', $key);

        // Obtener los datos de este ítem
        $id_producto = $_POST["producto_$index"];
        $cantidad = $_POST["cantidad_$index"];
        $precio = $_POST["precio_$index"];

        // Consultar el nombre del producto desde la base de datos
        $sqlProducto = "SELECT producto FROM productos WHERE id = ?";
        $stmtProducto = $conexion->prepare($sqlProducto);
        $stmtProducto->bind_param('i', $id_producto);
        $stmtProducto->execute();
        $stmtProducto->bind_result($nombre_producto);
        $stmtProducto->fetch();
        $stmtProducto->close();

        // Concatenar en formato "Nombre del Producto (Cantidad x Precio)"
        $detalle .= "$nombre_producto ($cantidad x $precio), ";
    }
}

// Eliminar la última coma y espacio
$detalle = rtrim($detalle, ', ');

// Guardar en la base de datos
$sql = "INSERT INTO facturas (nombre, metodo, total, detalle) VALUES (?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('ssis', $nombre, $metodo, $total, $detalle);

if ($stmt->execute()) {
    echo "Factura guardada correctamente.";
} else {
    echo "Error al guardar la factura: " . $stmt->error;
}

$stmt->close();
$conexion->close();

header('Location: ./');
exit;

?>
