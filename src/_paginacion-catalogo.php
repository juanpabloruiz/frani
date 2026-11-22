<?php
$totalPaginasCatalogo = max(1, (int) ceil((int) $loteCatalogo['total'] / max(1, (int) $porPaginaCatalogo)));
$paginaActualCatalogo = min(max(1, (int) $paginaCatalogo), $totalPaginasCatalogo);
?>
<nav class="catalogo-paginacion mt-4" id="paginacionCatalogo" aria-label="Paginación de productos"
    data-total="<?= e((string) $loteCatalogo['total']) ?>"
    data-page="<?= e((string) $paginaActualCatalogo) ?>"
    data-page-size="<?= e((string) $porPaginaCatalogo) ?>"
    <?= $totalPaginasCatalogo <= 1 ? 'hidden' : '' ?>>
    <?= catalogo_paginacion_html($paginaActualCatalogo, (int) $loteCatalogo['total'], (int) $porPaginaCatalogo) ?>
    <p class="small text-body-secondary text-center mt-2 mb-0" data-pagination-status role="status" aria-live="polite" aria-atomic="true"></p>
</nav>
