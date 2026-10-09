<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$db = conexion();

$categoria = null;
$resultado = null;
$uri = $_GET['uri'] ?? null;

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
        header('Location: ' . $rutaCanonica, true, 301);
        exit;
    }

    $urlCanonica = sitio_url(ltrim($rutaCanonica, '/'));
    $stmt = $db->prepare(
        "SELECT p.producto, p.uri, p.foto, p.precio, p.stock, c.nombre AS categoria, c.uri AS categoria_uri
         FROM productos p
         INNER JOIN categorias c ON c.id = p.id_categoria
         WHERE p.id_categoria = ?
         ORDER BY p.precio ASC"
    );
    $stmt->bind_param('i', $categoria['id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->close();
} else {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $categoria ? e($categoria['nombre']) . ' | Frani' : 'Categoría no encontrada | Frani' ?></title>
    <?php if ($categoria): ?>
        <meta name="description" content="<?= e('Encontrá productos de ' . $categoria['nombre'] . ' en Frani. Consultá precios y disponibilidad.') ?>">
        <link rel="canonical" href="<?= e($urlCanonica) ?>">
    <?php else: ?>
        <meta name="robots" content="noindex, follow">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= e(base_path('css/bootstrap.min.css')) ?>">
    <link rel="icon" type="image/svg+xml" href="<?= e(base_path('img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('css/estilo.css?v=11')) ?>">
</head>

<body>
    <?php include __DIR__ . '/cabecera.php'; ?>

    <?php if ($categoria): ?>
    <h1 class="h3 text-center text-primary fw-bolder mb-4"><?= e($categoria['nombre']) ?></h1>

    <div class="masonry-grid row row-cols-1 row-cols-md-5 g-4">
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <div class="col">
                    <a class="card product-card-link" href="<?= e(producto_path($fila['uri'], $fila['categoria_uri'])) ?>">
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
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    No hay productos en esta categoría.
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
        <div class="text-center py-5">
            <h1 class="h2">Categoría no encontrada</h1>
            <p class="text-secondary">La categoría que buscás no está disponible.</p>
            <a class="btn btn-primary" href="<?= e(base_path()) ?>">Volver al catálogo</a>
        </div>
    <?php endif; ?>

    <?php include __DIR__ . '/pie.php'; ?>
