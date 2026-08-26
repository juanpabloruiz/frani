<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();
$consulta = $db->query(
    "SELECT
        f.id,
        f.nombre,
        f.detalle,
        f.total,
        f.efectivo,
        f.transferencia,
        f.deuda,
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
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="mb-4">
            <a href="<?= e(base_path('panel/facturas/nueva')) ?>" class="btn btn-primary btn-lg">Nueva venta</a>
        </div>

        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                <input type="text" id="buscadorFacturas" class="form-control" placeholder="Buscar venta...">
            </div>
        </div>

        <table class="table table-hover" id="tablaFacturas">
            <thead class="text-center">
                <tr class="align-middle">
                    <th scope="col" style="width: 50px;">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Detalle</th>
                    <th scope="col" style="white-space: nowrap; width: 120px;">Efectivo</th>
                    <th scope="col" style="white-space: nowrap; width: 120px;">Transferencia</th>
                    <th scope="col" style="white-space: nowrap; width: 120px;">Total</th>
                    <th scope="col" style="white-space: nowrap; width: 120px;">Deuda</th>
                    <th scope="col" style="white-space: nowrap;">Agregado</th>
                    <th scope="col" style="white-space: nowrap;">Modificado</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($campo = $consulta->fetch_assoc()): ?>
                    <tr class="align-middle" style="cursor: pointer;" data-edit="<?= e(base_path('panel/facturas/editar?id=' . $campo['id'])) ?>">
                        <td class="text-center">
                            <form method="POST" action="<?= e(base_path('panel/facturas/eliminar')) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta venta?');">
                                <input type="hidden" name="csrf_token" value="<?= e(CSRF_token()) ?>">
                                <input type="hidden" name="id" value="<?= e((string) $campo['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="event.stopPropagation();"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                        <td><?= e($campo['nombre']) ?></td>
                        <td><?= e($campo['detalle']) ?></td>
                        <td class="text-center" style="white-space: nowrap;"><?= $campo['efectivo'] ? e(number_format((float) $campo['efectivo'], 0, '.', '')) : '' ?></td>
                        <td class="text-center" style="white-space: nowrap;"><?= $campo['transferencia'] ? e(number_format((float) $campo['transferencia'], 0, '.', '')) : '' ?></td>
                        <td class="text-center bg-success text-white" style="white-space: nowrap;"><?= $campo['total'] ? e(number_format((float) $campo['total'], 0, '.', '')) : '' ?></td>
                        <td class="text-center text-danger fw-bold" style="white-space: nowrap;"><?= $campo['deuda'] ? e(number_format((float) $campo['deuda'], 0, '.', '')) : '' ?></td>
                        <td class="text-center" style="white-space: nowrap;"><?= e(date('d-m | H:i', strtotime($campo['agregado']))) ?></td>
                        <td class="text-center" style="white-space: nowrap;"><?= $campo['modificado'] ? e(date('d-m | H:i', strtotime($campo['modificado']))) : '' ?></td>
                    </tr>
                <?php endwhile; ?>

                <?php if ($consulta->num_rows === 0): ?>
                    <tr>
                        <td colspan="9" class="text-center text-secondary">No hay ventas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        const buscador = document.getElementById('buscadorFacturas');
        const filas = document.querySelectorAll('#tablaFacturas tbody tr');

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

        filas.forEach(fila => {
            fila.addEventListener('click', function (e) {
                if (e.target.closest('form')) return;
                window.location.href = this.dataset.edit;
            });
        });
    </script>
</body>

</html>
