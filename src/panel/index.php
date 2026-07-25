<?php
require_once __DIR__ . '/conexion.php';

if (!empty($_SESSION['usuario_id'])) {
    redireccionar('panel/inicio');
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceder | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../css/estilo.css')) ?>">
</head>

<body class="bg-light">
    <!-- Desktop top bar -->
    <nav class="navbar navbar-dark bg-dark fixed-top d-none d-lg-flex" style="z-index:1030; height:32px; min-height:32px; padding:0 .75rem;">
        <div class="container-fluid d-flex justify-content-between align-items-center" style="height:32px;">
            <a class="navbar-brand fw-bold mb-0 lh-1" href="<?= e(base_path()) ?>" style="font-size:.9rem;">Frani</a>
            <a class="text-white-50 text-decoration-none small" href="<?= e(base_path()) ?>" target="_blank">Ver sitio</a>
        </div>
    </nav>

    <!-- Mobile navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top d-lg-none" style="z-index:1030;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?= e(base_path()) ?>">Frani</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mobileNav" aria-controls="mobileNav" aria-expanded="false" aria-label="Menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mobileNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= e(base_path()) ?>">
                            <i class="fa-solid fa-house me-2"></i>Inicio
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <style>@media(max-width:991.98px){#loginWrap{padding-top:4.25rem!important;}}</style>
    <div id="loginWrap" class="container" style="padding-top:2.5rem;">
        <div class="row justify-content-center mt-3">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h1 class="h4 text-center mb-4">Iniciar sesión</h1>

                        <?php if ($error !== ''): ?>
                            <div class="alert alert-danger"><?= e($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="<?= e(base_path('panel/ingreso')) ?>">
                            <?= CSRF_field() ?>

                            <div class="mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="correo" class="form-control" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="clave" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= e(base_path('../js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
