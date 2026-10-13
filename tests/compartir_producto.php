<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/compartir_producto.php
// Fixtures de identidad aislados: no inicia sesión HTTP ni consulta la base real.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../src/seo.php';

function verificar_compartir(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

/** Sustituye la resolución por ID de la identidad, sin confiar en correo de sesión. */
function usuario_actual(): ?array
{
    $id = $_SESSION['usuario_id'] ?? null;
    if ($id === null) {
        return null;
    }
    return $GLOBALS['usuarios_compartir_fixture'][(int) $id] ?? null;
}

function renderizar_compartir(array $sesion): string
{
    $_SESSION = $sesion;
    $producto = [
        'id' => 510,
        'producto' => 'Vaso "Stitch" & sorbete',
        'uri' => 'vaso-stitch-celeste-con-sorbete',
        'categoria' => 'General',
        'categoria_uri' => 'general',
        'descripcion' => 'Vaso celeste con sorbete.',
        'foto' => null,
        'precio' => '13000.00',
        'stock' => null,
    ];
    $seo = ['url' => sitio_url('general/vaso-stitch-celeste-con-sorbete')];
    ob_start();
    try {
        include __DIR__ . '/../src/_compartir-producto.php';
        return (string) ob_get_contents();
    } finally {
        ob_end_clean();
    }
}

function verificar_enlaces_compartir(string $html, string $caso): void
{
    $url = sitio_url('general/vaso-stitch-celeste-con-sorbete');
    $titulo = 'Vaso "Stitch" & sorbete | Frani';
    verificar_compartir(str_contains($html, 'data-product-share data-url="' . e($url) . '"'),
        $caso . ': conserva la URL canónica del producto');
    verificar_compartir(str_contains($html, 'data-title="' . e($titulo) . '"'),
        $caso . ': conserva el título del enlace');

    $redes = [
        'facebook' => ['host' => 'www.facebook.com', 'url' => ['u' => $url]],
        'x' => ['host' => 'x.com', 'url' => ['url' => $url, 'text' => $titulo]],
        'whatsapp' => ['host' => 'wa.me', 'url' => ['text' => $titulo . "\n" . $url]],
        'telegram' => ['host' => 't.me', 'url' => ['url' => $url, 'text' => $titulo]],
    ];
    foreach ($redes as $nombre => $esperado) {
        verificar_compartir(preg_match('/<a\b[^>]*class="[^"]*product-share-' . $nombre
            . '(?:\s|"|$)[^>]*href="([^"]+)"/s', $html, $enlace) === 1,
            $caso . ': conserva compartir enlace en ' . $nombre);
        $href = html_entity_decode($enlace[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        verificar_compartir(parse_url($href, PHP_URL_HOST) === $esperado['host'],
            $caso . ': destino correcto de ' . $nombre);
        parse_str((string) parse_url($href, PHP_URL_QUERY), $parametros);
        verificar_compartir($parametros === $esperado['url'],
            $caso . ': ' . $nombre . ' conserva título y URL sin convertirlo en estado');
    }
    foreach (['data-copy-link', 'data-native-share', 'data-share-fallback'] as $control) {
        verificar_compartir(substr_count($html, $control) === 1,
            $caso . ': mantiene el control ' . $control);
    }
    verificar_compartir(str_contains($html, 'value="' . e($url) . '" readonly'),
        $caso . ': mantiene alternativa de copiar el enlace');
}

$sesionAnterior = $_SESSION ?? [];
$usuariosAnteriores = $GLOBALS['usuarios_compartir_fixture'] ?? null;
$GLOBALS['usuarios_compartir_fixture'] = [
    1 => ['id' => 1, 'correo' => 'pabloruiz1980@gmail.com'],
    2 => ['id' => 2, 'correo' => 'otra-cuenta@example.test'],
    3 => ['id' => 3, 'correo' => '  PABLOruiz1980@GMAIL.COM  '],
    4 => ['id' => 4, 'correo' => null],
];

try {
    $publicos = [
        'visitante' => [],
        'otra cuenta' => ['usuario_id' => 2],
        'ID sin registro' => ['usuario_id' => 999999],
        'correo de sesión sin ID' => ['correo' => 'pabloruiz1980@gmail.com'],
        'otra cuenta con correo de sesión falso' => ['usuario_id' => 2, 'correo' => 'pabloruiz1980@gmail.com'],
        'cuenta sin correo' => ['usuario_id' => 4],
    ];
    foreach ($publicos as $caso => $sesion) {
        $html = renderizar_compartir($sesion);
        verificar_enlaces_compartir($html, $caso);
        foreach (['data-state-preview', 'data-state-download', 'data-state-dialog', 'data-state-share',
            'imagen-estado.php', 'Imagen para estado de WhatsApp'] as $elemento) {
            verificar_compartir(!str_contains($html, $elemento),
                $caso . ': no expone herramientas del estado (' . $elemento . ')');
        }
        fwrite(STDOUT, 'OK: ' . $caso . ' conserva enlaces y no muestra herramientas 9:16.' . PHP_EOL);
    }

    foreach (['correo exacto' => ['usuario_id' => 1], 'correo con mayúsculas y espacios' => ['usuario_id' => 3]] as $caso => $sesion) {
        $html = renderizar_compartir($sesion);
        verificar_enlaces_compartir($html, $caso);
        foreach (['data-state-preview', 'data-state-download', 'data-state-dialog', 'data-state-share'] as $elemento) {
            verificar_compartir(substr_count($html, $elemento) === 1,
                $caso . ': muestra una sola herramienta ' . $elemento);
        }
        verificar_compartir(preg_match('/<a\b[^>]*href="([^"]+)"[^>]*data-state-preview/s', $html, $vista) === 1,
            $caso . ': la vista previa tiene URL');
        $rutaVista = html_entity_decode($vista[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        verificar_compartir(parse_url($rutaVista, PHP_URL_PATH) === base_path('imagen-estado.php'),
            $caso . ': la vista previa usa la imagen vertical del mismo sitio');
        parse_str((string) parse_url($rutaVista, PHP_URL_QUERY), $parametrosVista);
        verificar_compartir(($parametrosVista['producto'] ?? '') === '510' && isset($parametrosVista['v'])
            && !isset($parametrosVista['descargar']), $caso . ': imagen del producto y versión actuales');
        verificar_compartir(str_contains($html, e($rutaVista . '&descargar=1')),
            $caso . ': conserva la descarga JPEG');
        verificar_compartir(str_contains($html, 'data-filename="frani-estado-510.jpg"')
            && str_contains($html, 'width="1080" height="1920"'),
            $caso . ': dialog prepara un JPEG vertical con nombre del producto');
        verificar_compartir(preg_match('/<button\b[^>]*data-state-share[^>]*\bhidden\b/', $html) === 1,
            $caso . ': compartir archivo comienza oculto hasta que el navegador lo habilita');
        fwrite(STDOUT, 'OK: ' . $caso . ' muestra vista previa, descarga y dialog 9:16.' . PHP_EOL);
    }
    fwrite(STDOUT, 'OK: identidad por ID, sin confianza en correo de sesión; las cuatro redes y copia siguen disponibles.' . PHP_EOL);
} finally {
    $_SESSION = $sesionAnterior;
    if ($usuariosAnteriores === null) {
        unset($GLOBALS['usuarios_compartir_fixture']);
    } else {
        $GLOBALS['usuarios_compartir_fixture'] = $usuariosAnteriores;
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_abort();
    }
}
