<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$db = conexion();
$editando = false;
$categoria = ['id' => '', 'nombre' => ''];

$idEditar = (int) ($_GET['id'] ?? 0);
if ($idEditar > 0) {
    $stmt = $db->prepare("SELECT id, nombre FROM categorias WHERE id = ?");
    $stmt->bind_param('i', $idEditar);
    $stmt->execute();
    $categoriaEditar = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($categoriaEditar) {
        $categoria = $categoriaEditar;
        $editando = true;
    }
}

$consulta = $db->query("SELECT id, nombre, agregado, modificado FROM categorias ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php $tituloPagina = 'Categorías'; require __DIR__ . '/../_head.php'; ?>
</head>

<body class="sb-app">
    <?php require __DIR__ . '/../menu.php'; ?>

    <div class="sb-contenido sb-workspace">
        <div class="row g-3">
            <div class="col-md-4 col-xl-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" id="formCategoria"
                            action="<?= e(base_path('panel/categorias/' . ($editando ? 'actualizar' : 'insertar'))) ?>">
                            <?= CSRF_field() ?>
                            <?php if ($editando): ?>
                                <input type="hidden" name="id" value="<?= (int) $categoria['id'] ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="nombreCategoria" class="form-label">Nombre de la categoría</label>
                                <input type="text" name="nombre" id="nombreCategoria" class="form-control" required
                                    value="<?= e($categoria['nombre']) ?>">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <?= $editando ? 'Actualizar' : 'Guardar' ?>
                                </button>
                                <?php if ($editando): ?>
                                    <a class="btn btn-outline-secondary" href="<?= e(base_path('panel/categorias')) ?>">Cancelar</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-xl-9">
                <div class="sb-buscador">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                        <input type="search" id="buscadorCategorias" class="form-control"
                            placeholder="Buscar categoría..." aria-label="Buscar categorías">
                    </div>
                </div>

                <div class="row g-3 sb-lista" id="listaCategorias" data-search-input="buscadorCategorias">
                    <?php while ($fila = $consulta->fetch_assoc()): ?>
                        <?php $activo = $editando && (int) $fila['id'] === (int) $categoria['id']; ?>
                        <div class="col-12 col-lg-6 col-xxl-4" data-card-item>
                            <div class="card sb-card h-100<?= $activo ? ' activo' : '' ?>"
                                id="categoria-<?= (int) $fila['id'] ?>" tabindex="0" role="link"
                                aria-label="Editar <?= e($fila['nombre']) ?>"
                                data-edit="<?= e(base_path('panel/categorias?id=' . $fila['id'])) ?>">
                                <div class="card-body d-flex gap-3">
                                    <div class="sb-thumb sb-thumb-vacio"><i class="fa-solid fa-tags"></i></div>
                                    <div class="flex-grow-1 sb-min0">
                                        <h2 class="sb-titulo"><?= e($fila['nombre']) ?></h2>
                                        <div class="sb-meta mt-2">
                                            <span><i class="fa-regular fa-clock me-1"></i><?= e(date('d-m | H:i', strtotime($fila['agregado']))) ?></span>
                                            <?php if ($fila['modificado']): ?>
                                                <span><i class="fa-solid fa-pen me-1"></i><?= e(date('d-m | H:i', strtotime($fila['modificado']))) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="sb-accion">
                                        <form method="POST" action="<?= e(base_path('panel/categorias/eliminar')) ?>"
                                            onsubmit="return confirm('¿Eliminar esta categoría? Los productos asociados no se eliminarán.');">
                                            <?= CSRF_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $fila['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                                aria-label="Eliminar <?= e($fila['nombre']) ?>"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php if ($consulta->num_rows === 0): ?>
                        <div class="sb-vacio col-12">No hay categorías cargadas.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </main>

    <script src="<?= e(base_path('js/bootstrap.bundle.min.js')) ?>"></script>
</body>

</html>
