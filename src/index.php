<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$resultado = $conexion->query(
    "SELECT p.producto, p.precio, p.stock, c.nombre AS categoria
    FROM productos p
    INNER JOIN categorias c ON c.id = p.id_categoria
    ORDER BY p.id DESC
    LIMIT 12"
);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('css/estilo.css')) ?>">
</head>

<body>
    <header class="bg-primary p-4 text-white d-none d-md-block">
        <a href="<?= e(base_path()) ?>">
            <picture>
                <source srcset="<?= e(base_path('img/logo.webp')) ?>" type="image/webp">
                <img src="<?= e(base_path('img/logo.png')) ?>" class="img-fluid mx-auto d-block" fetchpriority="high" width="300" alt="Logotipo Frani">
            </picture>
        </a>
    </header>

    <nav class="navbar sticky-top navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand d-lg-none" href="<?= e(base_path()) ?>">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto fs-5">
                    <li class="nav-item"><a class="nav-link active px-4" aria-current="page" href="<?= e(base_path()) ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="<?= e(base_path('panel.php')) ?>">Panel</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="<?= e(base_path('factura')) ?>">Facturas</a></li>
                    <li class="nav-item"><a class="nav-link px-4" href="<?= e(base_path('ventas')) ?>">Ventas</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h1 class="h2 mb-1">Catálogo</h1>
                <p class="text-secondary mb-0">Productos cargados en la base `frani`.</p>
            </div>
            <a href="<?= e(base_path('panel.php')) ?>" class="btn btn-primary">
                Administrar productos
            </a>
        </div>

        <div data-masonry='{"percentPosition": true }' class="row row-cols-1 row-cols-md-3 g-4">
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <div class="col">
                    <div class="card shadow h-100">
                        <picture class="zoom">
                            <source srcset="<?= e(base_path('img/1.webp')) ?>" type="image/webp">
                            <img src="<?= e(base_path('img/1.jpg')) ?>" class="card-img-top" width="200" alt="<?= e($fila['producto']) ?>">
                        </picture>
                        <div class="card-body">
                            <span class="badge text-bg-dark mb-2"><?= e($fila['categoria']) ?></span>
                            <h2 class="h4 card-title"><?= e($fila['producto']) ?></h2>
                            <p class="card-text h4 text-primary fw-bolder mb-2">$ <?= e(number_format((float) $fila['precio'], 2, ',', '.')) ?></p>
                            <p class="card-text text-secondary mb-0">Stock disponible: <?= e((string) $fila['stock']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if ($resultado->num_rows === 0): ?>
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        No hay productos cargados todavía.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="container-fluid bg-dark text-white text-center py-5">
        <p class="mb-0">
            Derechos reservados Frani - <?= date('Y') ?><br>
            Corrientes - Argentina
        </p>
    </footer>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
    <script src="<?= e(base_path('js/masonry.pkgd.min.js')) ?>"></script>
</body>

</html>
