<?php
declare(strict_types=1);

require_once __DIR__ . '/conexion.php';

// Estos documentos públicos no necesitan mantener abierta una sesión del panel.
session_write_close();
header_remove('Set-Cookie');
header_remove('Pragma');
header_remove('Expires');

function sitemap_uri_valida(?string $uri): bool
{
    return $uri !== null && strlen($uri) <= 200
        && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $uri) === 1;
}

function sitemap_xml(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function sitemap_entrada(string $ruta, ?string $modificado = null): string
{
    $entrada = '  <url><loc>' . sitemap_xml(sitio_url(ltrim($ruta, '/'))) . '</loc>';

    if ($modificado !== null) {
        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $modificado, new DateTimeZone('UTC'));
        $errores = DateTimeImmutable::getLastErrors();
        if ($fecha !== false && ($errores === false || ($errores['warning_count'] === 0 && $errores['error_count'] === 0))) {
            $entrada .= '<lastmod>' . $fecha->format('Y-m-d\TH:i:s\Z') . '</lastmod>';
        }
    }

    return $entrada . "</url>\n";
}

try {
    $db = conexion();
    // TIMESTAMP se convierte según la zona de la conexión, no la zona de PHP.
    $db->query("SET time_zone = '+00:00'");

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
        . sitemap_entrada(base_path());

    $categorias = $db->query('SELECT uri FROM categorias ORDER BY id');
    while ($categoria = $categorias->fetch_assoc()) {
        if (sitemap_uri_valida($categoria['uri']) && !uri_categoria_reservada($categoria['uri'])) {
            // No hay historial de borrados: MAX(productos.modificado) no refleja
            // necesariamente el último cambio del catálogo de esta categoría.
            $xml .= sitemap_entrada(categoria_path($categoria['uri']));
        }
    }
    $categorias->free();

    $productos = $db->query(
        'SELECT p.uri, c.uri AS categoria_uri,
            GREATEST(p.agregado, COALESCE(p.modificado, p.agregado),
                c.agregado, COALESCE(c.modificado, c.agregado)) AS modificado
         FROM productos p
         INNER JOIN categorias c ON c.id = p.id_categoria
         ORDER BY p.id'
    );
    while ($producto = $productos->fetch_assoc()) {
        if (sitemap_uri_valida($producto['uri']) && sitemap_uri_valida($producto['categoria_uri'])
            && !uri_categoria_reservada($producto['categoria_uri'])) {
            $xml .= sitemap_entrada(
                producto_path($producto['uri'], $producto['categoria_uri']),
                $producto['modificado']
            );
        }
    }
    $productos->free();
    $xml .= "</urlset>\n";
} catch (Throwable $error) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Retry-After: 300');
    header('Cache-Control: no-store');
    echo "El sitemap no está disponible temporalmente.\n";
    exit;
}

header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=300');
echo $xml;
