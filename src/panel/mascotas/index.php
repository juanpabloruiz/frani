<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();

$editando = false;
$mascota = [
    'id' => '', 'tipo' => '', 'talle' => '', 'precio' => ''
];

$idEditar = (int) ($_GET['id'] ?? 0);
if ($idEditar > 0) {
    $stmt = $db->prepare(
        "SELECT id, tipo, talle, precio
        FROM mascotas WHERE id = ?"
    );
    $stmt->bind_param('i', $idEditar);
    $stmt->execute();
    $mascota = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($mascota) {
        $editando = true;
    }
}

$consulta = $db->query(
    "SELECT id, tipo, talle, precio, creado, modificado
    FROM mascotas
    ORDER BY tipo ASC"
);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php $tituloPagina = 'Mascotas'; require __DIR__ . '/../_head.php'; ?>
</head>

<body class="sb-app">
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="sb-contenido sb-workspace">
        <div class="row g-3">

            <!-- Columna izquierda: Formulario -->
            <div class="col-md-4 col-xl-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="<?= e(base_path('panel/mascotas/' . ($editando ? 'actualizar' : 'insertar') . ($editando ? '#mascota-' . $mascota['id'] : ''))) ?>">
                            <?= CSRF_field() ?>
                            <?php if ($editando): ?>
                                <input type="hidden" name="id" value="<?= e((string) $mascota['id']) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Tipo</label>
                                <input type="text" name="tipo" class="form-control" required
                                    value="<?= e($mascota['tipo']) ?>" placeholder="Ej. Perro, Gato, Loro...">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Talle</label>
                                    <input type="text" name="talle" class="form-control"
                                        value="<?= e($mascota['talle'] ?? '') ?>" placeholder="Ej. M, L, I, II...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" name="precio" class="form-control" required
                                            value="<?= e(numero_limpio($mascota['precio'])) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editando ? 'Actualizar mascota' : 'Guardar mascota' ?>
                                </button>
                            </div>
                            <?php if ($editando): ?><a class="btn btn-outline-secondary w-100 mt-2" href="<?= e(base_path('panel/mascotas')) ?>">Cancelar</a><?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-6">
                        <div class="card shadow-sm">
                            <div class="card-header text-center fw-bold">Talles por letra</div>
                            <div class="card-body"><div class="d-flex justify-content-between small text-body-secondary mb-1"><span>Talle</span><span>Largo</span></div><dl class="mb-0"><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">XXS</dt><dd class="mb-0">27</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">XS</dt><dd class="mb-0">32</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">S</dt><dd class="mb-0">35</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">M</dt><dd class="mb-0">41</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">L</dt><dd class="mb-0">43</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">XL</dt><dd class="mb-0">48</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">XXL</dt><dd class="mb-0">51</dd></div></dl></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card shadow-sm">
                            <div class="card-header text-center fw-bold">Talle largo</div>
                            <div class="card-body"><div class="d-flex justify-content-between small text-body-secondary mb-1"><span>Talle</span><span>Largo</span></div><dl class="mb-0"><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">0</dt><dd class="mb-0">26</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">1</dt><dd class="mb-0">30</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">2</dt><dd class="mb-0">34</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">3</dt><dd class="mb-0">37</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">4</dt><dd class="mb-0">43</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">5</dt><dd class="mb-0">47</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">6</dt><dd class="mb-0">50</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">7</dt><dd class="mb-0">53</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">8</dt><dd class="mb-0">56</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">9</dt><dd class="mb-0">62</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">10</dt><dd class="mb-0">69</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">11</dt><dd class="mb-0">77</dd></div><div class="d-flex justify-content-between border-bottom py-2"><dt class="text-danger">12</dt><dd class="mb-0">81</dd></div></dl></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: listado en cards -->
            <div class="col-md-8 col-xl-9">
                <div class="sb-buscador">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="buscadorMascotas" class="form-control" placeholder="Buscar mascota..." aria-label="Buscar mascotas">
                    </div>
                </div>

                <div class="row g-3 sb-lista" id="listaMascotas" data-search-input="buscadorMascotas">
                    <?php while ($fila = $consulta->fetch_assoc()): ?>
                    <div class="col-12 col-lg-6 col-xxl-4" data-card-item>
                        <div class="card sb-card h-100<?= $editando && (int) $fila['id'] === (int) $mascota['id'] ? ' activo' : '' ?>" id="mascota-<?= (int) $fila['id'] ?>" tabindex="0" role="link" aria-label="Editar <?= e($fila['tipo']) ?>" data-edit="<?= e(base_path('panel/mascotas?id=' . $fila['id'])) ?>">
                            <div class="card-body d-flex gap-3">
                                <div class="sb-thumb sb-thumb-vacio"><i class="fa-solid fa-paw"></i></div>
                                <div class="flex-grow-1 sb-min0">
                                    <h2 class="sb-titulo"><?= e($fila['tipo']) ?></h2>
                                    <div class="sb-meta"><span class="sb-chip">Talle: <?= e($fila['talle'] ?: 'Sin talle') ?></span></div><div class="sb-datos"><span>Precio <strong class="text-success">$ <?= e(moneda($fila['precio'])) ?></strong></span></div>
                                    <div class="sb-meta mt-2">
                                        <span><i class="fa-regular fa-clock me-1"></i><?= e(date('d-m | H:i', strtotime($fila['creado']))) ?></span>
                                        <?php if ($fila['modificado']): ?><span><i class="fa-solid fa-pen me-1"></i><?= e(date('d-m | H:i', strtotime($fila['modificado']))) ?></span><?php endif; ?>
                                    </div>
                                </div>
                                <div class="sb-accion">
                                    <form method="POST" action="<?= e(base_path('panel/mascotas/eliminar')) ?>" onsubmit="return confirm('¿Eliminar esta mascota?');">
                                        <?= CSRF_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $fila['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" aria-label="Eliminar <?= e($fila['tipo']) ?>"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php if ($consulta->num_rows === 0): ?><div class="sb-vacio col-12">No hay mascotas cargados.</div><?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    </main>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>

</body>

</html>