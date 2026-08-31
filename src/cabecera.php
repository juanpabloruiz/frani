<?php
$categoriasMenu = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre");
$usuarioMenu = usuario_actual();
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
$categoriaSeleccionada = (int) ($_GET['id'] ?? 0);
?>
<header class="bg-primary p-4 text-white d-none d-md-block">
    <a href="<?= e(base_path()) ?>">
        <picture>
            <source srcset="<?= e(base_path('img/logo.webp')) ?>" type="image/webp">
            <img src="<?= e(base_path('img/logo.png')) ?>" class="img-fluid mx-auto d-block" fetchpriority="high" width="300" alt="Logotipo Frani">
        </picture>
    </a>
</header>

<nav class="navbar sticky-top navbar-expand-lg bg-dark" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand d-lg-none" href="<?= e(base_path()) ?>">Frani</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto fs-5 mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-3 <?= $paginaActual === 'index.php' ? 'active' : '' ?>" <?= $paginaActual === 'index.php' ? 'aria-current="page"' : '' ?> href="<?= e(base_path()) ?>">Inicio</a>
                </li>
                <?php if ($categoriasMenu && $categoriasMenu->num_rows > 0): ?>
                    <?php while ($catMenu = $categoriasMenu->fetch_assoc()): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 <?= ($paginaActual === 'categoria.php' && (int) $catMenu['id'] === $categoriaSeleccionada) ? 'active' : '' ?>" <?= ($paginaActual === 'categoria.php' && (int) $catMenu['id'] === $categoriaSeleccionada) ? 'aria-current="page"' : '' ?> href="<?= e(base_path('categoria.php?id=' . (int) $catMenu['id'])) ?>"><?= e($catMenu['nombre']) ?></a>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-2 text-white-50 small" href="<?= e(base_path('panel')) ?>">
                        <i class="fa-solid fa-user me-1"></i><?= $usuarioMenu ? e($usuarioMenu['nombre']) : 'Iniciar sesión' ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<main class="container my-4">
