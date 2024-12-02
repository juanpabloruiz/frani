<?php include('conexion.php'); ?>
<form method="POST" action="eliminar.php">
    <button type="submit" class="btn btn-danger mb-3" onclick="return confirm('¿Estás seguro de eliminar los productos seleccionados?')">Eliminar seleccionados</button>
    <table class="table align-middle table-hover">
        <tr>
            <th class="text-center"><input type="checkbox" id="selectAll"></th>
            <th class="text-center">PRODUCTO</th>
            <th class="text-center">STOCK</th>
            <th class="text-center">CATEGORIA</th>
            <th class="text-center">PRECIO</th>
            <th class="text-center">COSTO</th>
            <th class="text-center">FECHA</th>
            <th class="text-center">FUNCIONES</th>
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
                        <tr>
                            <td class="text-center"><input type="checkbox" name="ids[]" value="<?php echo $campo['id']; ?>"></td>
                            <td><?php echo $campo['producto']; ?></td>
                            <td class="text-center"><?php echo $campo['stock']; ?></td>
                            <td class="text-center"><?php echo $campo['categoria']; ?></td>
                            <td class="text-center"><?php echo '$ ' . $campo['precio']; ?></td>
                            <td class="text-center"><?php echo '$ ' . $campo['costo']; ?></td>
                            <?php
                            $fecha = $campo['fecha'];
                            $fecha_normal = date("d/m/Y", strtotime($fecha));
                            ?>
                            <td class="text-center"><?php echo $fecha_normal; ?></td>
                            <td class="text-center"><a href="?id=<?php echo $campo['id']; ?>">Editar</a></td>
                        </tr>
                <?php
                    }
                }
            }
        } else {
            $consulta = mysqli_query($conexion, "SELECT * FROM productos ORDER BY producto ASC");
            while ($campo = mysqli_fetch_array($consulta)) {
                ?>
                <tr>
                    <td class="text-center"><input type="checkbox" name="ids[]" value="<?php echo $campo['id']; ?>"></td>
                    <td><?php echo $campo['producto']; ?></td>
                    <td class="text-center"><?php echo $campo['stock']; ?></td>
                    <td class="text-center"><?php echo $campo['categoria']; ?></td>
                    <td class="text-center"><?php echo '$ ' . $campo['precio']; ?></td>
                    <td class="text-center"><?php echo '$ ' . $campo['costo']; ?></td>
                    <?php
                    $fecha = $campo['fecha'];
                    $fecha_normal = date("d/m/Y", strtotime($fecha));
                    ?>
                    <td class="text-center"><?php echo $fecha_normal; ?></td>
                    <td class="text-center"><a href="?id=<?php echo $campo['id']; ?>">Editar</a></td>
                </tr>
        <?php
            }
        }
        ?>
    </table>
</form>