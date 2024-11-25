<?php include('conexion.php'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | FRANI</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar sticky-top navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="./">FRANI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="./">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="factura">Factura</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ventas">Ventas</a>
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
                <input type="hidden" name="id" value="<?php echo $campo['id']; ?>">
                <input type="text" name="producto" value="<?php echo $campo['producto']; ?>" class="form-control" required>
                <input type="number" name="stock" value="<?php echo $campo['stock']; ?>" class="form-control" required>
                <select name="categoria" class="form-select">
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
                <input type="number" name="stock" placeholder="Stock" class="form-control" required>
                <select name="categoria" class="form-select">
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
                <input type="number" step="0.01" name="precio" placeholder="Precio" class="form-control" required>
                <input type="number" step="0.01" name="costo" placeholder="Costo" class="form-control" required>
                <input type="submit" name="nuevo" value="Insertar" class="btn btn-primary">
            </form>
        <?php
        }
        ?>
        <input type="search" placeholder="Buscar aquí..." name="busqueda" id="buscar" class="form-control">
        <hr>
        <div id="datos">
            <form method="POST" action="eliminar.php">
                <button type="submit" class="btn btn-danger mb-3" onclick="return confirm('¿Estás seguro de eliminar los productos seleccionados?')">Eliminar seleccionados</button>

                <table class="table">
                    <tr>
                        <th class="text-center"><input type="checkbox" id="selectAll"></th>
                        <th class="text-center">PRODUCTO</th>
                        <th class="text-center">STOCK</th>
                        <th class="text-center">CATEGORIA</th>
                        <th class="text-center">PRECIO</th>
                        <th class="text-center">COSTO</th>
                        <th class="text-center">FECHA</th>
                        <th class="text-center">FUNCIONES</th>
                    </tr>
                    <?php
                    $consulta = mysqli_query($conexion, "SELECT * FROM productos ORDER BY producto ASC");
                    while ($campo = mysqli_fetch_assoc($consulta)) {
                    ?>
                        <tr>
                            <td class="text-center"><input type="checkbox" name="ids[]" value="<?php echo $campo['id']; ?>"></td>
                            <td><?php echo $campo['producto']; ?></td>
                            <td class="text-center"><?php echo $campo['stock']; ?></td>
                            <td class="text-center"><?php echo $campo['categoria']; ?></td>
                            <td class="text-center"><?php echo '$ ' . $campo['precio']; ?></td>
                            <td class="text-center"><?php echo '$ ' . $campo['costo']; ?></td>
                            <?php
                            $fecha = $campo['fecha'];
                            $fecha_normal = date("d/m/Y", strtotime($fecha));
                            ?>
                            <td class="text-center"><?php echo $fecha_normal; ?></td>
                            <td class="text-center"><a href="?id=<?php echo $campo['id']; ?>">Editar</a></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </form>
        </div>
    </main>
    <script>
        // Seleccionar/deseleccionar todos los checkboxes
        document.getElementById('selectAll').addEventListener('click', function(e) {
            const checkboxes = document.querySelectorAll('input[name="ids[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
        });
    </script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/buscar.js"></script>
</body>

</html>