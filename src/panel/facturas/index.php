<?php
require_once __DIR__ . '/../../conexion.php';
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
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css?v=3')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="mb-4">
            <a href="<?= e(base_path('panel/facturas/nueva')) ?>" class="btn btn-primary">Nueva factura</a>
        </div>

        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                <input type="text" id="buscadorFacturas" class="form-control" placeholder="Buscar factura..." autofocus>
            </div>
        </div>

        <table class="table" id="tablaFacturas">
            <thead class="table-dark text-center text-uppercase">
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Detalle</th>
                    <th scope="col">Método</th>
                    <th scope="col">Total</th>
                    <th scope="col">Agregado</th>
                    <th scope="col">Modificado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($campo = $consulta->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($campo['nombre']) ?></td>
                        <td><?= e($campo['detalle']) ?></td>
                        <td class="text-center"><?= e($campo['metodo']) ?></td>
                        <td class="text-end">$ <?= e(number_format((float) $campo['total'], 2, ',', '.')) ?></td>
                        <td class="text-center"><?= e(date('d/m/Y H:i', strtotime($campo['agregado']))) ?></td>
                        <td class="text-center"><?= $campo['modificado'] ? e(date('d/m/Y H:i', strtotime($campo['modificado']))) : '-' ?></td>
                        <td class="text-center">
                            <a href="<?= e(base_path('panel/facturas/editar?id=' . $campo['id'])) ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="<?= e(base_path('panel/facturas/eliminar')) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar esta factura?');">
                                <input type="hidden" name="csrf_token" value="<?= e(CSRF_token()) ?>">
                                <input type="hidden" name="id" value="<?= e((string) $campo['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if ($consulta->num_rows === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center text-secondary">No hay facturas registradas.</td>
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
    </script>
</body>

</html>
