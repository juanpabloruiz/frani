<table class="table table-hover align-middle">
    <tr class="table-dark">
        <th class="text-center">CÓDIGO</th>
        <th class="text-center">PRODUCTO</th>
        <th class="text-center">COSTO</th>
        <th class="text-center">PRECIO</th>
        <th class="text-center">CATEGORÍA</th>
        <th class="text-center">AGREGADO</th>
        <th class="text-center">BORRAR</th>
    </tr>

    <?php
    $consulta = $conexion->query("SELECT * FROM productos p LEFT JOIN categorias c ON c.id = p.id_categoria");
    while ($fila = $consulta->fetch_assoc()):
    ?>

        <tr onclick="window.location='?editar=<?= $fila['id'] ?>';" style="cursor:pointer;">
            <td class="text-center"><?= $fila['codigo'] ?></td>
            <td class="text-left"><?= $fila['producto'] ?></td>
            <td class="text-end">$ <?= $fila['costo'] ?></td>
            <td class="text-end">$ <?= $fila['precio'] ?></td>
            <td class="text-center"><?= $fila['nombre'] ?? '' ?></td>
            <td class="text-center">
                <?= date('d/m/y', strtotime($fila['agregado'])) ?>
                <i class="fa-regular fa-alarm-clock mx-2 text-primary"></i>
                <?= date('H:i', strtotime($fila['agregado'])) ?>
            </td>
            <td class="text-center"><a href="?eliminar=<?= $fila['id'] ?>" class="btn btn-dark" title="Eliminar"><i class="fa-solid fa-trash"></i></a></td>
        </tr>

    <?php endwhile; ?>

</table>