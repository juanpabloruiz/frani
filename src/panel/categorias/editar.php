<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redireccionar('panel/categorias');
}

$db = conexion();

$stmt = $db->prepare("SELECT id, nombre FROM categorias WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$resultado = $stmt->get_result();
$categoria = $resultado->fetch_assoc();
$stmt->close();

if ($categoria === null) {
    redireccionar('panel/categorias');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoría | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css?v=3')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Editar categoría</h1>
            <a href="<?= e(base_path('panel/categorias')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <form method="POST" action="<?= e(base_path('panel/categorias/actualizar')) ?>" class="row g-3">
            <?= CSRF_field() ?>
            <input type="hidden" name="id" value="<?= e((string) $categoria['id']) ?>">

            <div class="col-md-8">
                <label class="form-label">Nombre de la categoría</label>
                <input type="text" name="nombre" class="form-control"
                    value="<?= e($categoria['nombre']) ?>" required>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Actualizar categoría</button>
            </div>
        </form>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
