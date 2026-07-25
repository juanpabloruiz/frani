<?php
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
$carpetaActual = basename(dirname($_SERVER['SCRIPT_NAME']));
$esInicio = $paginaActual === 'inicio.php';
$esProductos = $carpetaActual === 'productos';
$esCategorias = $carpetaActual === 'categorias';
$esFacturas = $carpetaActual === 'facturas';

function adminNavLink(string $href, bool $active, string $icon, string $label): string
{
    $cls = 'nav-link text-white' . ($active ? ' active' : '');
    return '<a class="' . $cls . '" href="' . e($href) . '"><i class="fa-solid ' . $icon . ' me-2"></i>' . $label . '</a>';
}
?>
<!-- ═══ Desktop layout (top bar + sidebar) ═══ -->
<nav class="navbar navbar-dark bg-dark fixed-top d-none d-lg-flex" style="z-index:1030; height:32px; min-height:32px; padding:0 .75rem;">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="height:32px;">
        <a class="navbar-brand fw-bold mb-0 lh-1" href="<?= e(base_path()) ?>" style="font-size:.9rem;">Frani</a>
        <a class="text-white-50 text-decoration-none small" href="<?= e(base_path()) ?>" target="_blank">Ver sitio</a>
    </div>
</nav>

<aside class="bg-dark text-white position-fixed overflow-auto d-none d-lg-flex flex-column" style="top:32px; bottom:0; left:0; width:160px; z-index:1020;">
    <nav class="nav flex-column py-2" style="font-size:.875rem;">
        <?= adminNavLink(e(base_path('panel/inicio')),   $esInicio,   'fa-house',          'Inicio') ?>
        <?= adminNavLink(e(base_path('panel/productos')), $esProductos, 'fa-box',           'Productos') ?>
        <?= adminNavLink(e(base_path('panel/categorias')), $esCategorias, 'fa-tags',        'Categorías') ?>
        <?= adminNavLink(e(base_path('panel/facturas')),  $esFacturas,  'fa-receipt',       'Facturas') ?>
        <hr class="border-secondary my-2 mx-3">
        <?= adminNavLink(e(base_path('panel/salir')),     false,        'fa-right-from-bracket', 'Salir') ?>
    </nav>
</aside>

<!-- ═══ Mobile layout (standard Bootstrap navbar) ═══ -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top d-lg-none" style="z-index:1030;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= e(base_path()) ?>">Frani</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#mobileNav" aria-controls="mobileNav" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mobileNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><?= adminNavLink(e(base_path('panel/inicio')),    $esInicio,    'fa-house',          'Inicio') ?></li>
                <li class="nav-item"><?= adminNavLink(e(base_path('panel/productos')),  $esProductos, 'fa-box',            'Productos') ?></li>
                <li class="nav-item"><?= adminNavLink(e(base_path('panel/categorias')), $esCategorias,'fa-tags',           'Categorías') ?></li>
                <li class="nav-item"><?= adminNavLink(e(base_path('panel/facturas')),   $esFacturas,  'fa-receipt',        'Facturas') ?></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><?= adminNavLink(e(base_path()),                  false,        'fa-globe',          'Ver sitio') ?></li>
                <li class="nav-item"><?= adminNavLink(e(base_path('panel/salir')),      false,        'fa-right-from-bracket', 'Salir') ?></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content wrapper -->
<style>@media(max-width:991.98px){#adminContent{margin-left:0!important;padding-top:4.25rem!important;}}</style>
<main id="adminContent" class="pt-4" style="margin-left:160px;">