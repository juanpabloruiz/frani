<?php
if (isset($_GET['eliminar'])) {
    $idEliminar = (int) $_GET['eliminar'];

    if ($idEliminar > 0) {
        $sentencia = $conexion->prepare("DELETE FROM productos WHERE id = ?");
        $sentencia->bind_param('i', $idEliminar);
        $sentencia->execute();
        $sentencia->close();
    }

    redireccionar('panel.php');
}

if (isset($_POST['guardar_producto'])) {
    $producto = trim($_POST['producto'] ?? '');
    $stock = (int) ($_POST['stock'] ?? 0);
    $costo = (float) numero($_POST['costo'] ?? '0');
    $precio = (float) numero($_POST['precio'] ?? '0');
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);

    if ($producto !== '' && $idCategoria > 0) {
        $sentencia = $conexion->prepare(
            "INSERT INTO productos (producto, costo, precio, stock, id_categoria)
            VALUES (?, ?, ?, ?, ?)"
        );
        $sentencia->bind_param('sddii', $producto, $costo, $precio, $stock, $idCategoria);
        $sentencia->execute();
        $sentencia->close();
    }

    redireccionar('panel.php');
}

if (isset($_POST['actualizar_producto'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $producto = trim($_POST['producto'] ?? '');
    $stock = (int) ($_POST['stock'] ?? 0);
    $costo = (float) numero($_POST['costo'] ?? '0');
    $precio = (float) numero($_POST['precio'] ?? '0');
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);

    if ($id > 0 && $producto !== '' && $idCategoria > 0) {
        $sentencia = $conexion->prepare(
            "UPDATE productos
            SET producto = ?, costo = ?, precio = ?, stock = ?, id_categoria = ?
            WHERE id = ?"
        );
        $sentencia->bind_param('sddiii', $producto, $costo, $precio, $stock, $idCategoria, $id);
        $sentencia->execute();
        $sentencia->close();
    }

    redireccionar('panel.php');
}

$idEditar = (int) ($_GET['editar'] ?? 0);
$editar = null;

if ($idEditar > 0) {
    $sentencia = $conexion->prepare(
        "SELECT id, producto, costo, precio, stock, id_categoria
        FROM productos
        WHERE id = ?"
    );
    $sentencia->bind_param('i', $idEditar);
    $sentencia->execute();
    $resultadoEditar = $sentencia->get_result();
    $editar = $resultadoEditar->fetch_assoc();
    $sentencia->close();
}

$consultaCategoriasProducto = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="h4 mb-3"><?= $editar ? 'Editar producto' : 'Agregar producto' ?></h2>

        <form method="POST" action="<?= e(base_path('panel.php')) ?>" class="row g-3">
            <?php if ($editar): ?>
                <input type="hidden" name="id" value="<?= e((string) $editar['id']) ?>">
            <?php endif; ?>

            <div class="col-md-4">
                <label class="form-label">Producto</label>
                <input type="text" name="producto" class="form-control" value="<?= e($editar['producto'] ?? '') ?>" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Costo</label>
                <input type="number" step="0.01" name="costo" class="form-control" value="<?= e(isset($editar['costo']) ? (string) $editar['costo'] : '') ?>" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Precio</label>
                <input type="number" step="0.01" name="precio" class="form-control" value="<?= e(isset($editar['precio']) ? (string) $editar['precio'] : '') ?>" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" value="<?= e(isset($editar['stock']) ? (string) $editar['stock'] : '0') ?>" min="0" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Categoría</label>
                <select name="id_categoria" class="form-select" required>
                    <option value="">Seleccionar</option>
                    <?php while ($fila = $consultaCategoriasProducto->fetch_assoc()): ?>
                        <?php $selected = (int) ($editar['id_categoria'] ?? 0) === (int) $fila['id']; ?>
                        <option value="<?= e((string) $fila['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                            <?= e($fila['nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" name="<?= $editar ? 'actualizar_producto' : 'guardar_producto' ?>" class="btn btn-primary">
                    <?= $editar ? 'Actualizar producto' : 'Guardar producto' ?>
                </button>

                <?php if ($editar): ?>
                    <a href="<?= e(base_path('panel.php')) ?>" class="btn btn-outline-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
