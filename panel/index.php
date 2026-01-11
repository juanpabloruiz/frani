<?php require_once __DIR__ . '/../conexion.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../css/estilo.css">

</head>

<body class="ubuntu">

    <!-- Menú -->
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL ?>">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?= BASE_URL ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>panel">Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-3">

        <!-- Inserción y edición -->
        <?php
        $id = $_GET['editar'] ?? null;
        $editar = null;

        if ($id) {
            $sentencia = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
            $sentencia->bind_param("i", $id);
            $sentencia->execute();
            $resultado = $sentencia->get_result();
            $editar = $resultado->fetch_assoc();
        }
        ?>

        <?php if ($editar): ?>

            <!-- Formulario edición -->
            <form method="POST" action="update" class="d-flex flex-column flex-md-row gap-2 mb-3">
                <input type="hidden" name="id" value="<?= $editar['id'] ?>">
                <input type="text" name="codigo" class="form-control" value="<?= $editar['codigo'] ?>">
                <input type="text" name="producto" class="form-control" value="<?= $editar['producto'] ?>">
                <input type="text" name="costo" class="form-control" value="<?= $editar['costo'] ?>">
                <input type="text" name="precio" class="form-control" value="<?= $editar['precio'] ?>">
                <input type="submit" value="Actualizar" class="btn btn-primary">
            </form>

        <?php else: ?>

            <!-- Formulario inserción -->
            <form method="POST" action="/insert" class="d-flex flex-column flex-md-row gap-2 mb-3">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-barcode"></i></span>
                    <input type="text" name="codigo" class="form-control" placeholder="Código de barras">
                </div>
                <div class="mb-3">
                    <input type="text" name="producto" class="form-control" placeholder="Producto">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">$</span>
                    <input type="text" name="costo" class="form-control" placeholder="Costo">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">$</span>
                    <input type="text" name="precio" class="form-control" placeholder="Precio">
                </div>
                <div class="mb-3">
                    <input type="submit" value="Agregar" class="btn btn-primary">
                </div>
            </form>

        <?php endif; ?>

        <!-- Porcentaje -->
        <div>
            <h5>Generar interés sobre productos</h5>
            <form method="post">
                <?php
                if (isset($_POST['porcentual'])) {
                    $categoria = $_POST['categoria'];
                    $porcentaje = $_POST['porcentaje'];
                    $porcentaje_entero = round($porcentaje);
                    $consulta = "UPDATE productos SET precio = ROUND(precio * (1 + ?/100)) WHERE categoria = '$categoria'";
                    $sentencia = $conexion->prepare($consulta);
                    $sentencia->bind_param("i", $porcentaje_entero);
                    if ($sentencia->execute()) {
                        echo '<script>window.location="./"</script>';
                    } else {
                        echo '<div class="alert alert-danger">Error en la actualización: ' . $sentencia->error . '</div>';
                    }
                }
                ?>
                <div class="row">
                    <div class="col-md-auto">
                        <select name="categoria" class="form-select mb-3">
                            <option disabled selected>Categoría</option>
                            <?php
                            $consulta = mysqli_query($conexion, "SELECT * FROM categorias");
                            while ($campo = mysqli_fetch_assoc($consulta)) {
                            ?>
                                <option value="<?php echo $campo['nombre']; ?>"><?php echo $campo['nombre']; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <div class="input-group mb-3">
                            <input type="number" name="porcentaje" class="form-control" aria-label="Amount (to the nearest dollar)">
                            <span class="input-group-text">%</span>
                            <input type="submit" name="porcentual" value="Aplicar aumento" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de productos -->
        <table class="table">
            <tr>
                <th class="text-center">CÓDIGO</th>
                <th class="text-center">PRODUCTO</th>
                <th class="text-center">COSTO</th>
                <th class="text-center">PRECIO</th>
                <th class="text-center">FECHA</th>
                <th class="text-center">BORRAR</th>
            </tr>

            <?php
            $resultado = $conexion->query("SELECT * FROM productos ORDER BY id DESC");
            while ($fila = $resultado->fetch_assoc()):
            ?>

                <tr>
                    <td class="text-center"><?= $fila['codigo'] ?></td>
                    <td class="text-left"><?= $fila['producto'] ?></td>
                    <td class="text-end">$ <?= $fila['costo'] ?></td>
                    <td class="text-end">$ <?= $fila['precio'] ?></td>
                    <td class="text-center">
                        <?= date('d/m/y', strtotime($fila['fecha'])) ?>
                        <i class="fa-regular fa-alarm-clock mx-2 text-primary"></i>
                        <?= date('H:i', strtotime($fila['fecha'])) ?>
                    </td>
                    <td class="text-center"><a href="?editar=<?= $fila['id'] ?>">Editar</a></td>
                </tr>

            <?php endwhile; ?>

        </table>

    </main>

    <!-- Scripts -->
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/masonry.pkgd.min.js"></script>

</body>

</html>