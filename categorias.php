<?php
if (isset($_POST['insertar'])) {
    $nombre = $_POST['nombre'];
    $sentencia = $conexion->prepare("INSERT INTO categorias (nombre) VALUES (?)");
    $sentencia->bind_param('s', $nombre);
    $sentencia->execute();
    $sentencia->close();
    header("Location: ./");
}
?>

<h3>Agregar Categoría</h3>

<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre">
    <input type="submit" name="insertar" value="Agregar">
</form>

<?php
$consulta = $conexion->query("SELECT * FROM categorias");
while ($fila = $consulta->fetch_assoc()):
?>
<ul>
    <li><?= $fila['nombre'] ?></li>
</ul>
<?php endwhile ?>