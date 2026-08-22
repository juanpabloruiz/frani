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
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css?v=5')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="mb-4">
            <a href="<?= e(base_path('panel/categorias/nuevo')) ?>" class="btn btn-primary btn-lg">Nueva categoría</a>
        </div>

        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                <input type="text" id="buscadorCategorias" class="form-control" placeholder="Buscar categoría..." autofocus>
            </div>
        </div>

        <div class="tabla-wrapper">
        <table class="table table-bordered" id="tablaCategorias">
            <thead class="table-dark text-center text-uppercase">
                <tr class="align-middle">
                    <th scope="col">Nombre</th>
                    <th scope="col" style="white-space: nowrap;">Agregado</th>
                    <th scope="col" style="white-space: nowrap;">Modificado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $consulta->fetch_assoc()): ?>
                    <tr class="align-middle">
                        <td><?= e($fila['nombre']) ?></td>
                        <td class="text-center" style="white-space: nowrap;"><?= e(date('d/m/Y H:i', strtotime($fila['agregado']))) ?></td>
                        <td class="text-center" style="white-space: nowrap;"><?= $fila['modificado'] ? e(date('d/m/Y H:i', strtotime($fila['modificado']))) : '' ?></td>
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
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        const buscador = document.getElementById('buscadorCategorias');
        const filas = document.querySelectorAll('#tablaCategorias tbody tr');

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
