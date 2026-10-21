<?php
declare(strict_types=1);

// Imagen vertical descargable para compartir un producto en estados e historias.
header('X-Robots-Tag: noindex');
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($metodo !== 'GET' && $metodo !== 'HEAD') {
    http_response_code(405);
    header('Allow: GET, HEAD');
    header('Cache-Control: no-store');
    header('Content-Type: text/plain; charset=UTF-8');
    exit;
}

function imagen_estado_error(int $estado): never
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    header_remove('Set-Cookie');
    header_remove('Pragma');
    header_remove('Expires');
    http_response_code($estado);
    header('Cache-Control: no-store');
    header('Content-Type: text/plain; charset=UTF-8');
    exit;
}

/** Solo archivos locales dentro del directorio indicado, sin enlaces simbólicos. */
function imagen_estado_archivo(string $directorio, string $nombre): ?string
{
    $base = realpath($directorio);
    $candidato = $directorio . '/' . $nombre;
    if ($base === false || is_link($candidato)) {
        return null;
    }
    $archivo = realpath($candidato);
    if ($archivo === false || dirname($archivo) !== $base || !is_file($archivo) || !is_readable($archivo)) {
        return null;
    }
    return $archivo;
}

/** Limita las dimensiones antes de decodificar imágenes de disco. */
function imagen_estado_abrir(?string $archivo, int $tipo): ?GdImage
{
    if ($archivo === null) {
        return null;
    }
    $datos = @getimagesize($archivo);
    if ($datos === false || ($datos[2] ?? 0) !== $tipo || $datos[0] < 1 || $datos[1] < 1
        || $datos[0] > 10000 || $datos[1] > 10000 || $datos[0] * $datos[1] > 20000000) {
        return null;
    }
    try {
        $imagen = $tipo === IMAGETYPE_JPEG ? @imagecreatefromjpeg($archivo) : @imagecreatefrompng($archivo);
        return $imagen instanceof GdImage ? $imagen : null;
    } catch (Throwable $error) {
        return null;
    }
}

function imagen_estado_ancho(string $texto, float $tamano, string $fuente): int
{
    $caja = imagettfbbox($tamano, 0, $fuente, $texto);
    return max($caja[0], $caja[2], $caja[4], $caja[6]) - min($caja[0], $caja[2], $caja[4], $caja[6]);
}

