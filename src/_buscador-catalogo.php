<?php $nombreCategoriaCatalogo = $nombreCategoriaCatalogo ?? $categoria['nombre'] ?? ''; ?>
<div class="catalogo-buscador w-100 mb-4" role="search" aria-label="Buscar productos"
    data-catalog-search data-endpoint="<?= e(base_path('buscar-productos.php')) ?>"
    <?php if (isset($loteCatalogo)): ?>data-pagination="paginacionCatalogo"<?php endif; ?>
    <?php if (isset($detalleCatalogo)): ?>data-detail="<?= e($detalleCatalogo) ?>"<?php endif; ?>
    <?php if ($categoriaCatalogo !== null): ?>data-category="<?= e((string) $categoriaCatalogo) ?>"<?php endif; ?>>
    <div class="input-group">
        <span class="input-group-text"><i class="fa-solid fa-search" aria-hidden="true"></i></span>
        <input type="search" id="buscadorCatalogo" class="form-control" maxlength="200"
            placeholder="<?= e($categoriaCatalogo !== null ? 'Buscar en ' . $nombreCategoriaCatalogo . '...' : 'Buscar en todos los productos...') ?>"
            aria-label="<?= e($categoriaCatalogo !== null ? 'Buscar productos en ' . $nombreCategoriaCatalogo : 'Buscar en todos los productos') ?>"
            aria-controls="productosCatalogo" autocomplete="off">
        <button type="button" class="btn btn-outline-secondary" data-search-clear aria-label="Limpiar búsqueda" hidden>
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
    <p class="small text-secondary mt-2 mb-0" data-search-status role="status" aria-live="polite" aria-atomic="true"></p>
</div>
