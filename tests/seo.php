<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/seo.php
// Solo lee el catálogo real; no modifica productos, precios ni stock.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../src/conexion.php';
require_once __DIR__ . '/../src/seo.php';

function verificar_seo(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function renderizar_seo(array $seo): string
{
    ob_start();
    include __DIR__ . '/../src/_seo.php';
    return (string) ob_get_clean();
}

function pedir_seo(string $ruta, string $agente = 'facebookexternalhit/1.1'): array
{
    $contexto = stream_context_create(['http' => [
        'ignore_errors' => true,
        'follow_location' => 0,
        'timeout' => 10,
        'header' => 'User-Agent: ' . $agente,
    ]]);
    $origen = rtrim(getenv('SEO_TEST_BASE_URL') ?: 'http://127.0.0.1', '/');
    $html = file_get_contents($origen . $ruta, false, $contexto);
    verificar_seo($html !== false, 'Debe responder la ruta ' . $ruta);
    $cabeceras = $http_response_header ?? [];
    preg_match('/\s(\d{3})\s/', $cabeceras[0] ?? '', $estado);
    return ['html' => $html, 'estado' => (int) ($estado[1] ?? 0), 'cabeceras' => implode("\n", $cabeceras)];
}

function metas_seo(string $html): array
{
    preg_match_all('/<meta (?:name|property)="([^"]+)" content="([^"]*)">/', $html, $coincidencias, PREG_SET_ORDER);
    $metas = [];
    foreach ($coincidencias as $coincidencia) {
        verificar_seo(!isset($metas[$coincidencia[1]]), 'No debe duplicarse ' . $coincidencia[1]);
        $metas[$coincidencia[1]] = html_entity_decode($coincidencia[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return $metas;
}

function datos_seo(string $html): array
{
    verificar_seo(preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $coincidencia) === 1,
        'Debe existir JSON-LD en el HTML inicial');
    return json_decode($coincidencia[1], true, 512, JSON_THROW_ON_ERROR);
}

try {
    $producto = [
        'id' => 123, 'producto' => 'Muñeca "Luz" & compañía', 'uri' => 'muneca-luz',
        'categoria' => 'Juguetes', 'categoria_uri' => 'juguetes',
        'descripcion' => "  Descripción con ñ.\nSegunda línea. ", 'foto' => null,
        'precio' => '1250.50', 'stock' => null,
    ];
    $seo = seo_producto($producto);
    $oferta = $seo['datos']['@graph'][0]['offers'];
    verificar_seo($oferta['price'] === '1250.50' && $oferta['priceCurrency'] === 'ARS', 'Precio exacto y moneda ARS');
    verificar_seo(!isset($oferta['availability']), 'Stock desconocido debe omitir disponibilidad');
    verificar_seo(!isset($seo['datos']['@graph'][0]['image']), 'Sin foto no debe usar un logo como imagen de Product');
    verificar_seo(str_starts_with($seo['descripcion_social'], 'Precio: $ 1.250,50 ARS.'), 'Precio visible en la descripción social');
    verificar_seo($seo['descripcion'] === 'Descripción con ñ. Segunda línea.', 'Descripción sin saltos de línea');
    verificar_seo($seo['url'] === sitio_url('juguetes/muneca-luz'), 'URL canónica limpia');
    verificar_seo($seo['datos']['@graph'][1]['itemListElement'][1]['item'] === sitio_url('juguetes'), 'Breadcrumb de categoría');
    parse_str((string) parse_url($seo['imagen']['url'], PHP_URL_QUERY), $parametrosImagen);
    verificar_seo(($parametrosImagen['producto'] ?? null) === '123' && !isset($parametrosImagen['foto']),
        'La tarjeta usa el ID del producto para obtener sus datos actuales');
    foreach (['precio' => '1499.00', 'producto' => 'Otro nombre', 'descripcion' => 'Otra descripción', 'categoria' => 'Otra categoría'] as $campo => $valor) {
        $cambiado = $producto;
        $cambiado[$campo] = $valor;
        verificar_seo(seo_producto($cambiado)['imagen']['url'] !== $seo['imagen']['url'], 'Renovar caché social al cambiar ' . $campo);
    }

    foreach ([4 => 'InStock', 0 => 'OutOfStock', -1 => 'OutOfStock'] as $stock => $disponibilidad) {
        $producto['stock'] = $stock;
        $actual = seo_producto($producto);
        verificar_seo($actual['datos']['@graph'][0]['offers']['availability'] === 'https://schema.org/' . $disponibilidad,
            'Disponibilidad exacta para stock ' . $stock);
    }
    $producto['descripcion'] = ' ';
    verificar_seo(str_contains(seo_producto($producto)['descripcion'], 'Consultá su precio'), 'Descripción vacía con alternativa');
    verificar_seo(seo_foto_producto('../logo') === null && seo_foto_producto("foto\n") === null,
        'La foto no admite traversal ni saltos de línea');
    $largo = seo_texto(str_repeat('ñ', 180), 160);
    verificar_seo(preg_match('/\A.{160}\z/u', $largo) === 1 && str_ends_with($largo, '…'), 'Límite de caracteres sin romper UTF-8');
    foreach ([70, 160, 200] as $limite) {
        foreach ([$limite - 1, $limite] as $cantidad) {
            $texto = str_repeat('ñ', $cantidad);
            verificar_seo(seo_texto($texto, $limite) === $texto, 'No truncar texto dentro del límite ' . $limite);
        }
        verificar_seo(seo_texto(str_repeat('ñ', $limite + 1), $limite) === str_repeat('ñ', $limite - 1) . '…',
            'Truncar solo cuando supera el límite ' . $limite);
    }

    $html = renderizar_seo($seo);
    $metas = metas_seo($html);
    verificar_seo($metas['og:title'] === $seo['titulo'], 'Título escapado y recuperable');
    verificar_seo($metas['twitter:card'] === 'summary_large_image', 'Tarjeta grande de X');
    verificar_seo($metas['og:image'] === $metas['twitter:image'], 'La misma foto para Facebook y X');
    verificar_seo($metas['product:price:amount'] === '1250.50', 'Precio legible por crawlers');
    verificar_seo(datos_seo($html) === $seo['datos'], 'JSON-LD legible después de renderizar');
    $appIdAnterior = getenv('FACEBOOK_APP_ID');
    try {
        // Un ID sintético sirve solo como fixture de prueba; no se publica en la aplicación.
        putenv('FACEBOOK_APP_ID=123456789012345');
        verificar_seo(metas_seo(renderizar_seo($seo))['fb:app_id'] === '123456789012345', 'Publicar el App ID configurado');
        foreach (['', '0', 'no-es-un-id', '123" onload="alert(1)'] as $idInvalido) {
            putenv('FACEBOOK_APP_ID=' . $idInvalido);
            verificar_seo(!isset(metas_seo(renderizar_seo($seo))['fb:app_id']), 'Omitir un App ID vacío o inválido');
        }
    } finally {
        $appIdAnterior === false ? putenv('FACEBOOK_APP_ID') : putenv('FACEBOOK_APP_ID=' . $appIdAnterior);
    }
    $peligroso = ['texto' => '</script><script>alert("x")</script> & ñ'];
    $json = seo_json($peligroso);
    verificar_seo(!str_contains($json, '<') && json_decode($json, true, 512, JSON_THROW_ON_ERROR) === $peligroso,
        'JSON-LD no debe permitir cerrar el script');
    $html404 = renderizar_seo(['titulo' => 'No encontrado', 'descripcion' => 'Página inexistente', 'noindex' => true]);
    verificar_seo(str_contains($html404, 'noindex, follow') && !str_contains($html404, 'og:')
        && !str_contains($html404, 'canonical') && !str_contains($html404, 'ld+json'), '404 sin canónica ni datos de producto');

    $db = conexion();
    $real = $db->query(
        'SELECT p.id, p.producto, p.uri, p.foto, p.descripcion, p.precio, p.stock,
            c.nombre AS categoria, c.uri AS categoria_uri
         FROM productos p INNER JOIN categorias c ON c.id = p.id_categoria
         WHERE p.uri IS NOT NULL AND c.uri IS NOT NULL ORDER BY p.id LIMIT 1'
    )->fetch_assoc();
    if ($real) {
        $ruta = producto_path($real['uri'], $real['categoria_uri']);
        $esperado = seo_producto($real);
        foreach (['facebookexternalhit/1.1', 'Twitterbot/1.0', 'Googlebot'] as $agente) {
            $respuesta = pedir_seo($ruta . '?utm_source=prueba', $agente);
            verificar_seo($respuesta['estado'] === 200, 'Producto público para ' . $agente);
            $metas = metas_seo($respuesta['html']);
            verificar_seo($metas['og:title'] === $esperado['titulo'] && $metas['og:description'] === $esperado['descripcion_social'],
                'Título y descripción reales en el HTML inicial');
            verificar_seo($metas['og:url'] === $esperado['url'] && str_contains($respuesta['html'], 'href="' . e($esperado['url']) . '"'),
                'Canónica sin tracking');
            verificar_seo($metas['product:price:amount'] === $esperado['precio'], 'Precio real del catálogo');
            verificar_seo(datos_seo($respuesta['html'])['@graph'][0]['offers'] === $esperado['datos']['@graph'][0]['offers'],
                'Oferta de Google coincide con la página');
            $imagenRuta = parse_url($metas['og:image'], PHP_URL_PATH) . '?' . parse_url($metas['og:image'], PHP_URL_QUERY);
            $imagen = pedir_seo($imagenRuta, $agente);
            $dimensiones = getimagesizefromstring($imagen['html']);
            verificar_seo($imagen['estado'] === 200 && $dimensiones[0] === 1200 && $dimensiones[1] === 630
                && $dimensiones[2] === IMAGETYPE_JPEG, 'Foto social pública para ' . $agente);
        }
        $antigua = pedir_seo('/productos/' . $real['uri']);
        verificar_seo($antigua['estado'] === 301 && stripos($antigua['cabeceras'], 'Location: ' . $ruta) !== false,
            'Redirección del enlace anterior');
        $categoria = pedir_seo(categoria_path($real['categoria_uri']));
        verificar_seo($categoria['estado'] === 200 && datos_seo($categoria['html'])['@graph'][0]['@type'] === 'CollectionPage',
            'Categoría con metadatos y datos estructurados');
    }
    $inicio = pedir_seo('/');
    verificar_seo($inicio['estado'] === 200 && str_contains($inicio['html'], '<h1 ')
        && datos_seo($inicio['html'])['@graph'][1]['@type'] === 'WebSite', 'Inicio con H1 y datos de sitio');
    foreach (['/categoria.php?uri=seo-prueba-inexistente', '/producto.php?uri=seo-prueba-inexistente'] as $ruta) {
        $respuesta = pedir_seo($ruta);
        verificar_seo($respuesta['estado'] === 404 && metas_seo($respuesta['html'])['robots'] === 'noindex, follow', '404 real y noindex');
    }
    $robots = pedir_seo('/robots.txt');
    verificar_seo($robots['estado'] === 200 && str_contains($robots['html'], 'Sitemap: ' . sitio_url('sitemap.xml'))
        && !str_contains($robots['html'], 'Disallow: /img'), 'Robots permite fotos y anuncia sitemap');
    $sitemap = pedir_seo('/sitemap.xml');
    verificar_seo($sitemap['estado'] === 200 && str_contains($sitemap['html'], '<loc>' . sitio_url() . '</loc>')
        && !str_contains($sitemap['html'], '/panel'), 'Sitemap público de páginas canónicas');
    fwrite(STDOUT, "OK: Google, Facebook y X; precio ARS, stock real, fotos, escaping, UTF-8, canónicas, redirecciones y 404.\n");
} catch (Throwable $error) {
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . "\n");
    exit(1);
}
