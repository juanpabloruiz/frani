<div class="catalogo-paginacion text-center mt-4" id="paginacionCatalogo"
    data-next="<?= e($loteCatalogo['siguiente'] === null ? '' : (string) $loteCatalogo['siguiente']) ?>"
    data-total="<?= e((string) $loteCatalogo['total']) ?>" <?= $loteCatalogo['siguiente'] === null ? 'hidden' : '' ?>>
    <p class="small text-secondary mb-2" data-load-status role="status" aria-live="polite" aria-atomic="true"></p>
    <a class="btn btn-outline-primary" data-load-more aria-controls="productosCatalogo"
        href="?pagina=<?= e((string) ($paginaCatalogo + 1)) ?>">Ver más productos</a>
</div>
