<?php
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
$carpetaActual = basename(dirname($_SERVER['SCRIPT_NAME']));
?>
<nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= e(base_path()) ?>">Frani</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link m-1 rounded <?= $paginaActual === 'inicio.php' ? 'active bg-primary' : '' ?>"
                        href="<?= e(base_path('panel/inicio')) ?>">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link m-1 rounded <?= $carpetaActual === 'productos' ? 'active bg-primary' : '' ?>"
                        href="<?= e(base_path('panel/productos')) ?>">Productos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link m-1 rounded <?= $carpetaActual === 'categorias' ? 'active bg-primary' : '' ?>"
                        href="<?= e(base_path('panel/categorias')) ?>">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link m-1 rounded <?= $carpetaActual === 'facturas' ? 'active bg-primary' : '' ?>"
                        href="<?= e(base_path('panel/facturas')) ?>">Facturas</a>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link m-1 rounded" href="<?= e(base_path()) ?>">Sitio web</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link m-1 rounded text-danger" href="<?= e(base_path('panel/salir')) ?>">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
