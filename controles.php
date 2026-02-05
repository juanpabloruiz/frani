<!-- Inserción y edición -->
<?php

if (isset($_POST['insertar'])) {
    $codigo = $_POST['codigo'];
    $producto = $_POST['producto'];
    $costo = $_POST['costo'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $id_categoria = $_POST['id_categoria'];
    $sentencia = $conexion->prepare("INSERT INTO productos (codigo, producto, costo, precio, cantidad, id_categoria) VALUES (?, ?, ?, ?, ?, ?)");
    $sentencia->bind_param('ssiiii', $codigo, $producto, $costo, $precio, $cantidad, $id_categoria);
    $sentencia->execute();
    $sentencia->close();
}

$id = $_GET['editar'] ?? null;
$editar = null;

if ($id) {
    $sentencia = $conexion->prepare("SELECT * FROM productos WHERE id = ?");
    $sentencia->bind_param("i", $id);
    $sentencia->execute();
    $resultado = $sentencia->get_result();
    $editar = $resultado->fetch_assoc();
}
?>

<?php if ($editar): ?>

    <!-- Formulario edición -->
    <form method="POST" action="update" class="d-flex flex-column flex-md-row gap-2 mb-3">
        <input type="hidden" name="id" value="<?= $editar['id'] ?>">
        <input type="text" name="codigo" class="form-control" value="<?= $editar['codigo'] ?>">
        <input type="text" name="producto" class="form-control" value="<?= $editar['producto'] ?>">
        <input type="text" name="costo" class="form-control" value="<?= $editar['costo'] ?>">
        <input type="text" name="precio" class="form-control" value="<?= $editar['precio'] ?>">
        <input type="text" name="cantidad" class="form-control" value="<?= $editar['cantidad'] ?>">
        <input type="submit" value="Actualizar" class="btn btn-primary">
    </form>

<?php else: ?>

    <!-- Formulario inserción -->
    <form method="POST" class="d-flex flex-column flex-md-row gap-2 mb-3">
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa-solid fa-barcode text-danger"></i></span>
            <input type="text" name="codigo" class="form-control" placeholder="Código de barras">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa-brands fa-product-hunt"></i></span>
            <input type="text" name="producto" class="form-control" placeholder="Producto">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text">$</span>
            <input type="text" name="costo" class="form-control" placeholder="Costo">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text">$</span>
            <input type="text" name="precio" class="form-control" placeholder="Precio">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text">/</span>
            <input type="number" name="cantidad" class="form-control" placeholder="Cantidad">
        </div>
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa-solid fa-layer-group"></i></span>
            <select name="id_categoria" class="form-select" aria-label="Seleccionar categoría">
                <option selected value="0">Categoría</option>
                <?php
                $consulta = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC");
                while ($fila = $consulta->fetch_assoc()):
                ?>
                    <option value="<?= $fila['id'] ?>"><?= $fila['nombre'] ?></option>
                <?php endwhile ?>
            </select>
        </div>
        <div class="mb-3">
            <input type="submit" name="insertar" value="Agregar producto" class="btn btn-primary w-100 w-md-auto">
        </div>
    </form>

<?php endif; ?>