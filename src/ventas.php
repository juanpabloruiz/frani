<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$consulta = $conexion->query("SELECT nombre, detalle, metodo, total, fecha FROM facturas ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | FRANI</title>
    <link rel="stylesheet" href="<?= e(base_path('css/bootstrap.min.css')) ?>">
</head>

<body>
    <nav class="navbar sticky-top navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= e(base_path()) ?>">FRANI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(base_path()) ?>">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= e(base_path('factura')) ?>">Factura</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= e(base_path('ventas')) ?>">Ventas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container my-3">
        <input type="search" placeholder="Buscar aquí..." name="busqueda" id="buscar" class="form-control">
        <hr>
        <div id="datos">
            <table class="table">
                <tr>
                    <th class="text-center">NOMBRE</th>
                    <th class="text-center">DETALLE</th>
                    <th class="text-center">METODO</th>
                    <th class="text-center">TOTAL</th>
                    <th class="text-center">FECHA</th>
                </tr>
                <?php while ($campo = $consulta->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($campo['nombre']) ?></td>
                        <td><?= e($campo['detalle']) ?></td>
                        <td class="text-center"><?= e($campo['metodo']) ?></td>
                        <td class="text-center">$ <?= e(number_format((float) $campo['total'], 2, ',', '.')) ?></td>
                        <td class="text-center"><?= e(date("d/m/Y H:i", strtotime($campo['fecha']))) ?></td>
                    </tr>
                <?php endwhile; ?>

                <?php if ($consulta->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">No hay facturas registradas.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </main>
    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
    <script src="<?= e(base_path('js/buscar.js')) ?>"></script>
</body>

</html>
