<?php
session_start();
include('conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <h1>Panel</h1>
    <?php
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE id = '$id'");
        $campo = mysqli_fetch_array($consulta);
    ?>
    <form method="post" action="modificacion">
        <input type="hidden" name="id" value="<?php echo $campo['id']; ?>">
        <input type="text" name="producto" value="<?php echo $campo['producto']; ?>">
        <input type="number" name="precio" value="<?php echo $campo['precio']; ?>">
        <input type="number" name="costo" value="<?php echo $campo['costo']; ?>">
        <input type="submit" name="actualizar">
    </form>
    <?php    
    } else {
    ?>
    <form method="post" action="alta">
        <input type="text" name="producto" placeholder="Producto">
        <input type="number" name="precio" placeholder="Precio">
        <input type="number" name="costo" placeholder="Costo">
        <input type="submit" name="insertar">
    </form>
    <?php
    }
    ?>
    <input type="search" placeholder="Buscar aquí..." name="busqueda" id="buscar">
    <div id="datos">
    <table>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Costo</th>
            <th>Borrar</th>
        </tr>
        <?php
        $consulta = mysqli_query($conexion, "SELECT * FROM productos ORDER BY producto ASC");
        while ($campo = mysqli_fetch_array($consulta)) {
            $producto = $campo['producto'];
        ?>
        <tr>
            <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['producto']; ?></td>
            <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['precio']; ?></td>
            <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['costo']; ?></td>
            <td><a href="baja?id=<?php echo $campo['id']; ?>" onclick="return confirm('¿Desea borrar <?php echo $producto; ?>?')">Borrar</a></td>
        </tr>
        <?php
        }
        ?>
    </table>
    </div>
    <footer>
        Frani <?php echo date('Y'); ?> - <a href="salir">Cerrar sesión</a>
    </footer>
    <script src="../js/buscar.js"></script>
    <script src="../js/jquery-3.6.1.min.js"></script>
</body>
</html>