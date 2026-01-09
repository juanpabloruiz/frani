<?php require_once __DIR__ . '/../conexion.php'; ?>

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

    <!-- Menú -->
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>panel">Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Cabecera -->
    <header class="bg-primary p-4 text-white">
        <a href="./">
            <h1>Frani</h1>
        </a>
    </header>

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
                            <h3 class="card-title">Card title</h3>
                            <p class="card-text">This is a longer card with supporting text below as a natural
                                lead-in
                                to additional content. This content is a little bit longer.</p>
                        </div>
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
                            <h3 class="card-title">Card title</h3>
                            <p class="card-text">This is a short card.</p>
                        </div>
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
                            <h3 class="card-title">Card title</h3>
                            <p class="card-text">This is a longer card with supporting text below as a natural
                                lead-in
                                to additional content.</p>
                        </div>
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
                            <h3 class="card-title">Card title</h3>
                            <p class="card-text">This is a longer card with supporting text below as a natural
                                lead-in
                                to additional content. This content is a little bit longer.</p>
                        </div>
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
                            <h3 class="card-title">Card title</h3>
                            <p class="card-text">This is a longer card with supporting text below as a natural
                                lead-in
                                to additional content. This content is a little bit longer.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="">
                    <div class="card shadow h-100">
                        <picture class="zoom">
                            <source srcset="img/7.webp" type="image/webp">
                            <img src="img/7.jpg" class="card-img-top" fetchpriority=high height="100%" width="200"
                                alt="Image1">
                        </picture>
                        <div class="card-body">
                            <h3 class="card-title">Card title</h3>
                            <p class="card-text">This is a longer card with supporting text below as a natural
                                lead-in
                                to additional content. This content is a little bit longer.</p>
                        </div>
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