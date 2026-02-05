<?php include 'conexion.php'; ?>

<?php
$consulta = $conexion->query("SELECT * FROM productos");
while ($fila = $consulta->fetch_assoc()):?>

<?= $fila['id'] ?>

<?php endwhile ?>