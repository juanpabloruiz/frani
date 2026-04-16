<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/funciones.php';

$busqueda = trim($_POST['busqueda'] ?? '');

if ($busqueda === '') {
    $sentencia = $conexion->prepare(
        "SELECT nombre, detalle, metodo, total, fecha
        FROM facturas
        ORDER BY fecha DESC"
    );
} else {
    $like = '%' . $busqueda . '%';
    $sentencia = $conexion->prepare(
        "SELECT nombre, detalle, metodo, total, fecha
        FROM facturas
        WHERE nombre LIKE ? OR detalle LIKE ? OR metodo LIKE ?
        ORDER BY fecha DESC"
    );
    $sentencia->bind_param('sss', $like, $like, $like);
}

$sentencia->execute();
$resultado = $sentencia->get_result();
?>
<table class="table align-middle table-hover">
    <tr>
        <th class="text-center">NOMBRE</th>
        <th class="text-center">DETALLE</th>
        <th class="text-center">METODO</th>
        <th class="text-center">TOTAL</th>
        <th class="text-center">FECHA</th>
    </tr>
    <?php while ($campo = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= e($campo['nombre']) ?></td>
            <td><?= e($campo['detalle']) ?></td>
            <td class="text-center"><?= e($campo['metodo']) ?></td>
            <td class="text-center">$ <?= e(number_format((float) $campo['total'], 2, ',', '.')) ?></td>
            <td class="text-center"><?= e(date('d/m/Y H:i', strtotime($campo['fecha']))) ?></td>
        </tr>
    <?php endwhile; ?>

    <?php if ($resultado->num_rows === 0): ?>
        <tr>
            <td colspan="5" class="text-center text-secondary py-4">No se encontraron resultados.</td>
        </tr>
    <?php endif; ?>
</table>
<?php $sentencia->close(); ?>
