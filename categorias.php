<?php
if (isset($_POST['insertar_categoria'])) {
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
    <div class="mb-3">
        <input type="text" name="nombre" class="form-control" placeholder="Nombre">
    </div>
    <div class="mb-3">
        <input type="submit" name="insertar_categoria" value="Agregar" class="btn btn-primary">
    </div>
</form>

<?php
$consulta = $conexion->query("SELECT * FROM categorias");
while ($fila = $consulta->fetch_assoc()):
?>
    <ul>
        <li><?= $fila['nombre'] ?></li>
    </ul>
<?php endwhile ?>