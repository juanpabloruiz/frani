<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$consulta = $conexion->query(
    "SELECT
        f.id,
        f.nombre,
        f.detalle,
        f.metodo,
        f.total,
        f.agregado,
        f.modificado
    FROM facturas f
    ORDER BY f.id DESC"
);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/menu.php'; ?>

    <main class="container-fluid my-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h2 class="h4 mb-0">Ventas realizadas</h2>
                    <span class="text-secondary"><?= e((string) $consulta->num_rows) ?> registros</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Detalle</th>
                                <th>Método</th>
                                <th class="text-end">Total</th>
                                <th>Agregado</th>
                                <th>Modificado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($campo = $consulta->fetch_assoc()): ?>
                                <tr>
                                    <td><?= e($campo['nombre']) ?></td>
                                    <td><?= e($campo['detalle']) ?></td>
                                    <td><?= e($campo['metodo']) ?></td>
                                    <td class="text-end">$ <?= e(number_format((float) $campo['total'], 2, ',', '.')) ?></td>
                                    <td><?= e(date('d/m/Y H:i', strtotime($campo['agregado']))) ?></td>
                                    <td>
                                        <?= $campo['modificado'] ? e(date('d/m/Y H:i', strtotime($campo['modificado']))) : '-' ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if ($consulta->num_rows === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">No hay ventas registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
