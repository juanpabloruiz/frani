<?php
require_once __DIR__ . '/conexion.php';
requerir_login();

$usuario = usuario_actual();
$db = conexion();

$totalProductos = $db->query("SELECT COUNT(*) FROM productos")->fetch_row()[0];
$totalCategorias = $db->query("SELECT COUNT(*) FROM categorias")->fetch_row()[0];
$totalFacturas = $db->query("SELECT COUNT(*) FROM facturas")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/menu.php'; ?>

    <div class="container">
        <div class="mb-4">
            <h1 class="h3 mb-1">Bienvenido, <?= e($usuario['nombre']) ?></h1>
            <p class="text-secondary mb-0">Panel de administración de Frani.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <a href="<?= e(base_path('panel/productos')) ?>" class="text-decoration-none">
                    <div class="card shadow-sm text-center py-4">
                        <i class="fa-solid fa-box fa-3x text-primary mb-3"></i>
                        <h2 class="h4"><?= $totalProductos ?></h2>
                        <p class="text-secondary mb-0">Productos</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= e(base_path('panel/categorias')) ?>" class="text-decoration-none">
                    <div class="card shadow-sm text-center py-4">
                        <i class="fa-solid fa-tags fa-3x text-success mb-3"></i>
                        <h2 class="h4"><?= $totalCategorias ?></h2>
                        <p class="text-secondary mb-0">Categorías</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?= e(base_path('panel/facturas')) ?>" class="text-decoration-none">
                    <div class="card shadow-sm text-center py-4">
                        <i class="fa-solid fa-receipt fa-3x text-warning mb-3"></i>
                        <h2 class="h4"><?= $totalFacturas ?></h2>
                        <p class="text-secondary mb-0">Facturas</p>
                    </div>
                </a>
            </div>
        </div>
        </div>
    </main>

    <script src="<?= e(base_path('../js/bootstrap.bundle.min.js')) ?>"></script>

</body>

</html>
