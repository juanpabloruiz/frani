<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/catalogo.php';

$db = conexion();

$categoria = null;
$resultado = null;
$uri = $_GET['uri'] ?? null;
$paginaCatalogo = catalogo_numero_pagina();

if (array_key_exists('uri', $_GET)) {
    if (is_string($uri) && strlen($uri) <= 200 && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $uri)) {
        $stmtCat = $db->prepare("SELECT id, nombre, uri FROM categorias WHERE uri = ?");
        $stmtCat->bind_param('s', $uri);
    }
} else {
    $id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($id !== false) {
        $stmtCat = $db->prepare("SELECT id, nombre, uri FROM categorias WHERE id = ?");
        $stmtCat->bind_param('i', $id);
    }
}

if (isset($stmtCat)) {
    $stmtCat->execute();
    $categoria = $stmtCat->get_result()->fetch_assoc();
    $stmtCat->close();
}

if ($categoria) {
    $rutaCanonica = categoria_path($categoria['uri']);
    $rutaSolicitada = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

    if ($rutaSolicitada !== $rutaCanonica) {
        header('Location: ' . $rutaCanonica . ($paginaCatalogo > 1 ? '?pagina=' . $paginaCatalogo : ''), true, 301);
        exit;
    }

    $urlCanonica = sitio_url(ltrim($rutaCanonica, '/')) . ($paginaCatalogo > 1 ? '?pagina=' . $paginaCatalogo : '');
    $descripcionMeta = 'Encontrá productos de ' . $categoria['nombre'] . ' en Frani. Consultá precios y disponibilidad.';
    $seo = [
        'titulo' => $categoria['nombre'] . ' | Frani',
        'descripcion' => seo_texto($descripcionMeta, 160),
        'url' => $urlCanonica,
        'imagen' => seo_imagen_social(),
        'datos' => [
            '@context' => 'https://schema.org',
            '@graph' => [
                ['@type' => 'CollectionPage', 'name' => $categoria['nombre'], 'url' => $urlCanonica,
                    'description' => $descripcionMeta, 'inLanguage' => 'es-AR'],
                ['@type' => 'BreadcrumbList', 'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => sitio_url()],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => $categoria['nombre'], 'item' => $urlCanonica],
                ]],
            ],
        ],
    ];
    $busquedaCatalogo = '';
    $categoriaCatalogo = (int) $categoria['id'];
    $loteCatalogo = catalogo_pagina($db, '', $categoriaCatalogo, ($paginaCatalogo - 1) * 25);
    $resultado = $loteCatalogo['resultado'];
} else {
    http_response_code(404);
    $seo = [
        'titulo' => 'Categoría no encontrada | Frani',
        'descripcion' => 'La categoría que buscás no está disponible. Visitá el catálogo de Frani.',
        'noindex' => true,
    ];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?= e(base_path('js/tema.js')) ?>"></script>
    <?php include __DIR__ . '/_seo.php'; ?>
    <link rel="stylesheet" href="<?= e(base_path('css/bootstrap.min.css')) ?>">
    <link rel="icon" type="image/svg+xml" href="<?= e(base_path('img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('css/estilo.css?v=23')) ?>">
</head>

<body>
    <?php include __DIR__ . '/cabecera.php'; ?>

    <?php if ($categoria): ?>
    <h1 class="visually-hidden"><?= e($categoria['nombre']) ?></h1>
    <?php include __DIR__ . '/_buscador-catalogo.php'; ?>

    <div class="masonry-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4" id="productosCatalogo" aria-busy="false">
        <?php include __DIR__ . '/_catalogo-productos.php'; ?>
    </div>
    <?php include __DIR__ . '/_paginacion-catalogo.php'; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <h1 class="h2">Categoría no encontrada</h1>
            <p class="text-body-secondary">La categoría que buscás no está disponible.</p>
            <a class="btn btn-primary" href="<?= e(base_path()) ?>">Volver al catálogo</a>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/pie.php'; ?>
