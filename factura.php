<?php include('conexion.php'); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas | FRANI</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <nav class="navbar sticky-top navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="./">FRANI</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="./">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="factura">Factura</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ventas">Ventas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container my-3">
        <h1>Crear Factura</h1>
        <form id="facturaForm" method="POST" action="guardar_factura.php">
            <div class="mb-3">
                <input type="text" id="nombre" name="nombre" placeholder="Nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <select id="metodo" name="metodo" class="form-control" required>
                    <option value="">Método</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>

            <table class="table table-bordered" id="itemsTable">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Aquí las filas dinámicas tendrán campos con `name` y `id` -->
                </tbody>
            </table>
            <button type="button" class="btn btn-success mb-3" id="addItemBtn">Agregar Ítem</button>

            <div class="mb-3">
                <label for="total">Total:</label>
                <input type="number" id="total" name="total" class="form-control" readonly>
            </div>


            <input type="submit" value="Guardar Factura" class="btn btn-primary">
        </form>
    </main>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsTable = document.getElementById("itemsTable").querySelector("tbody");
            const addItemBtn = document.getElementById("addItemBtn");
            const totalField = document.getElementById("total");

            let itemIndex = 0; // Para generar nombres únicos

            addItemBtn.addEventListener("click", async () => {
                const row = document.createElement("tr");

                const response = await fetch("obtener_productos.php");
                const productos = await response.json();

                const select = document.createElement("select");
                select.className = "form-control select-producto";
                select.name = `producto_${itemIndex}`; // Nombre único
                select.innerHTML = '<option value="">Seleccione un producto</option>';
                productos.forEach(producto => {
                    select.innerHTML += `<option value="${producto.id}" data-precio="${producto.precio}">${producto.producto}</option>`;
                });

                const cantidadInput = document.createElement("input");
                cantidadInput.type = "number";
                cantidadInput.className = "form-control cantidad";
                cantidadInput.name = `cantidad_${itemIndex}`; // Nombre único
                cantidadInput.min = "1";
                cantidadInput.value = "1";

                const precioInput = document.createElement("input");
                precioInput.type = "number";
                precioInput.className = "form-control precio";
                precioInput.name = `precio_${itemIndex}`; // Nombre único
                precioInput.readOnly = true;

                const subtotalInput = document.createElement("input");
                subtotalInput.type = "number";
                subtotalInput.className = "form-control subtotal";
                subtotalInput.name = `subtotal_${itemIndex}`; // Nombre único
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

                row.appendChild(createCell(select));
                row.appendChild(createCell(cantidadInput));
                row.appendChild(createCell(precioInput));
                row.appendChild(createCell(subtotalInput));
                itemsTable.appendChild(row);

                function updateTotal() {
                    total = Array.from(document.querySelectorAll(".subtotal"))
                        .reduce((sum, input) => sum + parseFloat(input.value || 0), 0);
                    totalField.value = total.toFixed(2);
                }

                function createCell(content) {
                    const cell = document.createElement("td");
                    cell.appendChild(content);
                    return cell;
                }

                itemIndex++; // Incrementar para la próxima fila
            });

        });
    </script>
</body>

</html>