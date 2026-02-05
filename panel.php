<?php include 'conexion.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/estilo.css">

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
                    <li class="nav-item"><a class="nav-link" href="panel.php">Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid my-3">

        <div class="row">
            <div class="col-md">
                <?php include 'categorias.php' ?>
            </div>
            <div class="col-md">
                <?php include 'porcentaje.php' ?>
            </div>
        </div>

        <?php include 'controles.php' ?>

        <?php include 'productos.php' ?>

    </main>

    <!-- Javascript -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/masonry.pkgd.min.js"></script>

</body>

</html>