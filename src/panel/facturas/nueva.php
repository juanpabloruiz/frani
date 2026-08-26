<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();
$categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Nueva venta</h1>
            <a href="<?= e(base_path('panel/facturas')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <form id="facturaForm" method="POST" action="<?= e(base_path('panel/facturas/insertar')) ?>">
            <?= CSRF_field() ?>

            <div class="mb-3">
                <label class="form-label">Nombre del cliente</label>
                <input type="text" id="nombre" name="nombre" class="form-control">
            </div>

            <div class="table-responsive">
                <table class="table" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="mb-3" id="contenedorBtnProducto" style="display: none;">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                    <i class="fa-solid fa-box-open me-1"></i>Agregar producto nuevo
                </button>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md">
                    <label class="form-label">Efectivo</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="1" id="efectivo" name="efectivo" class="form-control"
                            min="0">
                    </div>
                </div>
                <div class="col-md">
                    <label class="form-label">Transferencia</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="1" id="transferencia" name="transferencia" class="form-control"
                            min="0">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-danger">Deuda</label>
                <input type="number" id="deuda" name="deuda" class="form-control border-danger text-danger"
                    step="1" min="0">
            </div>

            <div class="mb-3">
                <label class="form-label">Total</label>
                <input type="number" id="total" name="total" class="form-control" step="1" readonly>
            </div>

            <div class="d-grid d-md-block">
                <button type="submit" class="btn btn-primary btn-lg">Guardar venta</button>
            </div>
        </form>
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
                                <input type="number" step="1" name="costo" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="1" name="precio" class="form-control" required>
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

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsTable = document.getElementById("itemsTable").querySelector("tbody");
            const contenedorBtn = document.getElementById("contenedorBtnProducto");
            const totalField = document.getElementById("total");
            const deudaField = document.getElementById("deuda");
            const efectivoField = document.getElementById("efectivo");
            const transferenciaField = document.getElementById("transferencia");
            let itemIndex = 0;
            let productosCache = null;

            function updateTotal() {
                const subtotal = Array.from(document.querySelectorAll(".subtotal"))
                    .reduce((sum, input) => sum + parseFloat(input.value || 0), 0);
                totalField.value = subtotal.toFixed(0);
                updateDeuda();
            }

            function updateDeuda() {
                const total = parseFloat(totalField.value) || 0;
                const efectivo = parseFloat(efectivoField.value) || 0;
                const transferencia = parseFloat(transferenciaField.value) || 0;
                const pagado = efectivo + transferencia;
                const deuda = total - pagado;
                deudaField.value = deuda > 0 ? deuda.toFixed(0) : '';
            }

            deudaField.addEventListener("input", updateDeuda);
            efectivoField.addEventListener("input", updateDeuda);
            transferenciaField.addEventListener("input", updateDeuda);

            async function cargarProductos() {
                if (!productosCache) {
                    const response = await fetch("<?= e(base_path('panel/obtener_productos')) ?>");
                    productosCache = await response.json();
                }
                return productosCache;
            }

            function agregarFilaVacia() {
                return new Promise(resolve => {
                const row = document.createElement("tr");
                cargarProductos().then(productos => {
                    const select = document.createElement("select");
                    select.className = "form-select select-producto";
                    select.name = `producto_${itemIndex}`;
                    select.innerHTML = '<option value="">Seleccione un producto</option>';
                    productos.forEach(producto => {
                        const stockAttr = producto.stock !== null ? `data-stock="${producto.stock}"` : '';
                        select.innerHTML += `<option value="${producto.id}" data-precio="${producto.precio}" ${stockAttr}>${producto.producto}${producto.stock !== null ? ' (Stock: ' + producto.stock + ')' : ''}</option>`;
                    });

                    const cantidadInput = document.createElement("input");
                    cantidadInput.type = "number";
                    cantidadInput.className = "form-control cantidad";
                    cantidadInput.name = `cantidad_${itemIndex}`;
                    cantidadInput.min = "1";
                    cantidadInput.value = "1";

                    const precioInput = document.createElement("input");
                    precioInput.type = "number";
                    precioInput.className = "form-control precio";
                    precioInput.name = `precio_${itemIndex}`;
                    precioInput.step = "1";
                    precioInput.readOnly = true;

                    const subtotalInput = document.createElement("input");
                    subtotalInput.type = "number";
                    subtotalInput.className = "form-control subtotal";
                    subtotalInput.name = `subtotal_${itemIndex}`;
                    subtotalInput.step = "1";
                    subtotalInput.readOnly = true;

                    select.addEventListener("change", () => {
                        const precioSeleccionado = select.selectedOptions[0].dataset.precio;
                        const precio = precioSeleccionado !== undefined ? parseFloat(precioSeleccionado) : 0;
                        precioInput.value = precioSeleccionado !== undefined ? precio.toFixed(0) : '';
                        subtotalInput.value = precioSeleccionado !== undefined ? (precio * cantidadInput.value).toFixed(0) : '';
                        updateTotal();

                        const filas = itemsTable.querySelectorAll("tr");
                        const ultimaFila = filas[filas.length - 1];
                        if (row === ultimaFila && select.value !== '') {
                            agregarFilaVacia();
                        }
                    });

                    cantidadInput.addEventListener("input", () => {
                        const precio = parseFloat(precioInput.value || 0);
                        subtotalInput.value = (precio * cantidadInput.value).toFixed(0);
                        updateTotal();
                    });

                    const btnQuitar = document.createElement("button");
                    btnQuitar.type = "button";
                    btnQuitar.className = "btn btn-sm btn-danger";
                    btnQuitar.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    btnQuitar.addEventListener("click", () => {
                        if (!confirm('¿Está seguro de quitar este ítem?')) return;
                        row.remove();
                        updateTotal();
                        contenedorBtn.style.display = itemsTable.children.length > 0 ? '' : 'none';
                    });

                    const tdProducto = document.createElement("td");
                    tdProducto.appendChild(select);
                    const tdCantidad = document.createElement("td");
                    tdCantidad.appendChild(cantidadInput);
                    const tdPrecio = document.createElement("td");
                    tdPrecio.className = "text-center";
                    tdPrecio.appendChild(precioInput);
                    const tdSubtotal = document.createElement("td");
                    tdSubtotal.className = "text-center";
                    tdSubtotal.appendChild(subtotalInput);
                    const tdQuitar = document.createElement("td");
                    tdQuitar.className = "text-center";
                    tdQuitar.appendChild(btnQuitar);

                    row.appendChild(tdProducto);
                    row.appendChild(tdCantidad);
                    row.appendChild(tdPrecio);
                    row.appendChild(tdSubtotal);
                    row.appendChild(tdQuitar);
                    itemsTable.appendChild(row);

                    itemIndex++;
                    contenedorBtn.style.display = '';
                    resolve();
                });
                });
            }

            agregarFilaVacia();

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
                        productosCache = null;

                        const selects = itemsTable.querySelectorAll('.select-producto');
                        let selectVacio = null;
                        selects.forEach(sel => {
                            if (sel.value === '') selectVacio = sel;
                        });

                        if (selectVacio) {
                            cargarProductos().then(productos => {
                                const opt = document.createElement('option');
                                opt.value = datos.id;
                                opt.textContent = datos.nombre;
                                opt.dataset.precio = datos.precio;
                                opt.selected = true;
                                selectVacio.appendChild(opt);
                                selectVacio.dispatchEvent(new Event('change'));
                            });
                        } else {
                            agregarFilaVacia().then(() => {
                                const selects2 = itemsTable.querySelectorAll('.select-producto');
                                const ultimo = selects2[selects2.length - 1];
                                cargarProductos().then(productos => {
                                    const opt = document.createElement('option');
                                    opt.value = datos.id;
                                    opt.textContent = datos.nombre;
                                    opt.dataset.precio = datos.precio;
                                    opt.selected = true;
                                    ultimo.appendChild(opt);
                                    ultimo.dispatchEvent(new Event('change'));
                                });
                            });
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
