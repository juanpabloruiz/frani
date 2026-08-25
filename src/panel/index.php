<?php
require_once __DIR__ . '/../conexion.php';

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
    <nav class="navbar navbar-expand-lg fixed-top" style="background-color: #0d6efd;">
        <div class="container-fluid d-flex align-items-center">
            <a class="navbar-brand" href="<?= e(base_path()) ?>">
                <img src="<?= e(base_path('img/logo.png')) ?>" alt="Frani" style="max-height: 40px; width: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#loginNav" aria-controls="loginNav" aria-expanded="false"
                aria-label="Menú">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="loginNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= e(base_path()) ?>">Volver al inicio</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="padding-top: 76px;">
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
