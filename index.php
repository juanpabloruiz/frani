<?php require_once __DIR__ . '/conexion.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frani</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/estilo.css">

</head>

<body>

    <!-- Cabecera -->
    <header class="bg-primary p-4 text-white d-none d-md-block">
        <a href="./">
            <h1>Frani</h1>
        </a>
    </header>

    <!-- Menú -->
    <nav class="navbar sticky-top navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand d-lg-none" href="<?= BASE_URL ?>">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto fs-5">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?= BASE_URL ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="<?= BASE_URL ?>panel">Panel</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="<?= BASE_URL ?>panel">Productos</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="<?= BASE_URL ?>panel">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="#">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-3">

        <!-- Galería -->
        <div data-masonry='{"percentPosition": true }' class="row row-cols-1 row-cols-md-3 g-4">

            <?php
            $resultado = $conexion->query("SELECT * FROM productos ORDER BY id DESC LIMIT 12");
            while ($fila = $resultado->fetch_assoc()):
            ?>

                <div class="col">
                    <a href="">
                        <div class="card shadow h-100">
                            <picture class="zoom">
                                <source srcset="img/1.webp" type="image/webp">
                                <img src="img/1.jpg" class="card-img-top" fetchpriority=high height="100%" width="200"
                                    alt="Image1">
                            </picture>
                            <div class="card-body">
                                <h3 class="card-title"><?= $fila['producto'] ?></h3>
                                <p class="card-text h4 text-primary fw-bolder">$ <?= $fila['precio'] ?></p>
                                <p class="card-text">Son grandes , tienen 208 piezas. Para niños mayores a 3 años.</p>
                            </div>
                        </div>
                    </a>
                </div>

            <?php endwhile; ?>

        </div>

    </div>

    <footer class="container-fluid bg-dark text-white text-center py-5">
        <p>
            Derechos Reservados Frani - <?= date('Y') ?>
            <br>
            CP W3400 - Corrientes - Argentina
        </p>
    </footer>

    <!-- Scripts -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/masonry.pkgd.min.js"></script>

</body>

</html>