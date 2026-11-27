<?php
$categoriasMenu = $db->query("SELECT id, nombre, uri FROM categorias ORDER BY nombre");
$usuarioMenu = usuario_actual();
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
$categoriaSeleccionada = (int) ($categoria['id'] ?? $producto['categoria_id'] ?? 0);
?>
<header class="bg-primary p-4 text-white d-none d-md-block">
    <a class="d-table mx-auto rounded" href="<?= e(base_path()) ?>">
        <picture>
            <source srcset="<?= e(base_path('img/logo.webp')) ?>" type="image/webp">
            <img src="<?= e(base_path('img/logo.png')) ?>" class="img-fluid mx-auto d-block" fetchpriority="high" width="300" alt="Logotipo Frani">
        </picture>
    </a>
</header>

<nav class="navbar sticky-top navbar-expand-lg bg-dark" data-bs-theme="dark"
    style="--bs-navbar-color: rgba(var(--bs-white-rgb), 0.85); --bs-navbar-hover-color: var(--bs-white);">
    <div class="container">
        <a class="navbar-brand d-lg-none rounded" href="<?= e(base_path()) ?>">Frani</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-3 small rounded <?= $paginaActual === 'index.php' ? 'active' : '' ?>" <?= $paginaActual === 'index.php' ? 'aria-current="page"' : '' ?> href="<?= e(base_path()) ?>">Inicio</a>
                </li>
                <?php if ($categoriasMenu && $categoriasMenu->num_rows > 0): ?>
                    <?php while ($catMenu = $categoriasMenu->fetch_assoc()): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 small rounded <?= (int) $catMenu['id'] === $categoriaSeleccionada ? 'active' : '' ?>" <?= (int) $catMenu['id'] === $categoriaSeleccionada ? 'aria-current="page"' : '' ?> href="<?= e(categoria_path($catMenu['uri'])) ?>"><?= e($catMenu['nombre']) ?></a>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-2 small rounded" href="<?= e(base_path('panel')) ?>">
                        <i class="fa-solid fa-user me-1"></i><?= $usuarioMenu ? e($usuarioMenu['nombre']) : 'Iniciar sesión' ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