/** Envuelve por ancho real y agrega puntos suspensivos al exceder las líneas. */
function imagen_estado_lineas(string $texto, float $tamano, string $fuente, int $ancho, int $maximo): array
{
    $lineas = [];
    $linea = '';
    foreach (preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $palabra) {
        $candidata = $linea === '' ? $palabra : $linea . ' ' . $palabra;
        if (imagen_estado_ancho($candidata, $tamano, $fuente) <= $ancho) {
            $linea = $candidata;
            continue;
        }
        if ($linea !== '') {
            $lineas[] = $linea;
            $linea = '';
        }
        foreach (preg_split('//u', $palabra, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $letra) {
            if ($linea !== '' && imagen_estado_ancho($linea . $letra, $tamano, $fuente) > $ancho) {
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
        while ($letras !== [] && imagen_estado_ancho(implode('', $letras) . '…', $tamano, $fuente) > $ancho) {
            array_pop($letras);
        }
        $lineas[$maximo - 1] = rtrim(implode('', $letras)) . '…';
    }
    return $lineas;
}

function imagen_estado_texto(GdImage $lienzo, array $lineas, int $x, int $base, int $interlineado,
    float $tamano, string $fuente, int $color): void
{
    foreach ($lineas as $indice => $linea) {
        imagettftext($lienzo, $tamano, 0, $x, $base + $indice * $interlineado, $color, $fuente, $linea);
    }
}

/** Muestra la imagen completa, conservando su proporción. */
function imagen_estado_encajar(GdImage $lienzo, GdImage $imagen, int $x, int $y, int $ancho, int $alto): void
{
    $escala = min($ancho / imagesx($imagen), $alto / imagesy($imagen));
    $destinoAncho = max(1, (int) round(imagesx($imagen) * $escala));
    $destinoAlto = max(1, (int) round(imagesy($imagen) * $escala));
    imagecopyresampled($lienzo, $imagen, $x + (int) floor(($ancho - $destinoAncho) / 2),
        $y + (int) floor(($alto - $destinoAlto) / 2), 0, 0, $destinoAncho, $destinoAlto, imagesx($imagen), imagesy($imagen));
}

/** Rellena el fondo con una copia ampliada y desenfocada; la foto principal permanece completa. */
function imagen_estado_fondo(GdImage $lienzo, GdImage $imagen): void
{
    $fondo = imagecreatetruecolor(216, 384);
    imagefill($fondo, 0, 0, imagecolorallocate($fondo, 230, 235, 240));
    $escala = max(216 / imagesx($imagen), 384 / imagesy($imagen));
    $ancho = max(1, (int) round(imagesx($imagen) * $escala));
    $alto = max(1, (int) round(imagesy($imagen) * $escala));
    imagecopyresampled($fondo, $imagen, (int) floor((216 - $ancho) / 2),
        (int) floor((384 - $alto) / 2), 0, 0, $ancho, $alto, imagesx($imagen), imagesy($imagen));
    for ($pasada = 0; $pasada < 8; $pasada++) {
        imagefilter($fondo, IMG_FILTER_GAUSSIAN_BLUR);
    }
    imagefilter($fondo, IMG_FILTER_BRIGHTNESS, -25);
    imagecopyresampled($lienzo, $fondo, 0, 0, 0, 0, 1080, 1920, 216, 384);
    unset($fondo);
}

$idSolicitado = $_GET['producto'] ?? null;
if (!is_string($idSolicitado) || preg_match('/^[1-9][0-9]*$/D', $idSolicitado) !== 1
    || ($idProducto = filter_var($idSolicitado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) === false
    || (array_key_exists('descargar', $_GET) && $_GET['descargar'] !== '1')) {
    imagen_estado_error(404);
}
if (!function_exists('imagecreatetruecolor') || !function_exists('imagettftext')) {
    imagen_estado_error(503);
}
try {
    require_once __DIR__ . '/conexion.php';
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    header_remove('Set-Cookie');
    header_remove('Pragma');
    header_remove('Expires');
    require_once __DIR__ . '/seo.php';
    $db = conexion();
    $stmt = $db->prepare(
        'SELECT p.id, p.producto, p.descripcion, p.foto, p.precio, p.uri,
            c.nombre AS categoria, c.uri AS categoria_uri
        FROM productos p
        INNER JOIN categorias c ON c.id = p.id_categoria
        WHERE p.id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $idProducto);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    foreach (['uri', 'categoria_uri'] as $campo) {
        $uri = $producto[$campo] ?? null;
        if (!is_string($uri) || strlen($uri) > 200 || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $uri) !== 1
            || ($campo === 'categoria_uri' && uri_categoria_reservada($uri))) {
            imagen_estado_error(404);
        }
    }
    if (!is_numeric($producto['precio'] ?? null) || !is_finite((float) $producto['precio'])) {
        imagen_estado_error(503);
    }
} catch (Throwable $error) {
    imagen_estado_error(503);
}

$negrita = __DIR__ . '/fonts/Lato-Bold.ttf';
if (!is_readable($negrita)) {
    imagen_estado_error(503);
}
$logo = imagen_estado_abrir(imagen_estado_archivo(__DIR__ . '/img/social', 'frani-logo.png'), IMAGETYPE_PNG);
$foto = $producto['foto'] ?? null;
$archivo = null;
if (is_string($foto) && strlen($foto) <= 255 && preg_match('/^[-A-Za-z0-9_]+$/D', $foto) === 1) {
    $archivo = imagen_estado_archivo(__DIR__ . '/img/productos', $foto . '.jpg');
}
$origen = imagen_estado_abrir($archivo, IMAGETYPE_JPEG);
if ($origen === null && $logo === null) {
    imagen_estado_error(503);
}
$lienzo = imagecreatetruecolor(1080, 1920);
$blanco = imagecolorallocate($lienzo, 255, 255, 255);
$oscuro = imagecolorallocate($lienzo, 20, 32, 40);
imagen_estado_fondo($lienzo, $origen ?? $logo);
// Esta región reserva espacio para la marca y conserva toda la foto, sin recortarla.
imagen_estado_encajar($lienzo, $origen ?? $logo, 0, 200, 1080, 1230);
imagefilledrectangle($lienzo, 60, 120, 330, 199, $oscuro);
imagettftext($lienzo, 44, 0, 99, 180, $blanco, $negrita, 'Frani');
imagefilledrectangle($lienzo, 0, 1450, 1079, 1919, $oscuro);
if ($origen !== null) {
    unset($origen);
}
if ($logo !== null) {
    unset($logo);
}

$nombre = seo_texto((string) $producto['producto'], 300);
imagen_estado_texto($lienzo, imagen_estado_lineas($nombre, 60, $negrita, 960, 2),
    60, 1535, 82, 60, $negrita, $blanco);
$precio = '$ ' . number_format((float) $producto['precio'], 2, ',', '.') . ' ARS';
$tamanoPrecio = 72;
while ($tamanoPrecio > 36 && imagen_estado_ancho($precio, $tamanoPrecio, $negrita) > 960) {
    $tamanoPrecio--;
}
if (imagen_estado_ancho($precio, $tamanoPrecio, $negrita) > 960) {
    // Evita letras diminutas incluso ante un valor fuera del rango habitual del catálogo.
    $precio = '$ ' . str_replace('.', ',', sprintf('%.6e', (float) $producto['precio'])) . ' ARS';
}
imagettftext($lienzo, $tamanoPrecio, 0, 60, 1725, $blanco, $negrita, $precio);
ob_start();
imagejpeg($lienzo, null, 92);
$jpeg = (string) ob_get_clean();
unset($lienzo);

$etag = '"' . hash('sha256', $jpeg) . '"';
header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=86400');
header('ETag: ' . $etag);
if (isset($_GET['descargar'])) {
    header('Content-Disposition: attachment; filename="frani-estado-' . $idProducto . '.jpg"');
}
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
