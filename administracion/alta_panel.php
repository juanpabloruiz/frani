<?php
include('conexion.php');
if (!isset($_SESSION['correo'])) {
    echo '<script>window.location="./"</script>';
}
$producto = $_POST['producto'];
$extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
$foto = mt_rand() . "." . $extension;
$carpeta = '../imagenes/';
move_uploaded_file($_FILES['foto']['tmp_name'], $carpeta . $foto);
$precio = $_POST['precio'];
$costo = $_POST['costo'];
if ($_POST['estado'] == 'on') {
    $estado = 'publico';
} else {
    $estado = 'privado';
}
mysqli_query($donweb, "INSERT INTO productos (producto, foto, precio, costo, estado) VALUES ('$producto', 'muestra.jpg', '$precio', '$costo', '$estado')");
mysqli_query($conexion, "INSERT INTO productos (producto, foto, precio, costo, estado) VALUES ('$producto', 'muestra.jpg', '$precio', '$costo', '$estado')");
echo '<script>window.location="./"</script>';
?>