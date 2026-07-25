<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$db = conexion();
$consultaCategorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container-fluid py-3 px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h3 mb-0">Nuevo producto</h1>
            <a href="<?= e(base_path('panel/productos')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="<?= e(base_path('panel/productos/insertar')) ?>" class="row g-3">
                    <?= CSRF_field() ?>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nombre del producto</label>
                        <input type="text" name="producto" class="form-control form-control-lg" required placeholder="Nombre del producto">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Categoría</label>
                        <select name="id_categoria" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <?php while ($fila = $consultaCategorias->fetch_assoc()): ?>
                                <option value="<?= e((string) $fila['id']) ?>"><?= e($fila['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="5" placeholder="Descripción del producto (opcional)"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Costo</label>
                        <input type="number" step="0.01" name="costo" class="form-control" required placeholder="0.00">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control" required placeholder="0.00">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" value="0" min="0" required>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg">Guardar producto</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>

</body>

</html>
