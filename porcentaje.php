<!-- Porcentaje -->
<div>
    <h3>Generar interés sobre productos</h3>
    <form method="post">
        <?php
        if (isset($_POST['porcentual'])) {
            $categoria = $_POST['categoria'];
            $porcentaje = $_POST['porcentaje'];
            $porcentaje_entero = round($porcentaje);
            $consulta = "UPDATE productos SET precio = ROUND(precio * (1 + ?/100)) WHERE categoria = '$categoria'";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->bind_param("i", $porcentaje_entero);
            if ($sentencia->execute()) {
                echo '<script>window.location="./"</script>';
            } else {
                echo '<div class="alert alert-danger">Error en la actualización: ' . $sentencia->error . '</div>';
            }
        }
        ?>
        <div class="row">
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa-solid fa-layer-group"></i></span>
                <select name="categoria" class="form-select" aria-label="Seleccionar categoría">

                    <option disabled selected>Categoría</option>

                    <?php
                    $consulta = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC");
                    while ($fila = $consulta->fetch_assoc()):
                    ?>

                        <option value="<?= $fila['nombre'] ?>"><?= $fila['nombre'] ?></option>

                    <?php endwhile ?>

                </select>
            </div>
            <div class="col-md-auto">
                <div class="input-group mb-3">
                    <input type="number" name="porcentaje" class="form-control" aria-label="Amount (to the nearest dollar)">
                    <span class="input-group-text">%</span>
                    <input type="submit" name="porcentual" value="Aplicar aumento" class="btn btn-primary">
                </div>
            </div>
        </div>
    </form>
</div>