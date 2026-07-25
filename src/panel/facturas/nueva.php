<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Factura | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container-fluid py-3 px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h3 mb-0">Nueva factura</h1>
            <a href="<?= e(base_path('panel/facturas')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form id="facturaForm" method="POST" action="<?= e(base_path('panel/facturas/insertar')) ?>">
                    <?= CSRF_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del cliente</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Método de pago</label>
                        <select id="metodo" name="metodo" class="form-select" required>
                            <option value="">Seleccionar método</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="table-dark">
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

                    <button type="button" class="btn btn-success mb-3" id="addItemBtn">
                        <i class="fa-solid fa-plus"></i> Agregar ítem
                    </button>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Total</label>
                        <input type="number" id="total" name="total" class="form-control" step="0.01" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">Guardar factura</button>
                </form>
            </div>
        </div>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsTable = document.getElementById("itemsTable").querySelector("tbody");
            const addItemBtn = document.getElementById("addItemBtn");
            const totalField = document.getElementById("total");
            let itemIndex = 0;

            addItemBtn.addEventListener("click", async () => {
                const row = document.createElement("tr");

                const response = await fetch("<?= e(base_path('panel/obtener_productos')) ?>");
                const productos = await response.json();

                const select = document.createElement("select");
                select.className = "form-select select-producto";
                select.name = `producto_${itemIndex}`;
                select.innerHTML = '<option value="">Seleccione un producto</option>';
                productos.forEach(producto => {
                    select.innerHTML += `<option value="${producto.id}" data-precio="${producto.precio}">${producto.producto}</option>`;
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
                    const precio = parseFloat(select.selectedOptions[0].dataset.precio || 0);
                    precioInput.value = precio.toFixed(2);
                    subtotalInput.value = (precio * cantidadInput.value).toFixed(2);
                    updateTotal();
                });

                cantidadInput.addEventListener("input", () => {
                    const precio = parseFloat(precioInput.value || 0);
                    subtotalInput.value = (precio * cantidadInput.value).toFixed(2);
                    updateTotal();
                });

                const btnQuitar = document.createElement("button");
                btnQuitar.type = "button";
                btnQuitar.className = "btn btn-sm btn-outline-danger";
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
                tdPrecio.appendChild(precioInput);
                const tdSubtotal = document.createElement("td");
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

                function updateTotal() {
                    const total = Array.from(document.querySelectorAll(".subtotal"))
                        .reduce((sum, input) => sum + parseFloat(input.value || 0), 0);
                    totalField.value = total.toFixed(2);
                }

                itemIndex++;
            });
        });
    </script>

</body>

</html>
