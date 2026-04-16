<?php
if (isset($_POST['insertar_categoria'])) {
    $nombre = trim($_POST['nombre'] ?? '');

    if ($nombre !== '') {
        $sentencia = $conexion->prepare("INSERT IGNORE INTO categorias (nombre) VALUES (?)");
        $sentencia->bind_param('s', $nombre);
        $sentencia->execute();
        $sentencia->close();
    }

    redireccionar('panel.php');
}

$consultaCategorias = $conexion->query("SELECT id, nombre FROM categorias ORDER BY nombre ASC");
?>

<div class="card shadow-sm h-100">
    <div class="card-body">
        <h2 class="h4">Agregar categoría</h2>

        <form method="POST" action="<?= e(base_path('panel.php')) ?>">
            <div class="mb-3">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre de la categoría" required>
            </div>
            <div class="mb-3 mb-0">
                <button type="submit" name="insertar_categoria" class="btn btn-primary">Guardar categoría</button>
            </div>
        </form>

        <hr>

        <ul class="list-group list-group-flush">
            <?php while ($fila = $consultaCategorias->fetch_assoc()): ?>
                <li class="list-group-item px-0"><?= e($fila['nombre']) ?></li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>
