<?php include 'conexion.php'; ?>

<?php include 'categorias.php' ?>

<?php
$consulta = $conexion->query("SELECT * FROM productos");
while ($fila = $consulta->fetch_assoc()):?>

<table>
    <thead>
        <tr>
            <th>CODIGO</th>
            <th>PRODUCTO</th>
            <th>COSTO</th>
            <th>PRECIO</th>
            <th>CANTIDAD</th>
            <th>CATEGORIA</th>
            <th>AGREGADO</th>
            <th>MODIFICADO</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $fila['codigo'] ?></td>
            <td><?= $fila['producto'] ?></td>
            <td><?= $fila['costo'] ?></td>
            <td><?= $fila['precio'] ?></td>
            <td><?= $fila['cantidad'] ?></td>
            <td><?= $fila['id_categoria'] ?></td>
            <td><?= $fila['agregado'] ?></td>
            <td><?= $fila['editado'] ?></td>
        </tr>
    </tbody>
</table>

<?php endwhile ?>