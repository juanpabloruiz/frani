<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();

$editando = false;
$cliente = [
    'id' => '', 'nombre' => '', 'telefono' => '', 'foto' => ''
];

$idEditar = (int) ($_GET['id'] ?? 0);
if ($idEditar > 0) {
    $stmt = $db->prepare(
        "SELECT id, nombre, foto, telefono
        FROM clientes WHERE id = ?"
    );
    $stmt->bind_param('i', $idEditar);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($cliente) {
        $editando = true;
    }
}

$consulta = $db->query(
    "SELECT id, nombre, telefono, foto, agregado, modificado
    FROM clientes
    ORDER BY nombre ASC"
);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php $tituloPagina = 'Clientes'; require __DIR__ . '/../_head.php'; ?>
</head>

<body class="sb-app">
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="sb-contenido sb-workspace">
        <div class="row g-3">

            <!-- Columna izquierda: Formulario -->
            <div class="col-md-4 col-xl-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="<?= e(base_path('panel/clientes/' . ($editando ? 'actualizar' : 'insertar') . ($editando ? '#cliente-' . $cliente['id'] : ''))) ?>" enctype="multipart/form-data">
                            <?= CSRF_field() ?>
                            <?php if ($editando): ?>
                                <input type="hidden" name="id" value="<?= e((string) $cliente['id']) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required
                                    value="<?= e($cliente['nombre']) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control"
                                    value="<?= e($cliente['telefono'] ?? '') ?>" placeholder="Teléfono (opcional)">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Foto del cliente</label>
                                <input type="file" name="foto" id="fotoInput" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                <?php if ($editando && !empty($cliente['foto'])): ?>
                                    <small class="text-muted">Dejar vacío para mantener la foto actual.</small>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editando ? 'Actualizar cliente' : 'Guardar cliente' ?>
                                </button>
                            </div>
                            <?php if ($editando): ?><a class="btn btn-outline-secondary w-100 mt-2" href="<?= e(base_path('panel/clientes')) ?>">Cancelar</a><?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: listado en cards -->
            <div class="col-md-8 col-xl-9">
                <div class="sb-buscador">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="text" id="buscadorClientes" class="form-control" placeholder="Buscar cliente..." aria-label="Buscar clientes">
                    </div>
                </div>

                <div class="row g-3 sb-lista" id="listaClientes" data-search-input="buscadorClientes">
                    <?php while ($fila = $consulta->fetch_assoc()): ?>
                    <div class="col-12 col-lg-6 col-xxl-4" data-card-item>
                        <div class="card sb-card h-100<?= $editando && (int) $fila['id'] === (int) $cliente['id'] ? ' activo' : '' ?>" id="cliente-<?= (int) $fila['id'] ?>" tabindex="0" role="link" aria-label="Editar <?= e($fila['nombre']) ?>" data-edit="<?= e(base_path('panel/clientes?id=' . $fila['id'])) ?>">
                            <div class="card-body d-flex gap-3">
                                <?php if (!empty($fila['foto'])): ?><img class="sb-thumb" src="<?= e(base_path('img/clientes/' . $fila['foto'] . '.jpg')) ?>" alt="<?= e($fila['nombre']) ?>"><?php else: ?><div class="sb-thumb sb-thumb-vacio"><i class="fa-solid fa-user"></i></div><?php endif; ?>
                                <div class="flex-grow-1 sb-min0">
                                    <h2 class="sb-titulo"><?= e($fila['nombre']) ?></h2>
                                    <div class="sb-datos"><span>Teléfono: <strong><?= e($fila['telefono'] ?: 'Sin teléfono') ?></strong></span></div>
                                    <div class="sb-meta mt-2">
                                        <span><i class="fa-regular fa-clock me-1"></i><?= e(date('d-m | H:i', strtotime($fila['agregado']))) ?></span>
                                        <?php if ($fila['modificado']): ?><span><i class="fa-solid fa-pen me-1"></i><?= e(date('d-m | H:i', strtotime($fila['modificado']))) ?></span><?php endif; ?>
                                    </div>
                                </div>
                                <div class="sb-accion">
                                    <form method="POST" action="<?= e(base_path('panel/clientes/eliminar')) ?>" onsubmit="return confirm('¿Eliminar este cliente?');">
                                        <?= CSRF_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $fila['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar" aria-label="Eliminar <?= e($fila['nombre']) ?>"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php if ($consulta->num_rows === 0): ?><div class="sb-vacio col-12">No hay clientes cargados.</div><?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    </main>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>

</body>

</html>
