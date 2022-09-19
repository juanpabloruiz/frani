<?php
include('conexion.php');
?>
<table class="table align-middle table-hover">
    <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Costo</th>
        <th>Borrar</th>
    </tr>
    <?php if ($_POST['busqueda'] == TRUE) {
        $busqueda = $_POST['busqueda'];
        $consulta = "SELECT * FROM productos where producto LIKE '%$busqueda%'";
        if ($resultado = $conexion->query($consulta)) {
            $encontrados = $resultado->num_rows;
            if ($encontrados >= 0) {
                while ($campo = $resultado->fetch_assoc()) {
                    $producto = $campo['producto'];
    ?>
    <tr <?php echo ($campo['estado'] == 'publico') ? 'class="bg-success text-white"' : 'class="bg-secondary text-white"'; ?>>
        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['producto']; ?></td>
        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['precio']; ?></td>
        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['costo']; ?></td>
        <td><a href="baja?id=<?php echo $campo['id']; ?>" onclick="return confirm('¿Desea borrar <?php echo $producto; ?>?')" class="btn btn-danger"><i class="bi bi-trash3"></i></a></td>
    </tr>
    <?php
                }
            }
        }
    } else {
        $consulta = mysqli_query($conexion, "SELECT * FROM productos ORDER BY producto ASC");
        while ($campo = mysqli_fetch_array($consulta)) {
            $producto = $campo['producto'];
    ?>
    <tr <?php echo ($campo['estado'] == 'publico') ? 'class="bg-success text-white"' : 'class="bg-secondary text-white"'; ?>>
        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['producto']; ?></td>
        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['precio']; ?></td>
        <td onclick="window.location='?id=<?php echo $campo['id']; ?>'"><?php echo $campo['costo']; ?></td>
        <td><a href="baja?id=<?php echo $campo['id']; ?>" onclick="return confirm('¿Desea borrar <?php echo $producto; ?>?')" class="btn btn-danger"><i class="bi bi-trash3"></i></a></td>
    </tr>
    <?php
        }
    }
    ?>
</table>