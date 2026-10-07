<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();
$consulta = $db->query(
    "SELECT
        f.id,
        f.nombre,
        f.detalle,
        f.total,
        f.efectivo,
        f.transferencia,
        f.deuda,
        f.agregado,
        f.modificado
    FROM facturas f
    ORDER BY f.id DESC"
);

$totalesPorDia = [];
$consultaTotales = $db->query(
    "SELECT DATE(agregado) AS dia,
            SUM(COALESCE(efectivo, 0) + COALESCE(transferencia, 0)) AS total_dia
     FROM facturas
     GROUP BY DATE(agregado)"
);
while ($f = $consultaTotales->fetch_assoc()) {
    $totalesPorDia[$f['dia']] = (float) $f['total_dia'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head><?php $tituloPagina = 'Ventas'; require __DIR__ . '/../_head.php'; ?></head>
<body class="sb-app">
    <?php require __DIR__ . '/../menu.php'; ?>
    <div class="sb-contenido sb-workspace">
        <div class="sb-cabecera"><h1>Ventas</h1><div class="sb-cabecera-acciones"><a href="<?= e(base_path('panel/facturas/nueva')) ?>" class="btn btn-primary">Nueva venta</a></div></div>
        <div class="sb-buscador"><div class="input-group"><span class="input-group-text"><i class="fa-solid fa-search"></i></span><input type="search" id="buscadorFacturas" class="form-control" placeholder="Buscar..." aria-label="Buscar ventas"></div></div>
        <div class="sb-lista" id="listaFacturas" data-search-input="buscadorFacturas">
<?php $diaActual = ''; $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']; ?>
        <?php while ($campo = $consulta->fetch_assoc()): ?>
            <?php
            $diaVenta = date('Y-m-d', strtotime($campo['agregado']));
            $tieneDeuda = (float) ($campo['deuda'] ?? 0) > 0;
            ?>
            <?php if ($diaVenta !== $diaActual): ?>
                <?php if ($diaActual !== ''): ?></div></section><?php endif; ?>
                <?php $diaActual = $diaVenta; ?>
                <section class="mb-4" data-card-group>
                    <div class="card sb-resumen-dia mb-3">
                        <div class="card-body py-2">
                            <h2 class="h6 mb-0">
                                <?= e($dias[(int) date('w', strtotime($campo['agregado']))]) ?>
                                <?= e(date('d-m', strtotime($campo['agregado']))) ?> |
                                <strong>$ <?= e(moneda($totalesPorDia[$diaVenta] ?? 0)) ?></strong>
                            </h2>
                        </div>
                    </div>
                    <div class="row g-3">
            <?php endif; ?>
<div class="col-12 col-lg-6 col-xxl-4" data-card-item>
    <div class="card sb-card h-100<?= $tieneDeuda ? ' sb-deuda-pendiente' : '' ?>" id="venta-<?= (int) $campo['id'] ?>" tabindex="0" role="link" aria-label="Editar <?= e($campo['nombre']) ?><?= $tieneDeuda ? ' — deuda pendiente de $ ' . e(moneda($campo['deuda'])) : '' ?>" data-edit="<?= e(base_path('panel/facturas/editar?id=' . $campo['id'])) ?>">
        <div class="card-body d-flex gap-3">
            <div class="sb-thumb sb-thumb-vacio"><i class="fa-solid fa-receipt"></i></div>
            <div class="flex-grow-1 sb-min0">
                <h2 class="sb-titulo"><?= e($campo['nombre']) ?></h2>
                <?php if ($tieneDeuda): ?>
                    <span class="badge text-wrap text-danger-emphasis bg-danger-subtle border border-danger-subtle mb-2">
                        <i class="fa-solid fa-circle-exclamation me-1" aria-hidden="true"></i>Deuda pendiente
                    </span>
                <?php endif; ?>
                <p class="small text-body-secondary mb-2"><?= e($campo['detalle']) ?></p><div class="sb-datos">
<?php foreach (['efectivo' => 'Efectivo', 'transferencia' => 'Transferencia', 'total' => 'Total', 'deuda' => 'Deuda'] as $clave => $etiqueta): ?>
<?php if (mostrar_monto($campo[$clave]) !== ''): ?><span><?= e($etiqueta) ?> <strong class="<?= $clave === 'deuda' ? 'text-danger' : ($clave === 'total' ? 'text-success' : '') ?>">$ <?= e(mostrar_monto($campo[$clave])) ?></strong></span><?php endif; ?>
<?php endforeach; ?></div>
                <div class="sb-meta mt-2">
                    <span><i class="fa-regular fa-clock me-1"></i><?= e(date('d-m | H:i', strtotime($campo['agregado']))) ?></span>
                    <?php if ($campo['modificado']): ?><span><i class="fa-solid fa-pen me-1"></i><?= e(date('d-m | H:i', strtotime($campo['modificado']))) ?></span><?php endif; ?>
                </div>
            </div>
            <div class="sb-accion">
                <form method="POST" action="<?= e(base_path('panel/facturas/eliminar')) ?>" onsubmit="return confirm('¿Eliminar esta venta?');">
                    <?= CSRF_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $campo['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" aria-label="Eliminar <?= e($campo['nombre']) ?>"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
</div><?php endwhile; ?><?php if ($diaActual !== ''): ?></div></section><?php endif; ?><?php if ($consulta->num_rows === 0): ?><div class="sb-vacio">No hay ventas cargadas.</div><?php endif; ?>
        </div>
    </div>
    </main>
    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
</body>
</html>
