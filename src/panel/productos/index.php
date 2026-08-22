<?php
require_once __DIR__ . '/../../conexion.php';
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
    ORDER BY p.producto ASC"
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
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css?v=4')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="mb-4">
            <a href="<?= e(base_path('panel/productos/nuevo')) ?>" class="btn btn-primary btn-lg">Nuevo producto</a>
        </div>

        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                <input type="text" id="buscadorProductos" class="form-control" placeholder="Buscar producto..." autofocus>
            </div>
        </div>

        <table class="table table-bordered" id="tablaProductos">
            <thead class="table-dark text-center text-uppercase">
                <tr class="align-middle"> 
                    <th scope="col">Producto</th>
                    <th scope="col" style="white-space: nowrap; width: 120px;">Costo</th>
                    <th scope="col" style="white-space: nowrap; width: 120px;">Precio</th>
                    <th scope="col">Stock</th>
                    <th scope="col">Categoría</th>
                    <th scope="col" style="white-space: nowrap;">Agregado</th>
                    <th scope="col" style="white-space: nowrap;">Modificado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $consulta->fetch_assoc()): ?>
                    <tr class="align-middle">
                        <td style="background-color: #0d6efd; color: white;"><?= e($fila['producto']) ?></td>
                        <td class="text-end" style="white-space: nowrap;">$ <?= e(number_format((float) $fila['costo'], 2, ',', '.')) ?></td>
                        <td class="text-end" style="white-space: nowrap; background-color: #0d6efd; color: white;">$ <?= e(number_format((float) $fila['precio'], 2, ',', '.')) ?></td>
                        <td class="text-center"><?= e((string) $fila['stock']) ?></td>
                        <td class="text-center"><?= e($fila['categoria']) ?></td>
                        <td class="text-center" style="white-space: nowrap;"><?= e(date('d/m/Y H:i', strtotime($fila['agregado']))) ?></td>
                        <td class="text-center" style="white-space: nowrap;"><?= $fila['modificado'] ? e(date('d/m/Y H:i', strtotime($fila['modificado']))) : '' ?></td>
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
    <script>
        const buscador = document.getElementById('buscadorProductos');
        const filas = document.querySelectorAll('#tablaProductos tbody tr');

        function normalizar(texto) {
            return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        }

        buscador.addEventListener('input', function () {
            const termino = normalizar(this.value);
            filas.forEach(fila => {
                const texto = normalizar(fila.textContent);
                fila.style.display = texto.includes(termino) ? '' : 'none';
            });
        });
    </script>
</body>

</html>
