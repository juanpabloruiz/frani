<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();
$categorias = $db->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="auto">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Factura | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
    <script src="<?= e(base_path('../../js/tema.js')) ?>"></script>
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Nueva factura</h1>
            <a href="<?= e(base_path('panel/facturas')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <form id="facturaForm" method="POST" action="<?= e(base_path('panel/facturas/insertar')) ?>">
            <?= CSRF_field() ?>

            <div class="mb-3">
                <label class="form-label">Nombre del cliente</label>
                <input type="text" id="nombre" name="nombre" class="form-control">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="table-dark text-uppercase">
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

            <div class="mb-3">
                <button type="button" class="btn btn-success" id="addItemBtn">
                    <i class="fa-solid fa-plus me-1"></i>Agregar ítem
                </button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
                    <i class="fa-solid fa-box-open me-1"></i>Agregar producto nuevo
                </button>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md">
                    <label class="form-label">Efectivo</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" id="efectivo" name="efectivo" class="form-control"
                            placeholder="0.00" min="0">
                    </div>
                </div>
                <div class="col-md">
                    <label class="form-label">Transferencia</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" id="transferencia" name="transferencia" class="form-control"
                            placeholder="0.00" min="0">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-danger">Deuda</label>
                <input type="number" id="deuda" name="deuda" class="form-control border-danger text-danger"
                    step="0.01" min="0" placeholder="0.00">
            </div>

            <div class="mb-3">
                <label class="form-label">Total</label>
                <input type="number" id="total" name="total" class="form-control" step="0.01" readonly>
            </div>

            <div class="d-grid d-md-block">
                <button type="submit" class="btn btn-primary btn-lg">Guardar factura</button>
            </div>
        </form>
        </div>
    </main>

    <!-- Modal Nuevo Producto -->
    <div class="modal fade" id="modalProducto" tabindex="-1" aria-labelledby="modalProductoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalProductoLabel"><i class="fa-solid fa-box-open me-2"></i>Nuevo producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <form id="formProductoModal">
                    <?= CSRF_field() ?>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="producto" class="form-control" required placeholder="Nombre">
                            </div>
                            <div class="col-md">
                                <label class="form-label">Costo</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="costo" class="form-control" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md">
                                <label class="form-label">Precio</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="precio" class="form-control" required placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" min="0">
                            </div>
                            <div class="col-md">
                                <label class="form-label">Categoría</label>
                                <select name="id_categoria" class="form-select" required>
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= e((string) $cat['id']) ?>"><?= e($cat['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción (opcional)"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Foto del producto</label>
                                <input type="file" name="foto" id="fotoInputModal" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                <small class="text-muted">JPG, JPEG, PNG, WEBP (máx. 5MB)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vista previa</label>
                                <div class="border rounded p-2 text-center" style="min-height: 100px;">
                                    <img id="imgPreviewModal" src="" alt="Vista previa" style="max-height: 80px; display: none;">
                                    <p id="placeholderModal" class="text-muted mb-0 mt-2">Sin imagen</p>
                                </div>
                            </div>
                        </div>
                        <div id="productoError" class="alert alert-danger mt-3 d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-lg">Guardar producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsTable = document.getElementById("itemsTable").querySelector("tbody");
            const addItemBtn = document.getElementById("addItemBtn");
            const totalField = document.getElementById("total");
            const deudaField = document.getElementById("deuda");
            const efectivoField = document.getElementById("efectivo");
            const transferenciaField = document.getElementById("transferencia");
            let itemIndex = 0;

            function updateTotal() {
                const subtotal = Array.from(document.querySelectorAll(".subtotal"))
                    .reduce((sum, input) => sum + parseFloat(input.value || 0), 0);
                totalField.value = subtotal.toFixed(2);
                updateDeuda();
            }

            function updateDeuda() {
                const total = parseFloat(totalField.value) || 0;
                const efectivo = parseFloat(efectivoField.value) || 0;
                const transferencia = parseFloat(transferenciaField.value) || 0;
                const pagado = efectivo + transferencia;
                const deuda = total - pagado;
                deudaField.value = deuda > 0 ? deuda.toFixed(2) : '';
            }

            deudaField.addEventListener("input", updateDeuda);
            efectivoField.addEventListener("input", updateDeuda);
            transferenciaField.addEventListener("input", updateDeuda);

            async function cargarProductos() {
                const response = await fetch("<?= e(base_path('panel/obtener_productos')) ?>");
                return await response.json();
            }

            addItemBtn.addEventListener("click", async () => {
                const row = document.createElement("tr");
                const productos = await cargarProductos();

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
                precioInput.step = "0.01";
                precioInput.readOnly = true;

                const subtotalInput = document.createElement("input");
                subtotalInput.type = "number";
                subtotalInput.className = "form-control subtotal";
                subtotalInput.name = `subtotal_${itemIndex}`;
                subtotalInput.step = "0.01";
                subtotalInput.readOnly = true;

                select.addEventListener("change", () => {
                    const precioSeleccionado = select.selectedOptions[0].dataset.precio;
                    const precio = precioSeleccionado !== undefined ? parseFloat(precioSeleccionado) : 0;
                    precioInput.value = precioSeleccionado !== undefined ? precio.toFixed(2) : '';
                    subtotalInput.value = precioSeleccionado !== undefined ? (precio * cantidadInput.value).toFixed(2) : '';
                    updateTotal();
                });

                cantidadInput.addEventListener("input", () => {
                    const precio = parseFloat(precioInput.value || 0);
                    subtotalInput.value = (precio * cantidadInput.value).toFixed(2);
                    updateTotal();
                });

                const btnQuitar = document.createElement("button");
                btnQuitar.type = "button";
                btnQuitar.className = "btn btn-sm btn-danger";
                btnQuitar.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                btnQuitar.addEventListener("click", () => {
                    row.remove();
                    updateTotal();
                });

                const tdProducto = document.createElement("td");
                tdProducto.appendChild(select);
                const tdCantidad = document.createElement("td");
                tdCantidad.appendChild(cantidadInput);
                const tdPrecio = document.createElement("td");
                tdPrecio.className = "text-end";
                tdPrecio.appendChild(precioInput);
                const tdSubtotal = document.createElement("td");
                tdSubtotal.className = "text-end";
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
            });

            document.getElementById('fotoInputModal').addEventListener('change', function(e) {
                const archivo = e.target.files[0];
                const img = document.getElementById('imgPreviewModal');
                const placeholder = document.getElementById('placeholderModal');
                if (archivo) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        img.src = ev.target.result;
                        img.style.display = 'block';
                        placeholder.style.display = 'none';
                    };
                    reader.readAsDataURL(archivo);
                }
            });

            document.getElementById('formProductoModal').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const errorDiv = document.getElementById('productoError');
                errorDiv.classList.add('d-none');

                try {
                    const response = await fetch("<?= e(base_path('panel/productos/insertar')) ?>", {
                        method: 'POST',
                        body: formData
                    });

                    if (response.redirected || response.ok) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalProducto'));
                        modal.hide();
                        this.reset();
                        document.getElementById('imgPreviewModal').style.display = 'none';
                        document.getElementById('placeholderModal').style.display = 'block';
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
