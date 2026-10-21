<?php
declare(strict_types=1);

// Tarjetas de inicio y categorías; las imágenes de productos usan otro endpoint.
header('X-Robots-Tag: noindex');
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($metodo !== 'GET' && $metodo !== 'HEAD') {
    http_response_code(405);
    header('Allow: GET, HEAD');
    header('Content-Type: text/plain; charset=UTF-8');
    exit;
}

function imagen_catalogo_error(int $estado): never
{
    http_response_code($estado);
    header('Cache-Control: no-store');
    header('Content-Type: text/plain; charset=UTF-8');
    exit;
}

function imagen_catalogo_liberar_sesion(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    header_remove('Set-Cookie');
    header_remove('Pragma');
    header_remove('Expires');
}

function imagen_catalogo_ancho(string $texto, float $tamano, string $fuente): int
{
    $caja = imagettfbbox($tamano, 0, $fuente, $texto);
    return max($caja[0], $caja[2], $caja[4], $caja[6]) - min($caja[0], $caja[2], $caja[4], $caja[6]);
}

/** Ajusta UTF-8 al ancho de la fuente, incluso palabras sin espacios. */
function imagen_catalogo_lineas(string $texto, float $tamano, string $fuente, int $ancho, int $maximo): array
{
    $lineas = [];
    $linea = '';
    foreach (preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $palabra) {
        $candidata = $linea === '' ? $palabra : $linea . ' ' . $palabra;
        if (imagen_catalogo_ancho($candidata, $tamano, $fuente) <= $ancho) {
            $linea = $candidata;
            continue;
        }
        if ($linea !== '') {
            $lineas[] = $linea;
            $linea = '';
        }
        foreach (preg_split('//u', $palabra, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $letra) {
            if ($linea !== '' && imagen_catalogo_ancho($linea . $letra, $tamano, $fuente) > $ancho) {
                $lineas[] = $linea;
                $linea = '';
            }
            $linea .= $letra;
        }
    }
    if ($linea !== '') {
        $lineas[] = $linea;
    }
    if (count($lineas) > $maximo) {
        $lineas = array_slice($lineas, 0, $maximo);
        $letras = preg_split('//u', $lineas[$maximo - 1], -1, PREG_SPLIT_NO_EMPTY) ?: [];
        while ($letras !== [] && imagen_catalogo_ancho(implode('', $letras) . '…', $tamano, $fuente) > $ancho) {
            array_pop($letras);
        }
        $lineas[$maximo - 1] = rtrim(implode('', $letras)) . '…';
    }
    return $lineas;
}

$idCategoria = null;
if (array_key_exists('categoria', $_GET)) {
    $idSolicitado = $_GET['categoria'];
    if (!is_string($idSolicitado) || preg_match('/^[1-9][0-9]*$/D', $idSolicitado) !== 1
        || ($idCategoria = filter_var($idSolicitado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) === false) {
        imagen_catalogo_error(404);
    }
}

try {
    require_once __DIR__ . '/conexion.php';
    imagen_catalogo_liberar_sesion();
    require_once __DIR__ . '/seo.php';
    $categoria = null;
    if ($idCategoria !== null) {
        $db = conexion();
        $stmt = $db->prepare('SELECT id, nombre, uri FROM categorias WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $idCategoria);
        $stmt->execute();
        $categoria = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $uri = $categoria['uri'] ?? null;
        if (!$categoria || !is_string($uri) || strlen($uri) > 200
            || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $uri) !== 1 || uri_categoria_reservada($uri)) {
            imagen_catalogo_error(404);
        }
    }
    $contenido = seo_catalogo_contenido($categoria);
} catch (Throwable $error) {
    imagen_catalogo_liberar_sesion();
    imagen_catalogo_error(503);
}

$regular = __DIR__ . '/fonts/Lato-Regular.ttf';
$archivoLogo = __DIR__ . '/img/social/frani-logo.png';
$directorioLogo = realpath(__DIR__ . '/img/social');
$logoReal = realpath($archivoLogo);
if (!function_exists('imagecreatetruecolor') || !function_exists('imagettftext')
    || !is_readable($regular)
    || $directorioLogo === false || $logoReal === false || dirname($logoReal) !== $directorioLogo
    || is_link($archivoLogo) || !is_file($logoReal) || !is_readable($logoReal)) {
    imagen_catalogo_error(503);
}
$dimensiones = @getimagesize($logoReal);
if ($dimensiones === false || ($dimensiones[2] ?? 0) !== IMAGETYPE_PNG
    || $dimensiones[0] < 1 || $dimensiones[1] < 1
    || $dimensiones[0] > 10000 || $dimensiones[1] > 10000
    || $dimensiones[0] * $dimensiones[1] > 20000000) {
    imagen_catalogo_error(503);
}
$logo = @imagecreatefrompng($logoReal);
if (!$logo instanceof GdImage) {
    imagen_catalogo_error(503);
}
$lienzo = imagecreatetruecolor(1200, 630);
imagefill($lienzo, 0, 0, imagecolorallocate($lienzo, 255, 255, 255));
$colorDescripcion = imagecolorallocate($lienzo, 32, 32, 32);

// Contiene la marca completa y reserva dos líneas grandes para la descripción.
$escala = min(900 / imagesx($logo), 494 / imagesy($logo));
$logoAncho = max(1, (int) round(imagesx($logo) * $escala));
$logoAlto = max(1, (int) round(imagesy($logo) * $escala));
imagecopyresampled($lienzo, $logo, (int) floor((1200 - $logoAncho) / 2), 16,
    0, 0, $logoAncho, $logoAlto, imagesx($logo), imagesy($logo));
unset($logo);

// La descripción coincide con los metadatos; no se añade título ni pie de URL.
$descripcion = imagen_catalogo_lineas(seo_texto((string) $contenido['descripcion']), 24, $regular, 1080, 2);
foreach ($descripcion as $indice => $linea) {
    $textoX = (int) floor((1200 - imagen_catalogo_ancho($linea, 24, $regular)) / 2);
    imagettftext($lienzo, 24, 0, $textoX, 550 + $indice * 40, $colorDescripcion, $regular, $linea);
}

ob_start();
imagejpeg($lienzo, null, 90);
$jpeg = (string) ob_get_clean();
unset($lienzo);
$etag = '"' . hash('sha256', $jpeg) . '"';
header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=86400');
header('ETag: ' . $etag);
foreach (explode(',', $_SERVER['HTTP_IF_NONE_MATCH'] ?? '') as $etagCliente) {
    $etagCliente = trim($etagCliente);
    if ($etagCliente === '*' || preg_replace('/^W\//', '', $etagCliente) === $etag) {
        http_response_code(304);
        exit;
    }
}
header('Content-Length: ' . strlen($jpeg));
if ($metodo === 'GET') {
    echo $jpeg;
}
