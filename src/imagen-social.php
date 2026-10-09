<?php
declare(strict_types=1);

// Vista previa pública. El modo foto/logo no necesita sesión ni base de datos.
header('X-Robots-Tag: noindex');
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($metodo !== 'GET' && $metodo !== 'HEAD') {
    http_response_code(405);
    header('Allow: GET, HEAD');
    header('Content-Type: text/plain; charset=UTF-8');
    exit;
}

if (!function_exists('imagecreatetruecolor')) {
    http_response_code(503);
    header('Cache-Control: no-store');
    exit;
}

/** Solo acepta archivos dentro del directorio indicado, sin enlaces simbólicos. */
function imagen_social_archivo(string $directorio, string $nombre): ?string
{
    $base = realpath($directorio);
    $candidato = $directorio . '/' . $nombre;
    if ($base === false || is_link($candidato)) {
        return null;
    }
    $archivo = realpath($candidato);
    if ($archivo === false || !str_starts_with($archivo, $base . DIRECTORY_SEPARATOR)
        || !is_file($archivo) || !is_readable($archivo)) {
        return null;
    }
    return $archivo;
}

/** Evita decodificar imágenes corruptas o con dimensiones excesivas. */
function imagen_social_abrir(?string $archivo, int $tipo): ?GdImage
{
    if ($archivo === null) {
        return null;
    }
    $dimensiones = @getimagesize($archivo);
    if ($dimensiones === false || ($dimensiones[2] ?? 0) !== $tipo
        || $dimensiones[0] < 1 || $dimensiones[1] < 1
        || $dimensiones[0] > 10000 || $dimensiones[1] > 10000
        || $dimensiones[0] * $dimensiones[1] > 20000000) {
        return null;
    }
    try {
        $imagen = $tipo === IMAGETYPE_JPEG
            ? @imagecreatefromjpeg($archivo)
            : @imagecreatefrompng($archivo);
        return $imagen instanceof GdImage ? $imagen : null;
    } catch (Throwable $error) {
        return null;
    }
}

function imagen_social_error(int $estado): never
{
    http_response_code($estado);
    header('Cache-Control: no-store');
    header('Content-Type: text/plain; charset=UTF-8');
    exit;
}

function imagen_social_ancho(string $texto, float $tamano, string $fuente): int
{
    $caja = imagettfbbox($tamano, 0, $fuente, $texto);
    return max($caja[0], $caja[2], $caja[4], $caja[6]) - min($caja[0], $caja[2], $caja[4], $caja[6]);
}

