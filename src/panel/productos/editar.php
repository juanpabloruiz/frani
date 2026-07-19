<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redireccionar('panel/productos');
}

$db = conexion();

$stmt = $db->prepare(
    "SELECT id, producto, descripcion, costo, precio, stock, id_categoria
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

    <main class="container-fluid my-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h3 mb-0">Editar producto</h1>
            <a href="<?= e(base_path('panel/productos')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="<?= e(base_path('panel/productos/actualizar')) ?>" class="row g-3">
                    <?= CSRF_field() ?>
                    <input type="hidden" name="id" value="<?= e((string) $producto['id']) ?>">

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nombre del producto</label>
                        <input type="text" name="producto" class="form-control form-control-lg"
                            value="<?= e($producto['producto']) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Categoría</label>
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
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="5"><?= e($producto['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Costo</label>
                        <input type="number" step="0.01" name="costo" class="form-control"
                            value="<?= e((string) $producto['costo']) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control"
                            value="<?= e((string) $producto['precio']) ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control"
                            value="<?= e((string) $producto['stock']) ?>" min="0" required>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg">Actualizar producto</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
