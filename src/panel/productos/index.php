<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();

$editando = false;
$producto = [
    'id' => '', 'producto' => '', 'descripcion' => '',
    'costo' => '', 'precio' => '', 'stock' => '',
    'id_categoria' => '', 'foto' => ''
];

$idEditar = (int) ($_GET['id'] ?? 0);
if ($idEditar > 0) {
    $stmt = $db->prepare(
        "SELECT id, producto, foto, descripcion, costo, precio, stock, id_categoria
        FROM productos WHERE id = ?"
    );
    $stmt->bind_param('i', $idEditar);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($producto) {
        $editando = true;
    }
}

$consultaCategorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");

$consulta = $db->query(
    "SELECT
        p.id, p.producto, p.descripcion, p.costo, p.precio,
        p.stock, p.agregado, p.modificado, c.nombre AS categoria
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
                        <form method="POST" action="<?= e(base_path('panel/productos/' . ($editando ? 'actualizar' : 'insertar'))) ?>" enctype="multipart/form-data">
                            <?= CSRF_field() ?>
                            <?php if ($editando): ?>
                                <input type="hidden" name="id" value="<?= e((string) $producto['id']) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="producto" class="form-control" required
                                    value="<?= e($producto['producto']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Costo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="1" name="costo" class="form-control" required
                                        value="<?= e((string) $producto['costo']) ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Precio</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="1" name="precio" class="form-control" required
                                        value="<?= e((string) $producto['precio']) ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" min="0"
                                    value="<?= e((string) $producto['stock']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Categoría</label>
                                <div class="input-group">
                                    <select name="id_categoria" id="selectCategoria" class="form-select" required>
                                        <option value="">Seleccionar</option>
                                        <?php while ($fila = $consultaCategorias->fetch_assoc()): ?>
                                            <?php $selected = (int) ($producto['id_categoria'] ?? 0) === (int) $fila['id']; ?>
                                            <option value="<?= e((string) $fila['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                                                <?= e($fila['nombre']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="4"><?= e($producto['descripcion'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto del producto</label>
                                <input type="file" name="foto" id="fotoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editando ? 'Actualizar producto' : 'Guardar producto' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Tabla -->
            <div class="col-md-8">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="buscadorProductos" class="form-control" placeholder="Buscar producto...">
                    </div>
                </div>

                <table class="table table-hover" id="tablaProductos">
                    <thead class="text-center">
                        <tr class="align-middle">
                            <th scope="col" style="width: 50px;">#</th>
                            <th scope="col">Producto</th>
                            <th scope="col" style="white-space: nowrap; width: 120px;">Costo</th>
                            <th scope="col" style="white-space: nowrap; width: 120px;">Precio</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Categoría</th>
                            <th scope="col" style="white-space: nowrap;">Agregado</th>
                            <th scope="col" style="white-space: nowrap;">Modificado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $token = CSRF_token(); ?>
                        <?php while ($fila = $consulta->fetch_assoc()): ?>
                            <tr class="align-middle <?= $editando && (int) $fila['id'] === (int) $producto['id'] ? 'table-active' : '' ?>"
                                style="cursor: pointer;"
                                data-edit="<?= e(base_path('panel/productos?id=' . $fila['id'])) ?>">
                                <td class="text-center">
                                    <form method="POST" action="<?= e(base_path('panel/productos/eliminar')) ?>" class="d-inline" onsubmit="return confirm('¿Eliminar este producto?');">
                                        <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                                        <input type="hidden" name="id" value="<?= e((string) $fila['id']) ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="event.stopPropagation();"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                                <td class="bg-success text-white"><?= e($fila['producto']) ?></td>
                                <td class="text-center" style="white-space: nowrap;"><?= $fila['costo'] ? e(number_format((float) $fila['costo'], 0, '.', '')) : '' ?></td>
                                <td class="text-center bg-success text-white" style="white-space: nowrap;"><?= $fila['precio'] ? e(number_format((float) $fila['precio'], 0, '.', '')) : '' ?></td>
                                <td class="text-center"><?= e((string) $fila['stock']) ?></td>
                                <td class="text-center"><?= e($fila['categoria']) ?></td>
                                <td class="text-center" style="white-space: nowrap;"><?= e(date('d-m | H:i', strtotime($fila['agregado']))) ?></td>
                                <td class="text-center" style="white-space: nowrap;"><?= $fila['modificado'] ? e(date('d-m | H:i', strtotime($fila['modificado']))) : '' ?></td>
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

        </div>
    </div>

    <!-- Modal Nueva Categoría -->
    <div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title mb-0"><i class="fa-solid fa-tags me-1"></i>Nueva categoría</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="nombreCategoria" class="form-control" placeholder="Nombre" autofocus>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-success" id="btnGuardarCategoria">Guardar</button>
                </div>
            </div>
        </div>
    </div>

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

        filas.forEach(fila => {
            fila.addEventListener('click', function (e) {
                if (e.target.closest('form')) return;
                window.location.href = this.dataset.edit;
            });
        });

        document.getElementById('btnGuardarCategoria').addEventListener('click', function () {
            const nombre = document.getElementById('nombreCategoria').value.trim();
            if (!nombre) return;

            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('csrf_token', '<?= e(CSRF_token()) ?>');

            fetch('<?= e(base_path('panel/categorias/agregar_ajax')) ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(datos => {
                if (datos.ok) {
                    const select = document.getElementById('selectCategoria');
                    const option = new Option(datos.nombre, datos.id, true, true);
                    select.appendChild(option);
                    document.getElementById('nombreCategoria').value = '';
                    bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();
                }
            });
        });

    </script>
</body>

</html>
