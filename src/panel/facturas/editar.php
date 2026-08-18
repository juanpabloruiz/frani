<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redireccionar('panel/facturas');
}

$db = conexion();

$stmt = $db->prepare("SELECT id, nombre, metodo, total, detalle FROM facturas WHERE id = ?");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Factura | Frani</title>
    <link rel="stylesheet" href="<?= e(base_path('../../css/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../fontawesome/css/all.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(base_path('../../css/estilo.css?v=3')) ?>">
</head>

<body>
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Editar factura</h1>
            <a href="<?= e(base_path('panel/facturas')) ?>" class="btn btn-outline-secondary">Volver</a>
        </div>

        <form id="facturaForm" method="POST" action="<?= e(base_path('panel/facturas/actualizar')) ?>">
            <?= CSRF_field() ?>
            <input type="hidden" name="id" value="<?= e((string) $factura['id']) ?>">

            <div class="mb-3">
                <label class="form-label">Nombre del cliente</label>
                <input type="text" id="nombre" name="nombre" class="form-control"
                    value="<?= e($factura['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Método de pago</label>
                <select id="metodo" name="metodo" class="form-select" required>
                    <option value="">Seleccionar método</option>
                    <?php
                    $metodos = ['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia'];
                    foreach ($metodos as $valor => $etiqueta): ?>
                        <option value="<?= e($valor) ?>" <?= $factura['metodo'] === $valor ? 'selected' : '' ?>>
                            <?= e($etiqueta) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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

            <button type="button" class="btn btn-success mb-3" id="addItemBtn">
                Agregar ítem
            </button>

            <div class="mb-3">
                <label class="form-label">Total</label>
                <input type="number" id="total" name="total" class="form-control" step="0.01" readonly>
            </div>

            <div class="d-grid d-md-block">
                <button type="submit" class="btn btn-primary">Actualizar factura</button>
            </div>
        </form>
        </div>
    </main>

    <script src="<?= e(base_path('../../js/bootstrap.bundle.min.js')) ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsTable = document.getElementById("itemsTable").querySelector("tbody");
            const addItemBtn = document.getElementById("addItemBtn");
            const totalField = document.getElementById("total");
            const productos = <?= $productosJSON ?>;
            let itemIndex = 0;

            function updateTotal() {
                const total = Array.from(document.querySelectorAll(".subtotal"))
                    .reduce((sum, input) => sum + parseFloat(input.value || 0), 0);
                totalField.value = total.toFixed(2);
            }

            function crearFila(selectId, cantidad, precio) {
                const index = itemIndex;
                const row = document.createElement("tr");

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
                precioInput.value = precio;

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
                    precioInput.value = select.selectedOptions[0].dataset.precio || 0;
                    recalcular();
                });

                cantidadInput.addEventListener("input", recalcular);

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

                if (precio > 0) recalcular();
            }

            <?php foreach ($items as $item): ?>
                crearFila(<?= $item['id'] !== null ? (int) $item['id'] : 'null' ?>, <?= (int) $item['cantidad'] ?>, <?= $item['precio'] ?>);
                itemIndex++;
            <?php endforeach; ?>

            addItemBtn.addEventListener("click", () => {
                crearFila(null, 1, 0);
                itemIndex++;
            });
        });
    </script>

</body>

</html>
