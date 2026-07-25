<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$db = conexion();
$consulta = $db->query("SELECT id, nombre, agregado, modificado FROM categorias ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container-fluid py-3 px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h3 mb-0">Categorías</h1>
            <a href="<?= e(base_path('panel/categorias/nuevo')) ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nueva categoría
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <span class="text-secondary"><?= e((string) $consulta->num_rows) ?> registros</span>

                <div class="table-responsive mt-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Agregado</th>
                                <th>Modificado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($fila = $consulta->fetch_assoc()): ?>
                                <tr>
                                    <td><?= e($fila['nombre']) ?></td>
                                    <td><?= e(date('d/m/Y H:i', strtotime($fila['agregado']))) ?></td>
                                    <td><?= $fila['modificado'] ? e(date('d/m/Y H:i', strtotime($fila['modificado']))) : '-' ?></td>
                                    <td class="text-center">
                                        <a href="<?= e(base_path('panel/categorias/editar?id=' . $fila['id'])) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="<?= e(base_path('panel/categorias/eliminar?id=' . $fila['id'])) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta categoría? Los productos asociados no se eliminarán.');">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if ($consulta->num_rows === 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">No hay categorías cargadas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>

</body>

</html>
