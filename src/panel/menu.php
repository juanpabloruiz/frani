<?php
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
$carpetaActual = basename(dirname($_SERVER['SCRIPT_NAME']));
$esInicio = $paginaActual === 'inicio.php';
$esProductos = $carpetaActual === 'productos';
$esCategorias = $carpetaActual === 'categorias';
$esFacturas = $carpetaActual === 'facturas';
$esPorcentajes = $carpetaActual === 'porcentajes';
?>
<nav class="navbar navbar-expand-lg fixed-top" style="background-color: #0d6efd;">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="<?= e(base_path()) ?>">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false"
            aria-label="Menú">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-white <?= $esInicio ? 'active' : '' ?>" <?= $esInicio ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/inicio')) ?>">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= $esProductos ? 'active' : '' ?>" <?= $esProductos ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/productos')) ?>">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= $esCategorias ? 'active' : '' ?>" <?= $esCategorias ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/categorias')) ?>">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= $esFacturas ? 'active' : '' ?>" <?= $esFacturas ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/facturas')) ?>">Facturas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= $esPorcentajes ? 'active' : '' ?>" <?= $esPorcentajes ? 'aria-current="page"' : '' ?> href="<?= e(base_path('panel/porcentajes')) ?>">Porcentajes</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= e(base_path()) ?>">Ver sitio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= e(base_path('panel/salir')) ?>">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main id="adminContent" style="padding-top: 70px;">
