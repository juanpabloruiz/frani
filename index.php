<?php include('conexion.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FRANI</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <?php
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE id = '$id'");
        $campo = mysqli_fetch_assoc($consulta);
    ?>
        <form method="POST" action="actualizar.php">
            <input type="hidden" name="id" value="<?php echo $campo['id']; ?>">
            <input type="text" name="producto" value="<?php echo $campo['producto']; ?>" required>
            <select name="categoria">
                <option value="<?php echo $campo['categoria']; ?>"><?php echo $campo['categoria']; ?></option>
                <?php
                $categoria = $campo['categoria'];
                $consulta = mysqli_query($conexion, "SELECT * FROM categorias");
                while ($campo2 = mysqli_fetch_assoc($consulta)) {
                ?>
                    <option value="<?php echo $campo2['nombre']; ?>"><?php echo $campo2['nombre']; ?></option>
                <?php
                }
                ?>
            </select>
            <input type="number" name="precio" value="<?php echo $campo['precio']; ?>" required>
            <input type="number" name="costo" value="<?php echo $campo['costo']; ?>" required>
            <input type="submit" name="nuevo" value="Actualizar">
        </form>
    <?php
    } else {
    ?>
        <form method="POST" action="insertar.php">
            <input type="text" name="producto" placeholder="Producto" required>
            <select name="categoria">
                <option>Elegir categoría</option>
                <?php
                $consulta = mysqli_query($conexion, "SELECT * FROM categorias");
                while ($campo = mysqli_fetch_assoc($consulta)) {
                ?>
                    <option value="<?php echo $campo['nombre']; ?>"><?php echo $campo['nombre']; ?></option>
                <?php
                }
                ?>
            </select>
            <input type="number" name="precio" placeholder="Precio" required>
            <input type="number" name="costo" placeholder="Costo" required>
            <input type="submit" name="nuevo" value="Insertar">
        </form>
    <?php
    }
    ?>
    <input type="search" placeholder="Buscar aquí..." name="busqueda" id="buscar">
    <hr>
    <div id="datos">
        <table border="1">
            <tr>
                <th>PRODUCTO</th>
                <th>CATEGORIA</th>
                <th>PRECIO</th>
                <th>COSTO</th>
                <th>FUNCIONES</th>
            </tr>
            <?php
            $consulta = mysqli_query($conexion, "SELECT * FROM productos ORDER BY producto ASC");
            while ($campo = mysqli_fetch_assoc($consulta)) {
            ?>
                <tr>
                    <td><?php echo $campo['producto']; ?></td>
                    <td><?php echo $campo['categoria']; ?></td>
                    <td><?php echo $campo['precio']; ?></td>
                    <td><?php echo $campo['costo']; ?></td>
                    <td><a href="index.php?id=<?php echo $campo['id']; ?>">Editar</a> | <a href="eliminar.php?id=<?php echo $campo['id']; ?>" onclick="return confirm('¿Desea eliminar <?php echo $campo['producto']; ?>?')">Eliminar</a></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/buscar.js"></script>
</body>
</html>