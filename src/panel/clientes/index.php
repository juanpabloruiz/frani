<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();

$editando = false;
$cliente = [
    'id' => '', 'nombre' => '', 'telefono' => '', 'foto' => ''
];

$idEditar = (int) ($_GET['id'] ?? 0);
if ($idEditar > 0) {
    $stmt = $db->prepare(
        "SELECT id, nombre, foto, telefono
        FROM clientes WHERE id = ?"
    );
    $stmt->bind_param('i', $idEditar);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($cliente) {
        $editando = true;
    }
}

$consulta = $db->query(
    "SELECT id, nombre, telefono, foto, agregado, modificado
    FROM clientes
    ORDER BY nombre ASC"
);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container-fluid">
        <div class="row g-4">

            <!-- Columna izquierda: Formulario -->
            <div class="col-md-4" style="position: sticky; top: 84px; align-self: flex-start;">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="<?= e(base_path('panel/clientes/' . ($editando ? 'actualizar' : 'insertar') . ($editando ? '#cliente-' . $cliente['id'] : ''))) ?>" enctype="multipart/form-data">
                            <?= CSRF_field() ?>
                            <?php if ($editando): ?>
                                <input type="hidden" name="id" value="<?= e((string) $cliente['id']) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required
                                    value="<?= e($cliente['nombre']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                    value="<?= e($cliente['telefono'] ?? '') ?>" placeholder="Teléfono (opcional)">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto del cliente</label>
                                <input type="file" name="foto" id="fotoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                <?php if ($editando && !empty($cliente['foto'])): ?>
                                    <small class="text-muted">Dejar vacío para mantener la foto actual.</small>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editando ? 'Actualizar cliente' : 'Guardar cliente' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Tabla -->
            <div class="col-md-8">
                <div style="position: sticky; top: 76px; z-index: 10; background: white; padding: 16px; border-bottom: 1px solid #dee2e6; margin-bottom: 16px;">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="buscadorClientes" class="form-control" placeholder="Buscar cliente...">
                    </div>
                </div>

                <div id="contenedorTabla" class="card shadow-sm" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                    <table class="table table-hover table-bordered mb-0" id="tablaClientes">
                    <thead class="text-center">
                        <tr class="align-middle">
                            <th scope="col" style="width: 50px;">#</th>
                            <th scope="col">Cliente</th>
                            <th scope="col" style="white-space: nowrap;">Teléfono</th>
                            <th scope="col" style="white-space: nowrap;">Agregado</th>
                            <th scope="col" style="white-space: nowrap;">Modificado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $token = CSRF_token(); ?>
                        <?php while ($fila = $consulta->fetch_assoc()): ?>
                            <tr id="cliente-<?= e((string) $fila['id']) ?>" class="align-middle <?= $editando && (int) $fila['id'] === (int) $cliente['id'] ? 'table-active' : '' ?>"
                                style="cursor: pointer;"
                                data-edit="<?= e(base_path('panel/clientes?id=' . $fila['id'])) ?>">
                                <td class="text-center">
                                    <form method="POST" action="<?= e(base_path('panel/clientes/eliminar')) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar este cliente?');">
                                        <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                                        <input type="hidden" name="id" value="<?= e((string) $fila['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="event.stopPropagation();"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                                <td class="bg-success text-white"><?= e($fila['nombre']) ?></td>
                                <td class="text-center" style="white-space: nowrap;"><?= $fila['telefono'] ? e($fila['telefono']) : '' ?></td>
                                <td class="text-center" style="white-space: nowrap;"><?= e(date('d-m | H:i', strtotime($fila['agregado']))) ?></td>
                                <td class="text-center" style="white-space: nowrap;"><?= $fila['modificado'] ? e(date('d-m | H:i', strtotime($fila['modificado']))) : '' ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($consulta->num_rows === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary">No hay clientes cargados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        const buscador = document.getElementById('buscadorClientes');
        const filas = document.querySelectorAll('#tablaClientes tbody tr');

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

        <?php if ($editando): ?>
        window.addEventListener('load', () => {
            const fila = document.getElementById('cliente-<?= e((string) $cliente['id']) ?>');
            const contenedor = document.getElementById('contenedorTabla');
            if (fila && contenedor) {
                contenedor.scrollTop = fila.offsetTop;
            }
        });
        <?php endif; ?>

    </script>
</body>

</html>
