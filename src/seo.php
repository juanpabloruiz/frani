<?php
declare(strict_types=1);

require_once __DIR__ . '/funciones.php';

/** Texto legible y UTF-8 para descripciones y datos estructurados. */
function seo_texto(string $texto, ?int $limite = null): string
{
    $texto = html_entity_decode(strip_tags($texto), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $texto = trim(preg_replace('/\s+/u', ' ', $texto) ?? $texto);

    if ($limite !== null && preg_match('/^(.{' . max(0, $limite - 1) . '})../us', $texto, $partes)) {
        $texto = rtrim($partes[1]) . '…';
    }

    return $texto;
}

/** Solo se publican fotos existentes dentro del directorio de productos. */
function seo_foto_producto(?string $foto): ?string
{
    if ($foto === null || strlen($foto) > 255 || !preg_match('/\A[A-Za-z0-9_-]+\z/', $foto)) {
        return null;
    }

    $candidato = __DIR__ . '/img/productos/' . $foto . '.jpg';
    $directorio = realpath(__DIR__ . '/img/productos');
    $archivo = realpath($candidato);

    if ($directorio === false || $archivo === false || dirname($archivo) !== $directorio
        || is_link($candidato) || !is_file($archivo) || !is_readable($archivo)) {
        return null;
    }
    $dimensiones = @getimagesize($archivo);
    if ($dimensiones === false || ($dimensiones[2] ?? 0) !== IMAGETYPE_JPEG
        || $dimensiones[0] < 1 || $dimensiones[1] < 1
        || $dimensiones[0] > 10000 || $dimensiones[1] > 10000
        || $dimensiones[0] * $dimensiones[1] > 20000000) {
        return null;
    }

    return $foto;
}

/** La versión cambia con la imagen, los datos de producto o el generador. */
function seo_imagen_social(?string $foto = null, string $alt = 'Frani', ?array $producto = null): array
{
    $foto = seo_foto_producto($foto);
    $archivo = $foto !== null ? __DIR__ . '/img/productos/' . $foto . '.jpg' : __DIR__ . '/img/logo.png';
    $version = substr(hash('sha256', implode(':', [
        $foto ?? 'logo',
        (string) @filemtime($archivo),
        (string) @filesize($archivo),
        (string) @filemtime(__DIR__ . '/imagen-social.php'),
        (string) @filemtime(__DIR__ . '/fonts/Lato-Regular.ttf'),
        (string) @filemtime(__DIR__ . '/fonts/Lato-Bold.ttf'),
        $producto === null ? '' : seo_json([
            'id' => (string) $producto['id'],
            'nombre' => $producto['producto'],
            'descripcion' => $producto['descripcion'] ?? '',
            'categoria' => $producto['categoria'],
            'precio' => number_format((float) $producto['precio'], 2, '.', ''),
        ]),
    ])), 0, 16);
    $parametros = ['v' => $version];
    if ($producto !== null) {
        $parametros['producto'] = (string) $producto['id'];
        $descripcionImagen = seo_texto((string) ($producto['descripcion'] ?? ''), 140);
        if ($descripcionImagen === '') {
            $descripcionImagen = 'Consultá por más detalles de este producto y su disponibilidad.';
        }
        $alt = seo_texto($alt . '. ' . $descripcionImagen . ' Precio: $ ' . moneda($producto['precio']) . ' ARS.', 420);
    } elseif ($foto !== null) {
        $parametros['foto'] = $foto;
    }

    return [
        'url' => sitio_url('imagen-social.php') . '?' . http_build_query($parametros, '', '&', PHP_QUERY_RFC3986),
        'ancho' => 1200,
        'alto' => 630,
        'tipo' => 'image/jpeg',
        'alt' => $producto !== null || $foto !== null ? $alt : 'Logotipo Frani',
    ];
}

function seo_json(array $datos): string
{
    // Impide cerrar el script con una descripción que contenga </script>.
    return json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
}

function seo_producto(array $producto): array
{
    $nombre = seo_texto($producto['producto']);
    $descripcion = seo_texto((string) ($producto['descripcion'] ?? ''));
    if ($descripcion === '') {
        $descripcion = $nombre . ' en Frani. Consultá su precio y disponibilidad.';
    }

    $url = sitio_url(ltrim(producto_path($producto['uri'], $producto['categoria_uri']), '/'));
    $precio = number_format((float) $producto['precio'], 2, '.', '');
    $oferta = [
        '@type' => 'Offer',
        'url' => $url,
        'price' => $precio,
        'priceCurrency' => 'ARS',
        'seller' => ['@type' => 'Organization', 'name' => 'Frani', 'url' => sitio_url()],
    ];
    // NULL significa consultar disponibilidad, no stock confirmado.
    if ($producto['stock'] !== null) {
        $oferta['availability'] = (int) $producto['stock'] > 0
            ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';
    }

    $datosProducto = [
        '@type' => 'Product',
        '@id' => $url . '#producto',
        'url' => $url,
        'name' => $nombre,
        'description' => $descripcion,
        'category' => $producto['categoria'],
        'productID' => (string) $producto['id'],
        'offers' => $oferta,
    ];
    $foto = seo_foto_producto($producto['foto'] ?? null);
    if ($foto !== null) {
        $datosProducto['image'] = [sitio_url('img/productos/' . $foto . '.jpg')];
    }

    return [
        'titulo' => $nombre . ' | Frani',
        'descripcion' => seo_texto($descripcion, 160),
        'descripcion_social' => seo_texto('Precio: $ ' . moneda($producto['precio']) . ' ARS. ' . $descripcion, 200),
        'url' => $url,
        'imagen' => seo_imagen_social($foto, $nombre, $producto),
        'precio' => $precio,
        'datos' => [
            '@context' => 'https://schema.org',
            '@graph' => [
                $datosProducto,
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => sitio_url()],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => $producto['categoria'],
                            'item' => sitio_url(ltrim(categoria_path($producto['categoria_uri']), '/'))],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $nombre, 'item' => $url],
                    ],
                ],
            ],
        ],
    ];
}
