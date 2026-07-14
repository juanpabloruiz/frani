<?php
if (isset($_GET['eliminar'])) {
    $idEliminar = (int) $_GET['eliminar'];

    if ($idEliminar > 0) {
        $sentencia = $conexion->prepare("DELETE FROM productos WHERE id = ?");
        $sentencia->bind_param('i', $idEliminar);
        $sentencia->execute();
        $sentencia->close();
    }

    redireccionar('panel.php');
}

if (isset($_POST['guardar_producto'])) {
    $producto = trim($_POST['producto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $stock = (int) ($_POST['stock'] ?? 0);
    $costo = (float) ($_POST['costo'] ?? '0');
    $precio = (float) ($_POST['precio'] ?? '0');
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);

    if ($producto !== '' && $idCategoria > 0) {
        $descripcionDB = $descripcion !== '' ? $descripcion : null;
        $sentencia = $conexion->prepare(
            "INSERT INTO productos (producto, descripcion, costo, precio, stock, id_categoria)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $sentencia->bind_param('ssddii', $producto, $descripcionDB, $costo, $precio, $stock, $idCategoria);
        $sentencia->execute();
        $sentencia->close();
    }

    redireccionar('panel.php');
}

if (isset($_POST['actualizar_producto'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $producto = trim($_POST['producto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $stock = (int) ($_POST['stock'] ?? 0);
    $costo = (float) ($_POST['costo'] ?? '0');
    $precio = (float) ($_POST['precio'] ?? '0');
    $idCategoria = (int) ($_POST['id_categoria'] ?? 0);

    if ($id > 0 && $producto !== '' && $idCategoria > 0) {
        $descripcionDB = $descripcion !== '' ? $descripcion : null;
        $sentencia = $conexion->prepare(
            "UPDATE productos
            SET producto = ?, descripcion = ?, costo = ?, precio = ?, stock = ?, id_categoria = ?
            WHERE id = ?"
        );
        $sentencia->bind_param('ssddiii', $producto, $descripcionDB, $costo, $precio, $stock, $idCategoria, $id);
        $sentencia->execute();
        $sentencia->close();
    }

    redireccionar('panel.php');
}

$idEditar = (int) ($_GET['editar'] ?? 0);
$editar = null;

if ($idEditar > 0) {
    $sentencia = $conexion->prepare(
        "SELECT id, producto, descripcion, costo, precio, stock, id_categoria
        FROM productos
        WHERE id = ?"
    );
    $sentencia->bind_param('i', $idEditar);
    $sentencia->execute();
    $resultadoEditar = $sentencia->get_result();
    $editar = $resultadoEditar->fetch_assoc();
    $sentencia->close();
}

$consultaCategorias = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
?>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <form id="formProducto" method="POST" action="<?= e(base_path('panel.php')) ?>">
                    <?php if ($editar): ?>
                        <input type="hidden" name="id" value="<?= e((string) $editar['id']) ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del producto</label>
                        <input type="text" name="producto" class="form-control form-control-lg"
                            value="<?= e($editar['producto'] ?? '') ?>" required placeholder="Nombre del producto">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="8"
                            placeholder="Descripción del producto (opcional)"><?= e($editar['descripcion'] ?? '') ?></textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <div class="input-group">
                        <select id="selectCategoria" name="id_categoria" class="form-select" form="formProducto" required>
                            <option value="">Seleccionar</option>
                            <?php while ($fila = $consultaCategorias->fetch_assoc()): ?>
                                <?php $selected = (int) ($editar['id_categoria'] ?? 0) === (int) $fila['id']; ?>
                                <option value="<?= e((string) $fila['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                                    <?= e($fila['nombre']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Costo</label>
                    <input type="number" step="0.01" name="costo" class="form-control" form="formProducto"
                        value="<?= e(isset($editar['costo']) ? (string) $editar['costo'] : '') ?>" required placeholder="0.00">
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" form="formProducto"
                        value="<?= e(isset($editar['precio']) ? (string) $editar['precio'] : '') ?>" required placeholder="0.00">
                </div>

                <div class="mb-0">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-control" form="formProducto"
                        value="<?= e(isset($editar['stock']) ? (string) $editar['stock'] : '0') ?>" min="0" required>
                </div>

                <button type="submit" form="formProducto" name="<?= $editar ? 'actualizar_producto' : 'guardar_producto' ?>" class="btn btn-primary w-100 mt-4">
                    <?= $editar ? 'Actualizar producto' : 'Guardar producto' ?>
                </button>
            </div>
        </div>

        <div class="card shadow-sm bg-light border-0">
            <div class="card-body">
                <h2 class="h5 mb-3">Actualizar precios por categoría</h2>
                <form method="POST" action="<?= e(base_path('panel.php')) ?>">
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select name="id_categoria" class="form-select" required>
                            <option value="">Seleccionar categoría</option>
                            <?php
                            $consultaCategoriasPorcentaje = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
                            while ($fila = $consultaCategoriasPorcentaje->fetch_assoc()):
                            ?>
                                <option value="<?= e((string) $fila['id']) ?>"><?= e($fila['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Porcentaje</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="porcentaje" class="form-control" required placeholder="0">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <button type="submit" name="porcentual" class="btn btn-primary w-100">Aplicar ajuste</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCategoriaLabel">Nueva categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="inputNuevaCategoria" class="form-control" placeholder="Nombre de la categoría" autofocus>
                <div id="mensajeCategoria" class="form-text"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnGuardarCategoria" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputNueva = document.getElementById('inputNuevaCategoria');
    const btnGuardar = document.getElementById('btnGuardarCategoria');
    const selectCategoria = document.getElementById('selectCategoria');
    const mensaje = document.getElementById('mensajeCategoria');
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalCategoria'));

    async function guardarCategoria() {
        const nombre = inputNueva.value.trim();
        if (!nombre) return;

        btnGuardar.disabled = true;
        mensaje.textContent = '';
        mensaje.className = 'form-text';

        try {
            const respuesta = await fetch('<?= e(base_path("agregar_categoria.php")) ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'nombre=' + encodeURIComponent(nombre)
            });

            const datos = await respuesta.json();

            if (datos.ok) {
                const option = document.createElement('option');
                option.value = datos.id;
                option.textContent = datos.nombre;
                option.selected = true;
                selectCategoria.appendChild(option);

                inputNueva.value = '';
                modal.hide();
            } else {
                mensaje.textContent = datos.error || 'No se pudo guardar.';
                mensaje.className = 'form-text text-danger';
            }
        } catch (e) {
            mensaje.textContent = 'Error de conexión.';
            mensaje.className = 'form-text text-danger';
        } finally {
            btnGuardar.disabled = false;
        }
    }

    btnGuardar.addEventListener('click', guardarCategoria);
    inputNueva.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            guardarCategoria();
        }
    });

    document.getElementById('modalCategoria').addEventListener('hidden.bs.modal', function () {
        inputNueva.value = '';
        mensaje.textContent = '';
    });
});
</script>
