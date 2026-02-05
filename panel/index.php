<?php
require_once __DIR__ . '/../conexion.php';
//require_once __DIR__ . '/../funciones.php';
?>

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



    <main class="container-fluid py-3">

        <!-- Agregar categoría -->
        <div class="row">
            <div class="col-md-6">
                <form method="POST" action="insertar_categoria.php" class="d-flex flex-column flex-md-row gap-2 mb-3">
                    <input type="text" name="nombre" class="form-control" placeholder="Categoría">
                    <input type="submit" value="Agregar categoría" class="btn btn-primary">
                </form>
                <?php
                $sentencia = $conexion->prepare("SELECT * FROM categorias");
                $sentencia->execute();
                $resultado = $sentencia->get_result();
                ?>
                <ul>
                    <?php while ($campo = $resultado->fetch_assoc()): ?>
                        <li><?= $campo['nombre'] ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

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
                    <span class="input-group-text"><i class="fa-solid fa-barcode text-danger"></i></span>
                    <input type="text" name="codigo" class="form-control" placeholder="Código de barras">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-brands fa-product-hunt"></i></span>
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
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fa-solid fa-layer-group"></i></span>
                    <select name="categoria" class="form-select" aria-label="Seleccionar categoría">
                        <option selected value="0">Categoría</option>
                        <?php
                        $consulta = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC");
                        while ($fila = $consulta->fetch_assoc()):
                        ?>
                            <option value="<?= $fila['id'] ?>"><?= $fila['nombre'] ?></option>
                        <?php
                        endwhile
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="submit" value="Agregar producto" class="btn btn-primary w-100 w-md-auto">
                </div>
            </form>

        <?php endif; ?>

 



    </main>

    <!-- Scripts -->
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/masonry.pkgd.min.js"></script>

</body>

</html>