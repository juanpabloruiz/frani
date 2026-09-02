<?php
require_once __DIR__ . '/../../conexion.php';
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

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Nuevo producto</h1>
            <a href="<?= e(base_path('panel/productos')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <form method="POST" action="<?= e(base_path('panel/productos/insertar')) ?>" enctype="multipart/form-data" class="row g-3">
            <?= CSRF_field() ?>

            <div class="col-md">
                <label class="form-label">Nombre</label>
                <input type="text" name="producto" class="form-control" required placeholder="Nombre">
            </div>

            <div class="col-md">
                <label class="form-label">Costo</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" name="costo" class="form-control" required>
                </div>
            </div>

            <div class="col-md">
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
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones (opcional)"></textarea>
            </div>

            <div class="col-12">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="5" placeholder="Descripción (opcional)"></textarea>
            </div>

            <div class="col-md-6">
                <label class="form-label">Foto del producto</label>
                <input type="file" name="foto" id="fotoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <small class="text-muted">Formatos: JPG, JPEG, PNG, WEBP (máx. 5MB)</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Vista previa</label>
                <div id="vistaPrevia" class="border rounded p-2 text-center" style="min-height: 120px;">
                    <img id="imgPreview" src="" alt="Vista previa" style="max-height: 100px; display: none;">
                    <p id="placeholderPreview" class="text-muted mb-0 mt-2">Sin imagen seleccionada</p>
                </div>
            </div>

            <div class="col-12">
                <div class="d-grid d-md-block">
                    <button type="submit" class="btn btn-primary btn-lg">Guardar producto</button>
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
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(archivo);
            } else {
                imgPreview.style.display = 'none';
                placeholder.style.display = 'block';
            }
        });
    </script>
</body>

</html>
