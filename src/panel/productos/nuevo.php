<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();
$consultaCategorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php $tituloPagina = 'Nuevo Producto'; require __DIR__ . '/../_head.php'; ?>
    <link rel="stylesheet" href="<?= e(base_path('vendor/cropperjs/cropper.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('css/editor-foto.css?v=1')) ?>">
</head>

<body class="sb-app">
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="sb-contenido">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Nuevo producto</h1>
            <a href="<?= e(base_path('panel/productos')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="<?= e(base_path('panel/productos/insertar')) ?>" enctype="multipart/form-data" class="row g-3">
                    <?= CSRF_field() ?>

                    <div class="col-md">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="producto" class="form-control" required placeholder="Nombre">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Costo</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="costo" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" min="0">
                    </div>

                    <div class="col-md">
                        <label class="form-label">Categoría</label>
                        <select name="id_categoria" class="form-select" required>
                            <option value="">Seleccionar</option>
                            <?php while ($fila = $consultaCategorias->fetch_assoc()): ?>
                                <option value="<?= e((string) $fila['id']) ?>"><?= e($fila['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="5" placeholder="Descripción (opcional)"></textarea>
                    </div>

                    <div class="col-12">
                        <?php require __DIR__ . '/_foto.php'; ?>
                    </div>

                    <div class="col-12">
                        <div class="d-grid d-md-block">
                            <button type="submit" class="btn btn-primary btn-lg">Guardar producto</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </main>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
    <?php require __DIR__ . '/_editor-foto.php'; ?>
</body>

</html>
