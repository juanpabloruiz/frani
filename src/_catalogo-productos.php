<?php while ($fila = $resultado->fetch_assoc()): ?>
    <div class="col">
        <a class="card<?= $categoriaCatalogo === null ? ' h-100' : '' ?> product-card-link" href="<?= e(producto_path($fila['uri'], $fila['categoria_uri'])) ?>">
            <?php if (!empty($fila['foto'])): ?>
                <picture>
                    <source srcset="<?= e(base_path('img/productos/' . $fila['foto'] . '.webp')) ?>" type="image/webp">
                    <img src="<?= e(base_path('img/productos/' . $fila['foto'] . '.jpg')) ?>" class="card-img-top" width="200" alt="<?= e($fila['producto']) ?>">
                </picture>
            <?php else: ?>
                <picture>
                    <source srcset="<?= e(base_path('img/Ejemplo.webp')) ?>" type="image/webp">
                    <img src="<?= e(base_path('img/Ejemplo..jpg')) ?>" class="card-img-top" width="200" alt="<?= e($fila['producto']) ?>">
                </picture>
            <?php endif; ?>
            <div class="card-body">
                <span class="badge text-bg-dark mb-2"><?= e($fila['categoria']) ?></span>
                <h2 class="h5 card-title"><?= e($fila['producto']) ?></h2>
                <p class="card-text h4 text-primary fw-bolder mb-2">$ <?= e(moneda($fila['precio'])) ?></p>
                <p class="card-text text-secondary mb-0">Stock disponible: <?= e((string) $fila['stock']) ?></p>
            </div>
        </a>
    </div>
<?php endwhile; ?>
<?php if ($resultado->num_rows === 0): ?>
    <div class="catalogo-vacio col-12">
        <div class="alert alert-info mb-0">
            <?php if ($busquedaCatalogo !== ''): ?>
                No hay resultados para esta búsqueda.
            <?php elseif ($categoriaCatalogo !== null): ?>
                No hay productos en esta categoría.
            <?php else: ?>
                No hay productos cargados todavía.
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
