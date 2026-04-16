<?php
if (isset($_POST['porcentual'])) {
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);
    $porcentaje = (float) numero($_POST['porcentaje'] ?? '0');

    if ($idCategoria > 0 && $porcentaje !== 0.0) {
        $sentencia = $conexion->prepare(
            "UPDATE productos
            SET precio = ROUND(precio * (1 + ? / 100), 2)
            WHERE id_categoria = ?"
        );
        $sentencia->bind_param('di', $porcentaje, $idCategoria);
        $sentencia->execute();
        $sentencia->close();
    }

    redireccionar('panel.php');
}

$consultaCategoriasPorcentaje = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
?>

<div class="card shadow-sm h-100">
    <div class="card-body">
        <h2 class="h4">Actualizar precios por categoría</h2>
        <form method="POST" action="<?= e(base_path('panel.php')) ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Categoría</label>
                    <select name="id_categoria" class="form-select" required>
                        <option value="">Seleccionar categoría</option>
                        <?php while ($fila = $consultaCategoriasPorcentaje->fetch_assoc()): ?>
                            <option value="<?= e((string) $fila['id']) ?>"><?= e($fila['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Porcentaje</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="porcentaje" class="form-control" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="porcentual" class="btn btn-primary w-100">Aplicar ajuste</button>
                </div>
            </div>
        </form>
    </div>
</div>
