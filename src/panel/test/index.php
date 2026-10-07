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
        p.stock, p.agregado, p.modificado, p.foto, c.nombre AS categoria
    FROM productos p
    INNER JOIN categorias c ON c.id = p.id_categoria
    ORDER BY p.producto ASC"
);

$token = CSRF_token();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php $tituloPagina = 'Test'; require __DIR__ . '/../_head.php'; ?>
</head>

<body class="sb-app">

    <?php require __DIR__ . '/menu.php'; ?>

    <div class="sb-contenido sb-workspace">

        <div class="row g-3">

            <!-- Columna izquierda: Formulario -->
            <div class="col-md-4 col-xl-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST"
                            action="<?= e(base_path('panel/test/' . ($editando ? 'actualizar' : 'insertar') . ($editando ? '#producto-' . $producto['id'] : ''))) ?>"
                            enctype="multipart/form-data" id="formProducto">
                            <?= CSRF_field() ?>
                            <?php if ($editando): ?>
                                <input type="hidden" name="id" value="<?= e((string) $producto['id']) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="producto" class="form-control" required
                                    value="<?= e($producto['producto']) ?>">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label">Costo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="costo" class="form-control" required
                                            value="<?= e(numero_limpio($producto['costo'])) ?>">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="precio" class="form-control" required
                                            value="<?= e(numero_limpio($producto['precio'])) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" min="0"
                                    value="<?= e((string) $producto['stock']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Categoria</label>
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
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                        data-bs-target="#modalCategoria" title="Nueva categoria">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripcion</label>
                                <textarea name="descripcion" class="form-control" rows="4"><?= e($producto['descripcion'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto del producto</label>
                                <input type="file" name="foto" id="fotoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vista previa</label>
                                <div id="vistaPrevia" class="border rounded p-2 text-center" style="min-height: 120px;">
                                    <?php if (!empty($producto['foto'])): ?>
                                        <img id="imgPreview" class="img-thumbnail" src="<?= e(base_path('img/productos/' . $producto['foto'] . '.jpg')) ?>" alt="Vista previa" style="width: 100%; height: auto;">
                                    <?php else: ?>
                                        <img id="imgPreview" src="" alt="Vista previa" style="width: 100%; height: auto; display: none;">
                                        <p id="placeholderPreview" class="text-muted mb-0 mt-2">Sin imagen</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <?= $editando ? 'Actualizar' : 'Guardar' ?>
                                </button>
                                <?php if ($editando): ?>
                                    <a class="btn btn-outline-secondary" href="<?= e(base_path('panel/test')) ?>">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: listado en cards -->
            <div class="col-md-8 col-xl-9">

                <div class="sb-buscador">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="buscadorProductos" class="form-control" placeholder="Buscar producto..." aria-label="Buscar producto">
                    </div>
                </div>

                <div class="row g-3 sb-lista" id="listaProductos" data-search-input="buscadorProductos">
                    <?php while ($fila = $consulta->fetch_assoc()): ?>
                        <?php $activo = $editando && (int) $fila['id'] === (int) $producto['id']; ?>
                        <div class="col-12 col-lg-6 col-xxl-4" data-card-item>
                            <div class="card sb-card h-100<?= $activo ? ' activo' : '' ?>"
                                id="producto-<?= e((string) $fila['id']) ?>"
                                tabindex="0" role="link" aria-label="Editar <?= e($fila['producto']) ?>"
                                data-edit="<?= e(base_path('panel/test?id=' . $fila['id'])) ?>">

                                <div class="card-body d-flex gap-3">
                                    <?php if (!empty($fila['foto'])): ?>
                                        <img class="sb-thumb" src="<?= e(base_path('img/productos/' . $fila['foto'] . '.jpg')) ?>"
                                            alt="<?= e($fila['producto']) ?>">
                                    <?php else: ?>
                                        <div class="sb-thumb sb-thumb-vacio"><i class="fa-solid fa-image"></i></div>
                                    <?php endif; ?>

                                    <div class="flex-grow-1 sb-min0">
                                        <h2 class="sb-titulo"><?= e($fila['producto']) ?></h2>

                                        <div class="sb-meta">
                                            <span class="sb-chip"><?= e($fila['categoria']) ?></span>
                                            <?php if ($fila['stock'] !== null): ?>
                                                <span>Stock: <strong><?= e((string) $fila['stock']) ?></strong></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="sb-datos">
                                            <?php if ($fila['costo'] > 0): ?>
                                                <span>Costo <span class="sb-valor">$ <?= e(moneda($fila['costo'])) ?></span></span>
                                            <?php endif; ?>
                                            <?php if ($fila['precio'] > 0): ?>
                                                <span>Precio <span class="sb-valor text-success">$ <?= e(moneda($fila['precio'])) ?></span></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="sb-meta mt-2">
                                            <span><i class="fa-regular fa-clock me-1"></i><?= e(date('d-m | H:i', strtotime($fila['agregado']))) ?></span>
                                            <?php if ($fila['modificado']): ?>
                                                <span><i class="fa-solid fa-pen me-1"></i><?= e(date('d-m | H:i', strtotime($fila['modificado']))) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="sb-accion">
                                        <form method="POST" action="<?= e(base_path('panel/test/eliminar')) ?>"
                                            onsubmit="return confirm('¿Eliminar este producto?');">
                                            <input type="hidden" name="csrf_token" value="<?= e($token) ?>">
                                            <input type="hidden" name="id" value="<?= e((string) $fila['id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                onclick="event.stopPropagation();">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php if ($consulta->num_rows === 0): ?>
                    <div class="sb-vacio">No hay productos cargados.</div>
                <?php endif; ?>

            </div>

        </div>
    </div>

    </main>

    <!-- Modal Nueva Categoria -->
    <div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title mb-0"><i class="fa-solid fa-tags me-1"></i>Nueva categoria</h6>
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

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
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
                    if (!datos.ok) return;
                    ['selectCategoria'].forEach(id => {
                        const select = document.getElementById(id);
                        if (select) select.add(new Option(datos.nombre, datos.id, true, true));
                    });
                    document.getElementById('nombreCategoria').value = '';
                    bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();
                });
        });

        document.getElementById('fotoInput')?.addEventListener('change', function (e) {
            const archivo = e.target.files[0];
            const imgPreview = document.getElementById('imgPreview');
            const placeholder = document.getElementById('placeholderPreview');
            if (archivo) {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    imgPreview.src = ev.target.result;
                    imgPreview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                };
                reader.readAsDataURL(archivo);
            }
        });

        <?php if ($editando): ?>
        // En movil el formulario queda arriba de todo, asi que hay que llevar
        // la vista hasta el. En pantallas anchas se mantiene el comportamiento
        // de centrar la card que se esta editando.
        (function irAlFormulario() {
            const card = document.getElementById('producto-<?= e((string) $producto['id']) ?>');
            const esPantallaAncha = window.matchMedia('(min-width: 768px)').matches;

            if (esPantallaAncha) {
                card?.scrollIntoView({ block: 'center' });
                return;
            }

            const topbar = document.querySelector('.sb-topbar');
            const altoTopbar = topbar && getComputedStyle(topbar).display !== 'none'
                ? topbar.offsetHeight
                : 0;
            const destino = document.querySelector('.sb-contenido') || document.getElementById('formProducto');
            if (!destino) return;

            window.scrollTo({
                top: Math.max(0, destino.getBoundingClientRect().top + window.scrollY - altoTopbar - 8),
                behavior: 'auto'
            });
        })();
        <?php endif; ?>
    </script>

</body>

</html>
