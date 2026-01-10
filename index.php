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
    <header class="bg-primary p-4 text-white">
        <a href="./">
            <h1>Frani</h1>
        </a>
    </header>

    <!-- Menú -->
    <nav class="navbar sticky-top navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container justify-content-center">
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

    <div class="container my-4">

        <!-- Galería -->
        <div data-masonry='{"percentPosition": true }' class="row row-cols-1 row-cols-md-3 g-4">

            <div class="col">
                <a href="">
                    <div class="card shadow h-100">
                        <picture class="zoom">
                            <source srcset="img/1.webp" type="image/webp">
                            <img src="img/1.jpg" class="card-img-top" fetchpriority=high height="100%" width="200"
                                alt="Image1">
                        </picture>
                        <div class="card-body">
                            <h3 class="card-title">$ 20.000 Set de Arte Carpin-bara</h3>
                            <p class="card-text">Son grandes , tienen 208 piezas. Para niños mayores a 3 años.</p>
                        </div>
                        <div class="card-footer text-body-secondary">10 de enero</div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="">
                    <div class="card shadow h-100">
                        <picture class="zoom">
                            <source srcset="img/2.webp" type="image/webp">
                            <img src="img/2.jpg" class="card-img-top" fetchpriority=high height="100%" width="200"
                                alt="Image1">
                        </picture>
                        <div class="card-body">
                            <h3 class="card-title">$ 2.000 Gorros navideños</h3>
                            <p class="card-text">Salieron más gorritos navideños 🎅</p>
                        </div>
                        <div class="card-footer text-body-secondary">10 de enero</div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="">
                    <div class="card shadow h-100">
                        <picture class="zoom">
                            <source srcset="img/3.webp" type="image/webp">
                            <img src="img/3.jpg" class="card-img-top" fetchpriority=high height="100%" width="200"
                                alt="Image1">
                        </picture>
                        <div class="card-body">
                            <h3 class="card-title">$ 4.000 Pizarras</h3>
                            <p class="card-text">Pizarras para divertirse dibujando!</p>
                        </div>
                        <div class="card-footer text-body-secondary">10 de enero</div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="">
                    <div class="card shadow h-100">
                        <picture class="zoom">
                            <source srcset="img/4.webp" type="image/webp">
                            <img src="img/4.jpg" class="card-img-top" fetchpriority=high height="100%" width="200"
                                alt="Image1">
                        </picture>
                        <div class="card-body">
                            <h3 class="card-title">$ 8.500 Pelotas</h3>
                            <p class="card-text">Pelotas N° 2 y 5 y de básquet</p>
                        </div>
                        <div class="card-footer text-body-secondary">10 de enero</div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="">
                    <div class="card shadow h-100">
                        <picture class="zoom">
                            <source srcset="img/6.webp" type="image/webp">
                            <img src="img/6.jpg" class="card-img-top" fetchpriority=high height="100%" width="200"
                                alt="Image1">
                        </picture>
                        <div class="card-body">
                            <h3 class="card-title">$ 10.000 Sets de jardín 2026</h3>
                            <p class="card-text">Combos, taza, cuchara/tenedor, individual, servilleta, toalla, botella, tupper, mochila, cartuchera y más!!!!</p>
                        </div>
                        <div class="card-footer text-body-secondary">10 de enero</div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="">
                    <div class="card shadow h-100">
                        <picture class="zoom">
                            <source srcset="img/5.webp" type="image/webp">
                            <img src="img/5.jpg" class="card-img-top" fetchpriority=high height="100%" width="200"
                                alt="Image1">
                        </picture>
                        <div class="card-body">
                            <h3 class="card-title">$ 21.000 Monopatines - últimos tres</h3>
                            <p class="card-text">Estamos a una cuadra de las Mil en el barrio Barrio Independencia Corrientes</p>
                        </div>
                        <div class="card-footer text-body-secondary">10 de enero</div>
                    </div>
                </a>
            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/masonry.pkgd.min.js"></script>

</body>

</html>