<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Categoría | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Nueva categoría</h1>
            <a href="<?= e(base_path('panel/categorias')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <form method="POST" action="<?= e(base_path('panel/categorias/insertar')) ?>" class="row g-3">
            <?= CSRF_field() ?>

            <div class="col-md-8">
                <label class="form-label">Nombre de la categoría</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Nombre de la categoría">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-lg w-100">Guardar categoría</button>
            </div>
        </form>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
