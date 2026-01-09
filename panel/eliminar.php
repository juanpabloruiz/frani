<?php
include('conexion.php');

// Verificar si se enviaron IDs
if (isset($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = $_POST['ids'];

    // Convertir el array de IDs a una lista separada por comas
    $ids_list = implode(',', array_map('intval', $ids));

    // Eliminar los productos seleccionados
    $sql = "DELETE FROM productos WHERE id IN ($ids_list)";
    if (mysqli_query($conexion, $sql)) {
        echo "Productos eliminados correctamente.";
    } else {
        echo "Error al eliminar los productos: " . mysqli_error($conexion);
    }
} else {
    echo "No se seleccionaron productos.";
}
header('Location:./');
?>
