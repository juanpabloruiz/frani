<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/seo.php';

$db = conexion();
$resultado = $db->query(
    "SELECT p.producto, p.uri, p.foto, p.precio, p.stock, c.nombre AS categoria, c.uri AS categoria_uri
    FROM productos p
    INNER JOIN categorias c ON c.id = p.id_categoria
    ORDER BY GREATEST(COALESCE(p.modificado, p.agregado), p.agregado) DESC
    LIMIT 25"
);
$seo = [
    'titulo' => 'Frani | Catálogo de productos y precios',
    'descripcion' => 'Explorá el catálogo de Frani. Encontrá productos, fotos y precios en pesos argentinos. Consultá disponibilidad.',
    'url' => sitio_url(),
    'imagen' => seo_imagen_social(),
    'datos' => [
        '@context' => 'https://schema.org',
        '@graph' => [
            ['@type' => 'Organization', '@id' => sitio_url() . '#organizacion',
                'name' => 'Frani', 'url' => sitio_url(), 'logo' => sitio_url('img/logo.png')],
            ['@type' => 'WebSite', '@id' => sitio_url() . '#sitio', 'name' => 'Frani',
                'url' => sitio_url(), 'inLanguage' => 'es-AR',
                'publisher' => ['@id' => sitio_url() . '#organizacion']],
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/_seo.php'; ?>
    <link rel="stylesheet" href="<?= e(base_path('css/bootstrap.min.css')) ?>">
    <link rel="icon" type="image/svg+xml" href="<?= e(base_path('img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('css/estilo.css?v=11')) ?>">
</head>

<body>
    <?php include __DIR__ . '/cabecera.php'; ?>

    <h1 class="h3 text-center text-primary fw-bolder mb-4">Catálogo de productos</h1>

    <div class="masonry-grid row row-cols-1 row-cols-md-5 g-4">
        <?php while ($fila = $resultado->fetch_assoc()): ?>
            <div class="col">
                <a class="card h-100 product-card-link" href="<?= e(producto_path($fila['uri'], $fila['categoria_uri'])) ?>">
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
                        <h2 class="h5 card-title"><?= e($fila['producto']) ?></h2>
                        <p class="card-text h4 text-primary fw-bolder mb-2">$ <?= e(moneda($fila['precio'])) ?></p>
                        <p class="card-text text-secondary mb-0">Stock disponible: <?= e((string) $fila['stock']) ?></p>
                    </div>
                </a>
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
