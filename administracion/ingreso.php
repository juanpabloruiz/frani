<?php
include('conexion.php');
$correo = $_POST['correo'];
$clave = $_POST['clave'];
$consulta = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo = '$correo'");
$campo = mysqli_fetch_array($consulta);
if ($correo !== $campo['correo'] or $clave !== $campo['clave']) {
    echo 'Error';
} else {
    $_SESSION['correo'] = $correo;
    echo '<script>window.location="panel"</script>';
}
?>