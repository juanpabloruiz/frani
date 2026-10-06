<?php
// Menu lateral del shell estilo WordPress.
// Solo para las secciones que usan este layout (ver ../test/).
// Abre <main class="sb-main">; la pagina lo cierra con </main>.
$carpetaActual = basename(dirname($_SERVER['SCRIPT_NAME']));
$esTest = $carpetaActual === 'test';

$itemsMenu = [
    ['icon' => 'fa-house', 'texto' => 'Inicio', 'ruta' => 'panel/inicio'],
    ['icon' => 'fa-box', 'texto' => 'Productos', 'ruta' => 'panel/productos'],
    ['icon' => 'fa-tags', 'texto' => 'Categorias', 'ruta' => 'panel/categorias'],
    ['icon' => 'fa-receipt', 'texto' => 'Ventas', 'ruta' => 'panel/facturas'],
    ['icon' => 'fa-users', 'texto' => 'Clientes', 'ruta' => 'panel/clientes'],
    ['icon' => 'fa-paw', 'texto' => 'Mascotas', 'ruta' => 'panel/mascotas'],
    ['icon' => 'fa-percent', 'texto' => 'Porcentajes', 'ruta' => 'panel/porcentajes'],
    ['icon' => 'fa-flask', 'texto' => 'Test', 'ruta' => 'panel/test'],
];

$itemsPie = [
    ['icon' => 'fa-globe', 'texto' => 'Ver sitio', 'ruta' => ''],
    ['icon' => 'fa-right-from-bracket', 'texto' => 'Salir', 'ruta' => 'panel/salir'],
];

/**
 * Dibuja la lista de items del menu lateral.
 */
if (!function_exists('sb_items')) {
    function sb_items(array $items, string $carpetaActual): void
    {
        foreach ($items as $item) {
            $activo = $carpetaActual === basename(parse_url($item['ruta'], PHP_URL_PATH) ?: '');
            echo '<a class="sb-item' . ($activo ? ' active' : '') . '"'
                . ' href="' . e(base_path($item['ruta'])) . '"'
                . ' title="' . e($item['texto']) . '">'
                . '<i class="fa-solid ' . e($item['icon']) . '"></i>'
                . '<span class="sb-label">' . e($item['texto']) . '</span>'
                . '</a>';
        }
    }
}
?>
<header class="sb-topbar d-md-none">
    <a class="sb-marca" href="<?= e(base_path()) ?>">Frani</a>
    <button class="sb-btn-menu" type="button" data-bs-toggle="offcanvas" data-bs-target="#sbMenu"
        aria-controls="sbMenu" aria-label="Menu">
        <i class="fa-solid fa-bars"></i>
    </button>
</header>

<div class="offcanvas offcanvas-start sb-offcanvas" tabindex="-1" id="sbMenu" aria-labelledby="sbMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title mb-0" id="sbMenuLabel">Frani</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0">
        <nav class="sb-nav">
            <?php sb_items($itemsMenu, $carpetaActual); ?>
        </nav>
        <div class="sb-pie">
            <?php sb_items($itemsPie, $carpetaActual); ?>
        </div>
    </div>
</div>

<aside class="sb-sidebar">
    <a class="sb-marca-lateral" href="<?= e(base_path()) ?>" title="Frani">
        <img src="<?= e(base_path('img/logo.png')) ?>" alt="Frani">
    </a>
    <nav class="sb-nav">
        <?php sb_items($itemsMenu, $carpetaActual); ?>
    </nav>
    <div class="sb-pie">
        <?php sb_items($itemsPie, $carpetaActual); ?>
    </div>
</aside>

<main class="sb-main sb-min0">
