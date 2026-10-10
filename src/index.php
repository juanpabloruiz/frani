<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/catalogo.php';

$db = conexion();
$busquedaCatalogo = '';
$categoriaCatalogo = null;
$resultado = catalogo_productos($db);
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
    <link rel="stylesheet" href="<?= e(base_path('css/estilo.css?v=19')) ?>">
</head>

<body>
    <?php include __DIR__ . '/cabecera.php'; ?>

    <h1 class="visually-hidden">Catálogo de productos</h1>
    <?php include __DIR__ . '/_buscador-catalogo.php'; ?>

    <div class="masonry-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4" id="productosCatalogo" aria-busy="false">
        <?php include __DIR__ . '/_catalogo-productos.php'; ?>
    </div>

    <?php include __DIR__ . '/pie.php'; ?>
