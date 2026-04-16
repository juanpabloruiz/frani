<?php
$consultaProductos = $conexion->query(
    "SELECT
        p.id,
        p.producto,
        p.costo,
        p.precio,
        p.stock,
        p.agregado,
        p.modificado,
        c.nombre AS categoria
    FROM productos p
    INNER JOIN categorias c ON c.id = p.id_categoria
    ORDER BY p.id DESC"
);
?>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h2 class="h4 mb-0">Productos cargados</h2>
            <span class="text-secondary"><?= e((string) $consultaProductos->num_rows) ?> registros</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Costo</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Stock</th>
                        <th>Categoría</th>
                        <th>Agregado</th>
                        <th>Modificado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $consultaProductos->fetch_assoc()): ?>
                        <tr>
                            <td><?= e($fila['producto']) ?></td>
                            <td class="text-end">$ <?= e(number_format((float) $fila['costo'], 2, ',', '.')) ?></td>
                            <td class="text-end">$ <?= e(number_format((float) $fila['precio'], 2, ',', '.')) ?></td>
                            <td class="text-center"><?= e((string) $fila['stock']) ?></td>
                            <td><?= e($fila['categoria']) ?></td>
                            <td><?= e(date('d/m/Y H:i', strtotime($fila['agregado']))) ?></td>
                            <td>
                                <?= $fila['modificado'] ? e(date('d/m/Y H:i', strtotime($fila['modificado']))) : '-' ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= e(base_path('panel.php?editar=' . $fila['id'])) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="<?= e(base_path('panel.php?eliminar=' . $fila['id'])) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                    <?php if ($consultaProductos->num_rows === 0): ?>
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-4">No hay productos cargados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
