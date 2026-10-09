<?php
// Cada página prepara $seo antes de incluir este bloque en <head>.
if (!isset($seo)) {
    http_response_code(404);
    return;
}
$descripcionSocial = $seo['descripcion_social'] ?? $seo['descripcion'];
$imagenSocial = $seo['imagen'] ?? null;
$facebookAppId = trim(getenv('FACEBOOK_APP_ID') ?: '');
?>
<title><?= e($seo['titulo']) ?></title>
<meta name="description" content="<?= e($seo['descripcion']) ?>">
<?php if (!empty($seo['noindex'])): ?>
    <meta name="robots" content="noindex, follow">
<?php else: ?>
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="<?= e($seo['url']) ?>">
    <meta property="og:locale" content="es_AR">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Frani">
    <meta property="og:title" content="<?= e($seo['titulo']) ?>">
    <meta property="og:description" content="<?= e($descripcionSocial) ?>">
    <meta property="og:url" content="<?= e($seo['url']) ?>">
    <?php if (preg_match('/\A[1-9][0-9]*\z/', $facebookAppId) === 1): ?>
        <meta property="fb:app_id" content="<?= e($facebookAppId) ?>">
    <?php endif; ?>
    <?php if ($imagenSocial): ?>
        <meta property="og:image" content="<?= e($imagenSocial['url']) ?>">
        <meta property="og:image:secure_url" content="<?= e($imagenSocial['url']) ?>">
        <meta property="og:image:type" content="<?= e($imagenSocial['tipo']) ?>">
        <meta property="og:image:width" content="<?= e((string) $imagenSocial['ancho']) ?>">
        <meta property="og:image:height" content="<?= e((string) $imagenSocial['alto']) ?>">
        <meta property="og:image:alt" content="<?= e($imagenSocial['alt']) ?>">
    <?php endif; ?>
    <?php if (isset($seo['precio'])): ?>
        <meta property="product:price:amount" content="<?= e($seo['precio']) ?>">
        <meta property="product:price:currency" content="ARS">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e(seo_texto($seo['titulo'], 70)) ?>">
    <meta name="twitter:description" content="<?= e($descripcionSocial) ?>">
    <?php if ($imagenSocial): ?>
        <meta name="twitter:image" content="<?= e($imagenSocial['url']) ?>">
        <meta name="twitter:image:alt" content="<?= e($imagenSocial['alt']) ?>">
    <?php endif; ?>
    <?php if (!empty($seo['datos'])): ?>
        <script type="application/ld+json"><?= seo_json($seo['datos']) ?></script>
    <?php endif; ?>
<?php endif; ?>
