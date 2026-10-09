<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/seo.php';

$db = conexion();
$uri = $_GET['uri'] ?? '';
$categoriaRuta = $_GET['categoria'] ?? null;
$producto = null;
$categoriaRutaValida = !array_key_exists('categoria', $_GET)
    || (is_string($categoriaRuta) && strlen($categoriaRuta) <= 200 && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $categoriaRuta));

if ($categoriaRutaValida && is_string($uri) && strlen($uri) <= 200 && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $uri)) {
    $stmt = $db->prepare(
        "SELECT p.id, p.producto, p.uri, p.foto, p.descripcion, p.precio, p.stock,
            c.id AS categoria_id, c.nombre AS categoria, c.uri AS categoria_uri
        FROM productos p
        INNER JOIN categorias c ON c.id = p.id_categoria
        WHERE p.uri = ?
        LIMIT 1"
    );
    $stmt->bind_param('s', $uri);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($producto) {
    $rutaCanonica = producto_path($producto['uri'], $producto['categoria_uri']);
    $rutaSolicitada = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

    if ($rutaSolicitada !== $rutaCanonica || ($categoriaRuta !== null && $categoriaRuta !== $producto['categoria_uri'])) {
        header('Location: ' . $rutaCanonica, true, 301);
        exit;
    }

    $descripcion = trim((string) ($producto['descripcion'] ?? ''));
    $seo = seo_producto($producto);
    $foto = seo_foto_producto($producto['foto']);
    $imagenJpg = $foto !== null ? 'img/productos/' . $foto . '.jpg' : 'img/Ejemplo..jpg';
    $imagenWebp = $foto !== null ? 'img/productos/' . $foto . '.webp' : 'img/Ejemplo.webp';
    $relacionados = productos_relacionados($db, (int) $producto['id'], (int) $producto['categoria_id']);
} else {
    http_response_code(404);
    $seo = [
        'titulo' => 'Producto no encontrado | Frani',
        'descripcion' => 'El producto que buscás no está disponible. Visitá el catálogo de Frani.',
        'noindex' => true,
    ];
}
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

    <?php if ($producto): ?>
        <nav aria-label="Ruta de navegación" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= e(base_path()) ?>">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= e(categoria_path($producto['categoria_uri'])) ?>"><?= e($producto['categoria']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($producto['producto']) ?></li>
            </ol>
        </nav>

        <article class="row g-4 g-lg-5 align-items-start">
            <div class="col-md-6">
                <picture class="d-block bg-white rounded shadow-sm p-3">
                    <source srcset="<?= e(base_path($imagenWebp)) ?>" type="image/webp">
                    <img src="<?= e(base_path($imagenJpg)) ?>" class="product-detail-image" alt="<?= e($producto['producto']) ?>" fetchpriority="high">
                </picture>
            </div>
            <div class="col-md-6">
                <a class="badge text-bg-dark mb-3 text-decoration-none" href="<?= e(categoria_path($producto['categoria_uri'])) ?>"><?= e($producto['categoria']) ?></a>
                <h1 class="product-detail-title h2 mb-3"><?= e($producto['producto']) ?></h1>
                <p class="h2 text-primary fw-bolder mb-3">$ <?= e(moneda($producto['precio'])) ?></p>
                <p class="mb-4 <?= $producto['stock'] !== null && (int) $producto['stock'] <= 0 ? 'text-danger' : 'text-secondary' ?>">
                    <?php if ($producto['stock'] === null): ?>
                        Consultar disponibilidad.
                    <?php elseif ((int) $producto['stock'] <= 0): ?>
                        Sin stock por el momento.
                    <?php else: ?>
                        Stock disponible: <?= e((string) $producto['stock']) ?>
                    <?php endif; ?>
                </p>
                <h2 class="h5">Descripción</h2>
                <div class="product-description mb-4">
                    <?php if ($descripcion !== ''): ?>
                        <p><?= nl2br(e($descripcion)) ?></p>
                    <?php else: ?>
                        <p>Consultá por más detalles de este producto y su disponibilidad.</p>
                    <?php endif; ?>
                </div>
                <a class="btn btn-outline-primary" href="<?= e(categoria_path($producto['categoria_uri'])) ?>">Ver más de <?= e($producto['categoria']) ?></a>
            </div>
        </article>

        <?php if ($relacionados): ?>
            <section class="mt-5 pt-4 border-top" aria-labelledby="productos-relacionados">
                <h2 class="h4 mb-4" id="productos-relacionados">Productos relacionados</h2>
                <div class="row row-cols-1 row-cols-lg-3 g-3">
                    <?php foreach ($relacionados as $relacionado): ?>
                        <?php
                        $fotoRelacionada = !empty($relacionado['foto']) ? 'img/productos/' . $relacionado['foto'] : 'img/Ejemplo';
                        $jpgRelacionado = !empty($relacionado['foto']) ? $fotoRelacionada . '.jpg' : 'img/Ejemplo..jpg';
                        ?>
                        <div class="col">
                            <a class="card h-100 product-card-link related-product-card" href="<?= e(producto_path($relacionado['uri'], $relacionado['categoria_uri'])) ?>">
                                <picture class="related-product-picture">
                                    <source srcset="<?= e(base_path($fotoRelacionada . '.webp')) ?>" type="image/webp">
                                    <img src="<?= e(base_path($jpgRelacionado)) ?>" class="related-product-image" width="112" height="112" alt="<?= e($relacionado['producto']) ?>" loading="lazy">
                                </picture>
                                <div class="card-body">
                                    <h3 class="h6 card-title mb-2"><?= e($relacionado['producto']) ?></h3>
                                    <p class="card-text text-primary fw-bold mb-0">$ <?= e(moneda($relacionado['precio'])) ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <h1 class="h2">Producto no encontrado</h1>
            <p class="text-secondary">El producto que buscás no está disponible.</p>
            <a class="btn btn-primary" href="<?= e(base_path()) ?>">Volver al catálogo</a>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/pie.php'; ?>
