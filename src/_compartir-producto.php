<?php
$urlCompartir = $seo['url'];
$tituloCompartir = seo_texto($producto['producto']) . ' | Frani';
$usuarioCompartir = usuario_actual();
$puedeCrearEstado = $usuarioCompartir !== null
    && strcasecmp(trim((string) ($usuarioCompartir['correo'] ?? '')), 'pabloruiz1980@gmail.com') === 0;
if ($puedeCrearEstado) {
    $imagenEstado = seo_imagen_estado($producto);
    $rutaImagenEstado = base_path('imagen-estado.php') . '?' . (string) parse_url($imagenEstado['url'], PHP_URL_QUERY);
    $rutaDescargaEstado = $rutaImagenEstado . '&descargar=1';
}
$redesCompartir = [
    ['nombre' => 'Facebook', 'icono' => 'fa-facebook-f', 'clase' => 'facebook',
        'url' => 'https://www.facebook.com/sharer/sharer.php?' . http_build_query(['u' => $urlCompartir], '', '&', PHP_QUERY_RFC3986)],
    ['nombre' => 'X', 'icono' => 'fa-x-twitter', 'clase' => 'x',
        'url' => 'https://x.com/intent/tweet?' . http_build_query(['url' => $urlCompartir, 'text' => $tituloCompartir], '', '&', PHP_QUERY_RFC3986)],
    ['nombre' => 'WhatsApp', 'icono' => 'fa-whatsapp', 'clase' => 'whatsapp',
        'url' => 'https://wa.me/?' . http_build_query(['text' => $tituloCompartir . "\n" . $urlCompartir], '', '&', PHP_QUERY_RFC3986)],
    ['nombre' => 'Telegram', 'icono' => 'fa-telegram', 'clase' => 'telegram',
        'url' => 'https://t.me/share/url?' . http_build_query(['url' => $urlCompartir, 'text' => $tituloCompartir], '', '&', PHP_QUERY_RFC3986)],
];
?>
<div class="product-share" data-product-share data-url="<?= e($urlCompartir) ?>" data-title="<?= e($tituloCompartir) ?>">
    <?php if ($puedeCrearEstado): ?>
    <div class="product-state-actions mb-3">
        <a class="btn btn-success d-inline-flex align-items-center gap-2" href="<?= e($rutaImagenEstado) ?>"
            target="_blank" rel="noopener" data-state-preview>
            <i class="fa-solid fa-image" aria-hidden="true"></i>
            Imagen para estado de WhatsApp
        </a>
        <p class="small text-body-secondary mb-0 mt-2">Compartí la imagen vertical en Mi estado.</p>
    </div>
    <?php endif; ?>
    <div class="d-flex flex-wrap align-items-center gap-2" role="group" aria-label="Compartir enlace del producto">
        <span class="product-share-label small text-body-secondary me-1">Compartir enlace</span>
        <?php foreach ($redesCompartir as $redCompartir): ?>
            <a class="btn product-share-button product-share-<?= e($redCompartir['clase']) ?>"
                href="<?= e($redCompartir['url']) ?>" target="_blank" rel="noopener noreferrer"
                aria-label="Compartir enlace en <?= e($redCompartir['nombre']) ?> (abre una nueva pestaña)" title="Compartir enlace en <?= e($redCompartir['nombre']) ?>">
                <i class="fa-brands <?= e($redCompartir['icono']) ?>" aria-hidden="true"></i>
            </a>
        <?php endforeach; ?>
        <button class="btn product-share-button" type="button" data-copy-link
            aria-label="Copiar enlace del producto" title="Copiar enlace" hidden>
            <i class="fa-solid fa-link" aria-hidden="true"></i>
        </button>
        <button class="btn product-share-button" type="button" data-native-share
            aria-label="Más opciones para compartir" title="Más opciones para compartir" hidden>
            <i class="fa-solid fa-arrow-up-from-bracket" aria-hidden="true"></i>
        </button>
    </div>
    <?php if ($puedeCrearEstado): ?>
    <div class="mt-3">
        <a class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2" data-state-download
            href="<?= e($rutaDescargaEstado) ?>" download="frani-estado-<?= e((string) $producto['id']) ?>.jpg">
            <i class="fa-solid fa-download" aria-hidden="true"></i>
            Descargar imagen para estado
        </a>
    </div>
    <?php endif; ?>
    <p class="small text-body-secondary mb-0 mt-2" data-share-status role="status" aria-live="polite" aria-atomic="true" hidden></p>
    <div class="mt-2" data-share-fallback hidden>
        <label class="visually-hidden" for="enlaceProducto">Enlace del producto para copiar</label>
        <input class="form-control form-control-sm" id="enlaceProducto" type="text" value="<?= e($urlCompartir) ?>" readonly>
    </div>
    <?php if ($puedeCrearEstado): ?>
    <dialog class="product-state-dialog" data-state-dialog aria-labelledby="tituloEstadoProducto"
        data-image="<?= e($rutaImagenEstado) ?>" data-filename="frani-estado-<?= e((string) $producto['id']) ?>.jpg">
        <div class="product-state-dialog-header">
            <h2 class="h5 mb-0" id="tituloEstadoProducto">Imagen para estado</h2>
            <form method="dialog">
                <button class="btn-close" aria-label="Cerrar vista previa"></button>
            </form>
        </div>
        <div class="product-state-dialog-preview">
            <img src="<?= e($rutaImagenEstado) ?>" alt="Estado con foto, nombre y precio de <?= e($producto['producto']) ?>"
                width="1080" height="1920" loading="lazy">
        </div>
        <div class="product-state-dialog-footer">
            <p class="mb-3" data-state-status role="status" aria-live="polite">Guardá la imagen en tu galería y elegila desde Mi estado de WhatsApp.</p>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success" type="button" data-state-share hidden>
                    <i class="fa-solid fa-arrow-up-from-bracket me-2" aria-hidden="true"></i>Compartir imagen
                </button>
                <a class="btn btn-outline-secondary" href="<?= e($rutaDescargaEstado) ?>"
                    download="frani-estado-<?= e((string) $producto['id']) ?>.jpg">Descargar imagen</a>
            </div>
        </div>
    </dialog>
    <?php endif; ?>
</div>