/** Ajusta por ancho real de fuente, incluso palabras largas sin espacios. */
function imagen_social_lineas(string $texto, float $tamano, string $fuente, int $ancho, int $maximo): array
{
    $lineas = [];
    $linea = '';
    foreach (preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $palabra) {
        $candidata = $linea === '' ? $palabra : $linea . ' ' . $palabra;
        if (imagen_social_ancho($candidata, $tamano, $fuente) <= $ancho) {
            $linea = $candidata;
            continue;
        }
        if ($linea !== '') {
            $lineas[] = $linea;
            $linea = '';
        }
        foreach (preg_split('//u', $palabra, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $letra) {
            if ($linea !== '' && imagen_social_ancho($linea . $letra, $tamano, $fuente) > $ancho) {
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
        while ($letras !== [] && imagen_social_ancho(implode('', $letras) . '…', $tamano, $fuente) > $ancho) {
            array_pop($letras);
        }
        $lineas[$maximo - 1] = rtrim(implode('', $letras)) . '…';
    }
    return $lineas;
}

function imagen_social_texto(GdImage $lienzo, array $lineas, int $x, int $base, int $interlineado,
    float $tamano, string $fuente, int $color): void
{
    foreach ($lineas as $indice => $linea) {
        imagettftext($lienzo, $tamano, 0, $x, $base + $indice * $interlineado, $color, $fuente, $linea);
    }
}

$producto = null;
if (array_key_exists('producto', $_GET)) {
    $idSolicitado = $_GET['producto'];
    if (!is_string($idSolicitado) || preg_match('/^[1-9][0-9]*$/D', $idSolicitado) !== 1
        || ($idProducto = filter_var($idSolicitado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) === false) {
        imagen_social_error(404);
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
            'SELECT p.id, p.producto, p.descripcion, p.foto, p.precio,
                c.nombre AS categoria, c.uri AS categoria_uri
            FROM productos p
            INNER JOIN categorias c ON c.id = p.id_categoria
            WHERE p.id = ?
            LIMIT 1'
        );
        $stmt->bind_param('i', $idProducto);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$producto) {
            imagen_social_error(404);
        }
        if (!is_numeric($producto['precio'] ?? null) || !is_finite((float) $producto['precio'])) {
            imagen_social_error(503);
        }
    } catch (Throwable $error) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        header_remove('Set-Cookie');
        header_remove('Pragma');
        header_remove('Expires');
        imagen_social_error(503);
    }
}

$foto = $producto !== null ? $producto['foto'] : ($_GET['foto'] ?? null);
$archivo = null;
if (is_string($foto) && strlen($foto) <= 255 && preg_match('/^[-A-Za-z0-9_]+$/D', $foto) === 1) {
    $archivo = imagen_social_archivo(__DIR__ . '/img/productos', $foto . '.jpg');
}
$origen = imagen_social_abrir($archivo, IMAGETYPE_JPEG);
if ($origen === null) {
    $origen = imagen_social_abrir(imagen_social_archivo(__DIR__ . '/img', 'logo.png'), IMAGETYPE_PNG);
}

$ancho = 1200;
$alto = 630;
$lienzo = imagecreatetruecolor($ancho, $alto);
imagefill($lienzo, 0, 0, imagecolorallocate($lienzo, 255, 255, 255));
$fotoX = 0;
$fotoY = 0;
$fotoAncho = $ancho;
$fotoAlto = $alto;
if ($producto !== null) {
    $regular = __DIR__ . '/fonts/Lato-Regular.ttf';
    $negrita = __DIR__ . '/fonts/Lato-Bold.ttf';
    if (!function_exists('imagettftext') || !is_readable($regular) || !is_readable($negrita)) {
        imagen_social_error(503);
    }
    $fotoX = 24;
    $fotoY = 24;
    $fotoAncho = 552;
    $fotoAlto = 582;
    $fondo = imagecolorallocate($lienzo, 241, 246, 249);
    $principal = imagecolorallocate($lienzo, 25, 52, 69);
    $secundario = imagecolorallocate($lienzo, 78, 101, 116);
    $borde = imagecolorallocate($lienzo, 214, 226, 233);
    imagefilledrectangle($lienzo, 600, 0, 1199, 629, $fondo);
    imagefilledrectangle($lienzo, 648, 40, 683, 44, $principal);
    imagettftext($lienzo, 24, 0, 648, 88, $principal, $negrita, 'Frani');
    imagen_social_texto($lienzo,
        imagen_social_lineas(seo_texto((string) $producto['categoria'], 100), 16, $regular, 504, 1),
        648, 123, 26, 16, $regular, $secundario);

    $nombre = seo_texto((string) $producto['producto'], 200);
    $titulo = imagen_social_lineas($nombre, 32, $negrita, 504, 3);
    imagen_social_texto($lienzo, $titulo, 648, 185, 46, 32, $negrita, $principal);
    $descripcion = seo_texto((string) ($producto['descripcion'] ?? ''));
    if ($descripcion === '') {
        $descripcion = 'Consultá por más detalles de este producto y su disponibilidad.';
    }
    $descripcion = imagen_social_lineas(seo_texto($descripcion, 140), 18, $regular, 504, 3);
    imagen_social_texto($lienzo, $descripcion, 648, 326, 31, 18, $regular, $secundario);

    imageline($lienzo, 648, 435, 1152, 435, $borde);
    imagettftext($lienzo, 16, 0, 648, 473, $secundario, $regular, 'Precio');
    $precio = '$ ' . number_format((float) $producto['precio'], 2, ',', '.') . ' ARS';
    $tamanoPrecio = 34;
    while ($tamanoPrecio > 16 && imagen_social_ancho($precio, $tamanoPrecio, $negrita) > 504) {
        $tamanoPrecio--;
    }
    imagettftext($lienzo, $tamanoPrecio, 0, 648, 530, $principal, $negrita, $precio);
    $dominio = seo_texto(getenv('APP_DOMAIN') ?: 'frani.ar', 100);
    imagen_social_texto($lienzo, imagen_social_lineas($dominio, 16, $regular, 504, 1),
        648, 590, 26, 16, $regular, $secundario);
}
if ($origen !== null) {
    $escala = min($fotoAncho / imagesx($origen), $fotoAlto / imagesy($origen));
    $destinoAncho = max(1, (int) round(imagesx($origen) * $escala));
    $destinoAlto = max(1, (int) round(imagesy($origen) * $escala));
    imagecopyresampled(
        $lienzo,
        $origen,
        $fotoX + (int) floor(($fotoAncho - $destinoAncho) / 2),
        $fotoY + (int) floor(($fotoAlto - $destinoAlto) / 2),
        0,
        0,
        $destinoAncho,
        $destinoAlto,
        imagesx($origen),
        imagesy($origen)
    );
    imagedestroy($origen);
}
ob_start();
imagejpeg($lienzo, null, 90);
$jpeg = (string) ob_get_clean();
imagedestroy($lienzo);

$etag = '"' . hash('sha256', $jpeg) . '"';
header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=86400');
header('ETag: ' . $etag);
$etagsCliente = explode(',', $_SERVER['HTTP_IF_NONE_MATCH'] ?? '');
foreach ($etagsCliente as $etagCliente) {
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
