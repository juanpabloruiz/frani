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
    <?php require __DIR__ . '/menu.php'; ?>

    <main class="container-fluid my-4">
        <?php require __DIR__ . '/controles.php'; ?>

        <section>
            <?php require __DIR__ . '/productos.php'; ?>
        </section>
    </main>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
    <script src="<?= e(base_path('js/masonry.pkgd.min.js')) ?>"></script>
</body>

</html>
