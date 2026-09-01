<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$db = conexion();
$resultado = $db->query(
    "SELECT p.producto, p.foto, p.precio, p.stock, c.nombre AS categoria
    FROM productos p
    INNER JOIN categorias c ON c.id = p.id_categoria
    ORDER BY p.precio ASC
    LIMIT 15"
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
    <?php include __DIR__ . '/cabecera.php'; ?>

    <div data-masonry='{"percentPosition": true }' class="row row-cols-1 row-cols-md-5 g-4">
        <?php while ($fila = $resultado->fetch_assoc()): ?>
            <div class="col">
                <div class="card shadow h-100">
                    <?php if (!empty($fila['foto'])): ?>
                        <picture>
                            <source srcset="<?= e(base_path('img/productos/' . $fila['foto'] . '.webp')) ?>" type="image/webp">
                            <img src="<?= e(base_path('img/productos/' . $fila['foto'] . '.jpg')) ?>" class="card-img-top" width="200" alt="<?= e($fila['producto']) ?>">
                        </picture>
                    <?php else: ?>
                        <picture>
                            <source srcset="<?= e(base_path('img/Ejemplo.webp')) ?>" type="image/webp">
                            <img src="<?= e(base_path('img/Ejemplo..jpg')) ?>" class="card-img-top" width="200" alt="<?= e($fila['producto']) ?>">
                        </picture>
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="badge text-bg-dark mb-2"><?= e($fila['categoria']) ?></span>
                        <h2 class="h4 card-title"><?= e($fila['producto']) ?></h2>
                        <p class="card-text h4 text-primary fw-bolder mb-2">$ <?= e(moneda($fila['precio'])) ?></p>
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

    <?php include __DIR__ . '/pie.php'; ?>
