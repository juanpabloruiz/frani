<?php
require_once __DIR__ . '/../../conexion.php';
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
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css?v=3')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Categorías</h1>
            <a href="<?= e(base_path('panel/categorias/nuevo')) ?>" class="btn btn-primary">Nueva categoría</a>
        </div>

        <table class="table">
            <thead class="text-center">
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Agregado</th>
                    <th scope="col">Modificado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $consulta->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($fila['nombre']) ?></td>
                        <td class="text-center"><?= e(date('d/m/Y H:i', strtotime($fila['agregado']))) ?></td>
                        <td class="text-center"><?= $fila['modificado'] ? e(date('d/m/Y H:i', strtotime($fila['modificado']))) : '-' ?></td>
                        <td class="text-center">
                            <a href="<?= e(base_path('panel/categorias/editar?id=' . $fila['id'])) ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="<?= e(base_path('panel/categorias/eliminar')) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría? Los productos asociados no se eliminarán.');">
                                <input type="hidden" name="csrf_token" value="<?= e(CSRF_token()) ?>">
                                <input type="hidden" name="id" value="<?= e((string) $fila['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if ($consulta->num_rows === 0): ?>
                    <tr>
                        <td colspan="4" class="text-center text-secondary">No hay categorías cargadas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
