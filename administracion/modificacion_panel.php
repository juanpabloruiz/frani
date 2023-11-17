<?php
include('conexion.php');
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
$id = $_POST['id'];
$producto = $_POST['producto'];
$extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
$foto = mt_rand() . "." . $extension;
$carpeta = '../imagenes/';
move_uploaded_file($_FILES['foto']['tmp_name'], $carpeta . $foto);
$precio = $_POST['precio'];
$costo = $_POST['costo'];
if (isset($_POST['estado'])) {
    $estado = ($_POST['estado'] == 'on') ? 'publico' : 'privado';
} else {
    $estado = 'privado';
}
$categoria = $_POST['categoria'];
if ($_FILES['foto']['name'] == TRUE) {
    mysqli_query($conexion, "UPDATE productos SET foto = '$foto' WHERE id = '$id'");
}
mysqli_query($conexion, "UPDATE productos SET producto = '$producto', precio = '$precio', costo = '$costo', estado = '$estado', categoria = '$categoria' WHERE id = '$id'");
echo '<script>window.location="./"</script>';
?>