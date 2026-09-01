<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redireccionar('panel/productos');
}

$db = conexion();

$stmt = $db->prepare(
    "SELECT id, producto, foto, descripcion, costo, precio, stock, id_categoria
    FROM productos WHERE id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();
$stmt->close();

if ($producto === null) {
    redireccionar('panel/productos');
}

$consultaCategorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Editar producto</h1>
            <a href="<?= e(base_path('panel/productos')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <form method="POST" action="<?= e(base_path('panel/productos/actualizar')) ?>" enctype="multipart/form-data" class="row g-3">
            <?= CSRF_field() ?>
            <input type="hidden" name="id" value="<?= e((string) $producto['id']) ?>">

            <div class="col-md">
                <label class="form-label">Nombre</label>
                <input type="text" name="producto" class="form-control"
                    value="<?= e($producto['producto']) ?>" required>
            </div>

            <div class="col-md">
                <label class="form-label">Costo</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" name="costo" class="form-control"
                        value="<?= e(numero_limpio($producto['costo'])) ?>" required>
                </div>
            </div>

            <div class="col-md">
                <label class="form-label">Precio</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" name="precio" class="form-control"
                        value="<?= e(numero_limpio($producto['precio'])) ?>" required>
                </div>
            </div>

            <div class="col-md">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control"
                    value="<?= e((string) $producto['stock']) ?>" min="0">
            </div>

            <div class="col-md">
                <label class="form-label">Categoría</label>
                <select name="id_categoria" class="form-select" required>
                    <option value="">Seleccionar</option>
                    <?php while ($fila = $consultaCategorias->fetch_assoc()): ?>
                        <?php $selected = (int) $producto['id_categoria'] === (int) $fila['id']; ?>
                        <option value="<?= e((string) $fila['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                            <?= e($fila['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="5"><?= e($producto['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Foto del producto</label>
                <input type="file" name="foto" id="fotoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <small class="text-muted">Formatos: JPG, JPEG, PNG, WEBP (máx. 5MB). Dejar vacío para mantener la actual.</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Vista previa</label>
                <div id="vistaPrevia" class="border rounded p-2 text-center" style="min-height: 120px;">
                    <?php if (!empty($producto['foto'])): ?>
                        <img id="imgPreview" src="<?= e(base_path('img/productos/' . $producto['foto'] . '.jpg')) ?>" alt="Vista previa" style="max-height: 100px;">
                    <?php else: ?>
                        <img id="imgPreview" src="" alt="Vista previa" style="max-height: 100px; display: none;">
                        <p id="placeholderPreview" class="text-muted mb-0 mt-2">Sin imagen</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12">
                <div class="d-grid d-md-block">
                    <button type="submit" class="btn btn-primary btn-lg">Actualizar producto</button>
                </div>
            </div>
        </form>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        document.getElementById('fotoInput').addEventListener('change', function(e) {
            const archivo = e.target.files[0];
            const imgPreview = document.getElementById('imgPreview');
            const placeholder = document.getElementById('placeholderPreview');
            if (archivo) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    imgPreview.src = ev.target.result;
                    imgPreview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                };
                reader.readAsDataURL(archivo);
            }
        });
    </script>
</body>

</html>
