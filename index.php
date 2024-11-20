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
    <nav class="navbar sticky-top navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">FRANI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Ventas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container my-3">
        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $consulta = mysqli_query($conexion, "SELECT * FROM productos WHERE id = '$id'");
            $campo = mysqli_fetch_assoc($consulta);
        ?>
            <form method="POST" action="actualizar.php" class="d-flex gap-2 mb-3">
                <input type="hidden" name="id" value="<?php echo $campo['id']; ?>" class="form-control">
                <input type="text" name="producto" value="<?php echo $campo['producto']; ?>" required>
                <select name="categoria" class="form-select" >
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
                <input type="number" name="precio" value="<?php echo $campo['precio']; ?>" class="form-control" required>
                <input type="number" name="costo" value="<?php echo $campo['costo']; ?>" class="form-control" required>
                <input type="submit" name="nuevo" value="Actualizar" class="btn btn-primary">
            </form>
        <?php
        } else {
        ?>
            <form method="POST" action="insertar.php" class="d-flex gap-2 mb-3">
                <input type="text" name="producto" placeholder="Producto" class="form-control" required>
                <select name="categoria" class="form-select" >
                    <option>Categoría</option>
                    <?php
                    $consulta = mysqli_query($conexion, "SELECT * FROM categorias");
                    while ($campo = mysqli_fetch_assoc($consulta)) {
                    ?>
                        <option value="<?php echo $campo['nombre']; ?>"><?php echo $campo['nombre']; ?></option>
                    <?php
                    }
                    ?>
                </select>
                <input type="number" name="precio" placeholder="Precio" class="form-control" required>
                <input type="number" name="costo" placeholder="Costo" class="form-control" required>
                <input type="submit" name="nuevo" value="Insertar" class="btn btn-primary">
            </form>
        <?php
        }
        ?>
        <input type="search" placeholder="Buscar aquí..." name="busqueda" id="buscar" class="form-control">
        <hr>
        <div id="datos">
            <table class="table">
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
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/buscar.js"></script>
</body>

</html>