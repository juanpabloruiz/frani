<?php
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
$carpetaActual = basename(dirname($_SERVER['SCRIPT_NAME']));
$esInicio = $paginaActual === 'inicio.php';
$esProductos = $carpetaActual === 'productos';
$esCategorias = $carpetaActual === 'categorias';
$esFacturas = $carpetaActual === 'facturas';
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary mb-3 mb-md-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= e(base_path()) ?>">Frani</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false"
            aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $esInicio ? 'active' : '' ?>" <?= $esInicio ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/inicio')) ?>">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $esProductos ? 'active' : '' ?>" <?= $esProductos ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/productos')) ?>">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $esCategorias ? 'active' : '' ?>" <?= $esCategorias ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/categorias')) ?>">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $esFacturas ? 'active' : '' ?>" <?= $esFacturas ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/facturas')) ?>">Facturas</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?= e(base_path()) ?>">Ver sitio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= e(base_path('panel/salir')) ?>">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main id="adminContent">
