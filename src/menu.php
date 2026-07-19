<?php
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
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
                    <a class="nav-link m-1 px-2 <?= $paginaActual === 'index.php' ? 'active bg-primary rounded' : '' ?>"
                        href="<?= e(base_path()) ?>">Inicio</a>
                </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link m-1 px-2 <?= $paginaActual === 'index.php' && strpos($_SERVER['SCRIPT_NAME'], 'panel') !== false ? 'active bg-primary rounded' : '' ?>"
                        href="<?= e(base_path('panel')) ?>">Acceder</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
