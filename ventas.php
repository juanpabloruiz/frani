<?php include('conexion.php'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | FRANI</title>
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
        <input type="search" placeholder="Buscar aquí..." name="busqueda" id="buscar" class="form-control">
        <hr>
        <div id="datos">
            <table class="table">
                <tr>
                    <th class="text-center">DETALLE</th>
                    <th class="text-center">CANTIDAD</th>
                    <th class="text-center">PRECIO</th>
                    <th class="text-center">METODO</th>
                    <th class="text-center">FECHA</th>
                </tr>
                <?php
                $consulta = mysqli_query($conexion, "SELECT * FROM ventas ORDER BY fecha ASC");
                while ($campo = mysqli_fetch_assoc($consulta)) {
                ?>
                    <tr>
                        <td><?php echo $campo['detalle']; ?></td>
                        <td class="text-center"><?php echo $campo['cantidad']; ?></td>
                        <td class="text-center"><?php echo '$ ' . $campo['precio']; ?></td>
                        <td class="text-center"><?php echo $campo['metodo']; ?></td>
                        <?php
                        $fecha = $campo['fecha'];
                        $fecha_normal = date("d/m/Y", strtotime($fecha));
                        ?>
                        <td class="text-center"><?php echo $fecha_normal; ?></td>
                        <td class="text-center"><a href="?id=<?php echo $campo['id']; ?>">Editar</a> | <a href="eliminar?id=<?php echo $campo['id']; ?>" onclick="return confirm('¿Desea eliminar <?php echo $campo['producto']; ?>?')">Eliminar</a></td>
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