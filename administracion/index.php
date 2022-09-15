<?php
session_start();
if (isset($_SESSION['correo'])) {
    echo '<script>window.location="panel"</script>';
}
include('conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración</title>
</head>
<body>
    <form method="post" action="ingreso">
        <input type="email" name="correo">
        <input type="password" name="clave">
        <input type="submit" name="ingreso">
    </form>
</body>
</html>