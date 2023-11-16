<?php
include('conexion.php');
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
$id = $_GET['id'];
mysqli_query($conexion, "DELETE FROM productos WHERE id = '$id'");
echo '<script>window.location="./"</script>';
?>