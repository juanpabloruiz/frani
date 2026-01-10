<?php require_once __DIR__ . '/../conexion.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/estilo.css">

</head>

<body>

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

    <main class="container-fluid py-3">

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
            <form method="POST" action="update" class="d-flex gap-2 mb-3">
                <input type="hidden" name="id" value="<?= $editar['id'] ?>">
                <input type="text" name="codigo" class="form-control" value="<?= $editar['codigo'] ?>">
                <input type="text" name="producto" class="form-control" value="<?= $editar['producto'] ?>">
                <input type="text" name="precio" class="form-control" value="<?= $editar['precio'] ?>">
                <input type="submit" value="Actualizar" class="btn btn-primary">
            </form>

        <?php else: ?>

            <!-- Formulario inserción -->
            <form method="POST" action="/insert" class="d-flex gap-2 mb-3">
                <input type="text" name="codigo" class="form-control" placeholder="Código de barras">
                <input type="text" name="producto" class="form-control" placeholder="Producto">
                <input type="text" name="precio" class="form-control" placeholder="Precio">
                <input type="submit" value="Agregar" class="btn btn-primary">
            </form>

        <?php endif; ?>

        <!-- Tabla de productos -->
        <table class="table">
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Editar</th>
            </tr>

            <?php
            $resultado = $conexion->query("SELECT * FROM productos ORDER BY id DESC");
            while ($fila = $resultado->fetch_assoc()):
            ?>

                <tr>
                    <td><?= $fila['codigo'] ?></td>
                    <td><?= $fila['producto'] ?></td>
                    <td>$ <?= $fila['precio'] ?></td>
                    <td><a href="?editar=<?= $fila['id'] ?>">Editar</a></td>
                </tr>

            <?php endwhile; ?>

        </table>

    </main>

    <!-- Scripts -->
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/masonry.pkgd.min.js"></script>

</body>

</html>