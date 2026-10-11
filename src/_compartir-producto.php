<?php
$urlCompartir = $seo['url'];
$tituloCompartir = seo_texto($producto['producto']) . ' | Frani';
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
    <div class="d-flex flex-wrap align-items-center gap-2" role="group" aria-label="Compartir este producto">
        <span class="product-share-label small text-body-secondary me-1">Compartir</span>
        <?php foreach ($redesCompartir as $redCompartir): ?>
            <a class="btn product-share-button product-share-<?= e($redCompartir['clase']) ?>"
                href="<?= e($redCompartir['url']) ?>" target="_blank" rel="noopener noreferrer"
                aria-label="Compartir en <?= e($redCompartir['nombre']) ?> (abre una nueva pestaña)" title="Compartir en <?= e($redCompartir['nombre']) ?>">
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
    <p class="small text-body-secondary mb-0 mt-2" data-share-status role="status" aria-live="polite" aria-atomic="true" hidden></p>
    <div class="mt-2" data-share-fallback hidden>
        <label class="visually-hidden" for="enlaceProducto">Enlace del producto para copiar</label>
        <input class="form-control form-control-sm" id="enlaceProducto" type="text" value="<?= e($urlCompartir) ?>" readonly>
    </div>
</div>
