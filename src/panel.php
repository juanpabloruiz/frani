<?php
ob_start();

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('css/estilo.css')) ?>">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= e(base_path()) ?>">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= e(base_path()) ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?= e(base_path('panel.php')) ?>">Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(base_path('factura')) ?>">Facturas</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(base_path('ventas')) ?>">Ventas</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid my-4">
        <div class="mb-4">
            <h1 class="h3 mb-1">Administración</h1>
            <p class="text-secondary mb-0">Alta, edición y consulta de categorías y productos.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <?php require __DIR__ . '/categorias.php'; ?>
            </div>
            <div class="col-lg-8">
                <?php require __DIR__ . '/porcentaje.php'; ?>
            </div>
        </div>

        <section class="mt-4">
            <?php require __DIR__ . '/controles.php'; ?>
        </section>

        <section class="mt-4">
            <?php require __DIR__ . '/productos.php'; ?>
        </section>
    </main>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
    <script src="<?= e(base_path('js/masonry.pkgd.min.js')) ?>"></script>
</body>

</html>
