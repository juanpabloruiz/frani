<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$db = conexion();
$consulta = $db->query(
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
    <title>Facturas | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <main class="container-fluid my-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h3 mb-0">Facturas</h1>
            <a href="<?= e(base_path('panel/facturas/nueva')) ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nueva factura
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
                                <th>Detalle</th>
                                <th>Método</th>
                                <th class="text-end">Total</th>
                                <th>Agregado</th>
                                <th>Modificado</th>
                                <th class="text-center">Acciones</th>
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
                                    <td><?= $campo['modificado'] ? e(date('d/m/Y H:i', strtotime($campo['modificado']))) : '-' ?></td>
                                    <td class="text-center">
                                        <a href="<?= e(base_path('panel/facturas/eliminar?id=' . $campo['id'])) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta factura?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if ($consulta->num_rows === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-secondary py-4">No hay facturas registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
