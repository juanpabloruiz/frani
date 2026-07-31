<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$db = conexion();
$consulta = $consulta = $db->query(
    "SELECT
        p.id,
        p.producto,
        p.descripcion,
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
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css?v=3')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Productos</h1>
            <a href="<?= e(base_path('panel/productos/nuevo')) ?>" class="btn btn-primary">Nuevo producto</a>
        </div>

        <table class="table">
            <thead class="table-dark text-uppercase text-center">
                <tr>
                    <th scope="col">Producto</th>
                    <th scope="col">Costo</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Stock</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Agregado</th>
                    <th scope="col">Modificado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $consulta->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($fila['producto']) ?></td>
                        <td class="text-end">$ <?= e(number_format((float) $fila['costo'], 2, ',', '.')) ?></td>
                        <td class="table-dark text-end">$ <?= e(number_format((float) $fila['precio'], 2, ',', '.')) ?></td>
                        <td class="text-center"><?= e((string) $fila['stock']) ?></td>
                        <td class="text-center"><?= e($fila['categoria']) ?></td>
                        <td class="text-center"><?= e(date('d/m/Y H:i', strtotime($fila['agregado']))) ?></td>
                        <td class="text-center"><?= $fila['modificado'] ? e(date('d/m/Y H:i', strtotime($fila['modificado']))) : '-' ?></td>
                        <td class="text-center">
                            <a href="<?= e(base_path('panel/productos/editar?id=' . $fila['id'])) ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i></a>
                            <?php $token = CSRF_token(); ?>
                            <form method="POST" action="<?= e(base_path('panel/productos/eliminar')) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar este producto?');">
                                <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                                <input type="hidden" name="id" value="<?= e((string) $fila['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if ($consulta->num_rows === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center text-secondary">No hay productos cargados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
