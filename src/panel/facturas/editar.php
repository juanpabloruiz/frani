<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redireccionar('panel/facturas');
}

$db = conexion();

$stmt = $db->prepare("SELECT id, nombre, total, efectivo, transferencia, deuda, descuento, descuento_importe, detalle, observaciones FROM facturas WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$resultado = $stmt->get_result();
$factura = $resultado->fetch_assoc();
$stmt->close();

if ($factura === null) {
    redireccionar('panel/facturas');
}

$items = [];
if (!empty($factura['detalle'])) {
    foreach (explode(', ', $factura['detalle']) as $parte) {
        if (preg_match('/^(.+?) \((\d+) x ([\d.]+)\)$/', $parte, $m)) {
            $items[] = [
                'nombre' => $m[1],
                'cantidad' => (int) $m[2],
                'precio' => (float) $m[3],
            ];
        }
    }
}

// Las ventas anteriores almacenaban un porcentaje. Al editarlas se muestra
// su importe equivalente, calculado sobre los precios originales del detalle.
$descuentoImporte = $factura['descuento_importe'];
if ($descuentoImporte === null && (float) $factura['descuento'] > 0) {
    $subtotalOriginal = array_sum(array_map(
        static fn(array $item): float => $item['cantidad'] * $item['precio'],
        $items
    ));
    $descuentoImporte = round($subtotalOriginal * (float) $factura['descuento'] / 100, 2);
}

$productos = [];
$res = $db->query("SELECT id, producto, precio FROM productos ORDER BY producto ASC");
while ($fila = $res->fetch_assoc()) {
    $productos[] = [
        'id' => (int) $fila['id'],
        'nombre' => $fila['producto'],
        'precio' => (float) $fila['precio'],
    ];
}
$productosJSON = json_encode($productos, JSON_UNESCAPED_UNICODE);

$categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

foreach ($items as &$item) {
    $item['id'] = null;
    foreach ($productos as $p) {
        if ($p['nombre'] === $item['nombre']) {
            $item['id'] = $p['id'];
            break;
        }
    }
}
unset($item);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php $tituloPagina = 'Editar Venta'; require __DIR__ . '/../_head.php'; ?>
</head>

<body class="sb-app sb-page-scroll">
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="sb-contenido">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Editar venta</h1>
            <a href="<?= e(base_path('panel/facturas')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form id="facturaForm" method="POST" action="<?= e(base_path('panel/facturas/actualizar')) ?>">
                    <?= CSRF_field() ?>
                    <input type="hidden" name="id" value="<?= e((string) $factura['id']) ?>">

                    <div class="mb-3">
                        <label class="form-label">Nombre del cliente</label>
                        <input type="text" id="nombre" name="nombre" class="form-control"
                            value="<?= e($factura['nombre']) ?>">
                    </div>

                    <div id="itemsCards" class="d-grid gap-3 mb-3" aria-label="Productos de la venta"></div>

                    <div class="mb-3">
                        <select id="selectAgregarProducto" class="form-select mb-2" style="max-width: 400px;">
                            <option value="">Seleccione un producto</option>
                        </select>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                            <i class="fa-solid fa-box-open me-1"></i>Agregar producto nuevo
                        </button>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md">
                            <label class="form-label">Efectivo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" id="efectivo" name="efectivo" class="form-control"
                                    min="0" value="<?= e(numero_limpio($factura['efectivo'])) ?>">
                            </div>
                        </div>
                        <div class="col-md">
                            <label class="form-label">Transferencia</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" id="transferencia" name="transferencia" class="form-control"
                                    min="0" value="<?= e(numero_limpio($factura['transferencia'])) ?>">
                            </div>
                        </div>
                    </div>

                    <div id="filaPago2" class="row g-3 mb-2 <?= ((float) $factura['deuda'] > 0) ? '' : 'd-none' ?>">
                        <div class="col-md">
                            <label class="form-label text-muted">Efectivo (Línea 2)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" id="efectivo2" name="efectivo2" class="form-control"
                                    min="0" placeholder="Opcional">
                            </div>
                        </div>
                        <div class="col-md">
                            <label class="form-label text-muted">Transferencia (Línea 2)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" id="transferencia2" name="transferencia2" class="form-control"
                                    min="0" placeholder="Opcional">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 <?= ((float) $factura['deuda'] > 0) ? '' : 'd-none' ?>" id="deudaRow">
                        <label class="form-label fw-bold text-danger">Deuda</label>
                        <input type="number" id="deuda" name="deuda" class="form-control border-danger text-danger"
                            step="0.01" min="0" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="descuento" class="form-label">Descuento</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="descuento" name="descuento" class="form-control" min="0" step="0.01"
                                value="<?= e(numero_limpio($descuentoImporte)) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input type="number" id="total" name="total" class="form-control" step="0.01" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <input type="text" name="observaciones" class="form-control"
                            value="<?= e($factura['observaciones'] ?? '') ?>"
                            placeholder="Ej: Se le hizo un descuento por falla de $ 1000">
                    </div>

                    <div class="d-grid d-md-block">
                        <button type="submit" class="btn btn-primary btn-lg">Actualizar venta</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </main>

    <!-- Modal Nuevo Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalProductoLabel"><i class="fa-solid fa-box-open me-2"></i>Nuevo producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formProductoModal">
                    <?= CSRF_field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="producto" class="form-control" required placeholder="Nombre">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Costo</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="costo" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="precio" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="id_categoria" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= e((string) $cat['id']) ?>"><?= e($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="productoError" class="alert alert-danger d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsCards = document.getElementById("itemsCards");
            const addItemBtn = document.getElementById("addItemBtn");
            const totalField = document.getElementById("total");
            const deudaField = document.getElementById("deuda");
            const deudaRow = document.getElementById("deudaRow");
            const efectivoField = document.getElementById("efectivo");
            const transferenciaField = document.getElementById("transferencia");
            const efectivo2Field = document.getElementById("efectivo2");
            const transferencia2Field = document.getElementById("transferencia2");
            const descuentoField = document.getElementById("descuento");
            const filaPago2 = document.getElementById("filaPago2");
            let productos = <?= $productosJSON ?>;
            let itemIndex = 0;

            function updateTotal() {
                const subtotal = Array.from(document.querySelectorAll(".subtotal"))
                    .reduce((sum, input) => sum + parseFloat(input.value || 0), 0);
                const descuento = Math.max(0, parseFloat(descuentoField.value) || 0);
                descuentoField.max = subtotal.toFixed(2);
                totalField.value = Math.max(0, subtotal - descuento).toFixed(2);
                updateDeuda();
            }

            function updateDeuda() {
                const total = parseFloat(totalField.value) || 0;
                const efectivo = parseFloat(efectivoField.value) || 0;
                const transferencia = parseFloat(transferenciaField.value) || 0;
                const efectivo2 = parseFloat(efectivo2Field.value) || 0;
                const transferencia2 = parseFloat(transferencia2Field.value) || 0;
                const pagado = efectivo + transferencia + efectivo2 + transferencia2;
                const deuda = total - pagado;

                if (deuda > 0) {
                    deudaField.value = deuda.toFixed(2);
                    deudaRow.classList.remove('d-none');
                } else {
                    deudaField.value = '';
                    deudaRow.classList.add('d-none');
                }
            }

            efectivoField.addEventListener("input", updateDeuda);
            transferenciaField.addEventListener("input", updateDeuda);
            efectivo2Field.addEventListener("input", updateDeuda);
            transferencia2Field.addEventListener("input", updateDeuda);
            descuentoField.addEventListener("input", updateTotal);

            function crearFila(selectId, cantidad, precio) {
                const index = itemIndex;
                const row = document.createElement("div");
                row.className = "card venta-item";

                const select = document.createElement("select");
                select.className = "form-select select-producto";
                select.name = `producto_${index}`;
                select.innerHTML = '<option value="">Seleccione un producto</option>';
                productos.forEach(producto => {
                    const option = document.createElement("option");
                    option.value = producto.id;
                    option.dataset.precio = producto.precio;
                    option.textContent = producto.nombre;
                    if (selectId !== null && producto.id === selectId) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });

                const cantidadInput = document.createElement("input");
                cantidadInput.type = "number";
                cantidadInput.className = "form-control cantidad";
                cantidadInput.name = `cantidad_${index}`;
                cantidadInput.min = "1";
                cantidadInput.value = cantidad;

                const precioInput = document.createElement("input");
                precioInput.type = "number";
                precioInput.className = "form-control precio";
                precioInput.name = `precio_${index}`;
                precioInput.step = "0.01";
                precioInput.readOnly = true;
                precioInput.value = precio || select.selectedOptions[0]?.dataset.precio || '';

                const subtotalInput = document.createElement("input");
                subtotalInput.type = "number";
                subtotalInput.className = "form-control subtotal";
                subtotalInput.name = `subtotal_${index}`;
                subtotalInput.step = "0.01";
                subtotalInput.readOnly = true;

                const recalcular = () => {
                    const p = parseFloat(precioInput.value || 0);
                    subtotalInput.value = (p * cantidadInput.value).toFixed(2);
                    updateTotal();
                };

                select.addEventListener("change", () => {
                    const precioSeleccionado = select.selectedOptions[0].dataset.precio;
                    precioInput.value = precioSeleccionado !== undefined ? precioSeleccionado : '';
                    recalcular();

                    const filas = itemsCards.querySelectorAll(".venta-item");
                    const ultimaFila = filas[filas.length - 1];
                    if (row === ultimaFila && select.value !== '') {
                        crearFila(null, 1, 0);
                        itemIndex++;
                    }
                });

                cantidadInput.addEventListener("input", recalcular);

                const btnQuitar = document.createElement("button");
                btnQuitar.type = "button";
                btnQuitar.className = "btn btn-sm btn-danger";
                btnQuitar.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    btnQuitar.addEventListener("click", () => {
                        if (!confirm('¿Está seguro de quitar este ítem?')) return;
                        row.remove();
                        updateTotal();
                    });

                const body = document.createElement('div');
                body.className = 'card-body row g-3 align-items-end m-0';
                [['Producto', select, 'col-12 col-xl-4'], ['Cantidad', cantidadInput, 'col-6 col-xl-2'],
                 ['Precio', precioInput, 'col-6 col-xl-2'], ['Subtotal', subtotalInput, 'col-9 col-xl-3']].forEach(([texto, input, clase]) => {
                    const campo = document.createElement('div');
                    campo.className = clase;
                    const label = document.createElement('label');
                    label.className = 'form-label';
                    label.textContent = texto;
                    input.id = input.name;
                    label.htmlFor = input.id;
                    campo.append(label, input);
                    body.append(campo);
                });
                const acciones = document.createElement('div');
                acciones.className = 'col-3 col-xl-1 text-end';
                btnQuitar.setAttribute('aria-label', 'Quitar producto');
                acciones.append(btnQuitar);
                body.append(acciones);
                row.append(body);
                itemsCards.appendChild(row);

                recalcular();
            }

            <?php foreach ($items as $item): ?>
                crearFila(<?= $item['id'] !== null ? (int) $item['id'] : 'null' ?>, <?= (int) $item['cantidad'] ?>, <?= $item['precio'] ?>);
                itemIndex++;
            <?php endforeach; ?>

            const selectAgregar = document.getElementById('selectAgregarProducto');
            function cargarSelectAgregar() {
                selectAgregar.innerHTML = '<option value="">Seleccione un producto</option>';
                productos.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.dataset.precio = p.precio;
                    opt.textContent = p.nombre + (p.stock != null ? ' (Stock: ' + p.stock + ')' : '');
                    selectAgregar.appendChild(opt);
                });
            }
            cargarSelectAgregar();

            selectAgregar.addEventListener('change', () => {
                const id = selectAgregar.value;
                if (id === '') return;
                crearFila(parseInt(id), 1, 0);
                itemIndex++;
                selectAgregar.value = '';
            });

            document.getElementById('formProductoModal').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const errorDiv = document.getElementById('productoError');
                errorDiv.classList.add('d-none');

                try {
                    const response = await fetch("<?= e(base_path('panel/productos/insertar')) ?>", {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const datos = await response.json();

                    if (datos.ok) {
                        const res = await fetch("<?= e(base_path('panel/obtener_productos')) ?>");
                        productos = await res.json();

                        document.querySelectorAll('.select-producto').forEach(sel => {
                            const opt = document.createElement('option');
                            opt.value = datos.id;
                            opt.dataset.precio = datos.precio;
                            opt.textContent = datos.nombre;
                            sel.appendChild(opt);
                        });

                        cargarSelectAgregar();

                        const filas = itemsCards.querySelectorAll(".venta-item");
                        const ultimaFila = filas[filas.length - 1];
                        const ultimoSelect = ultimaFila ? ultimaFila.querySelector('.select-producto') : null;
                        if (ultimoSelect && ultimoSelect.value === '') {
                            ultimoSelect.value = datos.id;
                            ultimoSelect.dispatchEvent(new Event('change'));
                        } else {
                            crearFila(datos.id, 1, 0);
                            itemIndex++;
                        }

                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalProducto'));
                        modal.hide();
                        this.reset();
                    } else {
                        errorDiv.textContent = 'Error al guardar el producto.';
                        errorDiv.classList.remove('d-none');
                    }
                } catch (err) {
                    errorDiv.textContent = 'Error de conexión.';
                    errorDiv.classList.remove('d-none');
                }
            });
        });
    </script>

</body>

</html>
