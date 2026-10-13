<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/imagen_catalogo.php
// Usa un servidor PHP, un logo y categorías temporales sin alterar datos reales.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

function comprobar_catalogo(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function pedir_catalogo(string $url, string $metodo = 'GET', array $cabeceras = []): array
{
    $contexto = stream_context_create(['http' => [
        'method' => $metodo, 'header' => implode("\r\n", $cabeceras), 'ignore_errors' => true, 'timeout' => 5,
    ]]);
    $cuerpo = @file_get_contents($url, false, $contexto);
    $cabecerasRespuesta = $http_response_header ?? [];
    preg_match('/\s(\d{3})\s/', $cabecerasRespuesta[0] ?? '', $estado);
    $respuesta = ['estado' => (int) ($estado[1] ?? 0), 'cuerpo' => $cuerpo === false ? '' : $cuerpo];
    foreach ($cabecerasRespuesta as $cabecera) {
        if (str_contains($cabecera, ':')) {
            [$nombre, $valor] = explode(':', $cabecera, 2);
            $respuesta[strtolower($nombre)] = trim($valor);
        }
    }
    return $respuesta;
}

function comprobar_jpeg_catalogo(array $respuesta): GdImage
{
    comprobar_catalogo($respuesta['estado'] === 200, 'La tarjeta debe responder 200');
    comprobar_catalogo(($respuesta['content-type'] ?? '') === 'image/jpeg', 'Debe enviar image/jpeg');
    comprobar_catalogo(($respuesta['cache-control'] ?? '') === 'public, max-age=86400', 'Debe permitir caché pública');
    comprobar_catalogo(($respuesta['x-robots-tag'] ?? '') === 'noindex', 'La imagen debe llevar noindex');
    comprobar_catalogo(!isset($respuesta['set-cookie']) && !isset($respuesta['pragma']) && !isset($respuesta['expires']),
        'Debe quitar los encabezados de sesión');
    $datos = getimagesizefromstring($respuesta['cuerpo']);
    comprobar_catalogo($datos !== false && $datos[0] === 1200 && $datos[1] === 630 && $datos[2] === IMAGETYPE_JPEG,
        'Debe ser un JPEG de 1200 × 630');
    comprobar_catalogo((int) ($respuesta['content-length'] ?? 0) === strlen($respuesta['cuerpo']), 'El tamaño debe coincidir con Content-Length');
    return imagecreatefromstring($respuesta['cuerpo']);
}

function comprobar_pixel_catalogo(GdImage $imagen, int $x, int $y, array $esperado, string $mensaje): void
{
    $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, $y));
    foreach (['red', 'green', 'blue'] as $indice => $canal) {
        comprobar_catalogo(abs($color[$canal] - $esperado[$indice]) < 15, $mensaje);
    }
}

function comprobar_texto_catalogo(GdImage $imagen, int $arriba, int $abajo, string $mensaje): void
{
    $oscuros = 0;
    for ($y = $arriba; $y <= $abajo; $y++) {
        for ($x = 60; $x <= 1140; $x++) {
            $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, $y));
            if ($color['red'] + $color['green'] + $color['blue'] < 450) {
                $oscuros++;
            }
        }
    }
    comprobar_catalogo($oscuros > 100, $mensaje);
}

function comprobar_blanco_catalogo(GdImage $imagen, int $x1, int $y1, int $x2, int $y2, string $mensaje): void
{
    for ($y = $y1; $y <= $y2; $y++) {
        for ($x = $x1; $x <= $x2; $x++) {
            $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, $y));
            comprobar_catalogo(min($color['red'], $color['green'], $color['blue']) > 240, $mensaje);
        }
    }
}

$temporal = sys_get_temp_dir() . '/frani-imagen-catalogo-' . bin2hex(random_bytes(8));
$servidor = null;
$canales = [];
$salida = 0;
try {
    mkdir($temporal . '/img/social', 0700, true);
    mkdir($temporal . '/fonts', 0700);
    foreach (['imagen-catalogo.php', 'seo.php', 'funciones.php'] as $archivo) {
        comprobar_catalogo(copy(__DIR__ . '/../src/' . $archivo, $temporal . '/' . $archivo), 'Debe copiar los helpers reales');
    }
    foreach (['Lato-Regular.ttf'] as $fuente) {
        comprobar_catalogo(copy(__DIR__ . '/../src/fonts/' . $fuente, $temporal . '/fonts/' . $fuente), 'Debe copiar la tipografía');
    }
    // El fixture tiene una mitad negra y otra gris para detectar recortes.
    $logo = imagecreatetruecolor(1766, 968);
    imagefilledrectangle($logo, 0, 0, 882, 967, imagecolorallocate($logo, 0, 0, 0));
    imagefilledrectangle($logo, 883, 0, 1765, 967, imagecolorallocate($logo, 100, 100, 100));
    imagepng($logo, $temporal . '/img/social/frani-logo.png');
    imagedestroy($logo);
    file_put_contents($temporal . '/conexion.php', <<<'PHP'
<?php
require_once __DIR__ . '/funciones.php';
putenv('APP_DOMAIN=frani.ar');
function conexion() {
    file_put_contents(__DIR__ . '/db-consultada', '1');
    return new class {
        public function prepare(string $sql) {
            if (!str_contains($sql, 'SELECT id, nombre, uri FROM categorias WHERE id = ?')) {
                throw new RuntimeException('La categoría debe consultarse por ID preparado');
            }
            return new class {
                private int $id;
                public function bind_param(string $tipo, int &$id): void {
                    if ($tipo !== 'i') { throw new RuntimeException('El ID debe ser entero'); }
                    $this->id = $id;
                }
                public function execute(): void {
                    if (session_status() === PHP_SESSION_ACTIVE) { throw new RuntimeException('La sesión debe estar liberada'); }
                    if ($this->id === 98) { throw new RuntimeException('Fallo de DB simulado'); }
                }
                public function get_result() { return $this; }
                public function fetch_assoc() {
                    $datos = json_decode(file_get_contents(__DIR__ . '/categorias.json'), true);
                    return $datos[(string) $this->id] ?? null;
                }
                public function close(): void {}
            };
        }
    };
}
PHP);
    $categorias = [
        1 => ['id' => 1, 'nombre' => 'Librería y papelería', 'uri' => 'libreria-y-papeleria'],
        2 => ['id' => 2, 'nombre' => str_repeat('Ñandú', 30), 'uri' => 'titulo-largo'],
        3 => ['id' => 3, 'nombre' => 'Pendiente', 'uri' => null],
        4 => ['id' => 4, 'nombre' => 'Pendiente', 'uri' => ''],
        5 => ['id' => 5, 'nombre' => 'Reservada', 'uri' => 'panel'],
        6 => ['id' => 6, 'nombre' => 'Inválida', 'uri' => '../secreto'],
        7 => ['id' => 7, 'nombre' => 'Inválida', 'uri' => 'MASCOTAS'],
        8 => ['id' => 8, 'nombre' => 'Regalos para bebés, cumpleaños y ocasiones especiales', 'uri' => 'regalos'],
        9 => ['id' => 9, 'nombre' => 'Inválida', 'uri' => str_repeat('a', 201)],
    ];
    file_put_contents($temporal . '/categorias.json', json_encode($categorias, JSON_UNESCAPED_UNICODE));
    $socket = stream_socket_server('tcp://127.0.0.1:0', $codigo, $mensaje);
    comprobar_catalogo($socket !== false, 'Debe reservar un puerto de prueba');
    $direccion = stream_socket_get_name($socket, false);
    fclose($socket);
    $servidor = proc_open([PHP_BINARY, '-S', $direccion, '-t', $temporal],
        [0 => ['pipe', 'r'], 1 => ['file', $temporal . '/servidor.log', 'a'], 2 => ['file', $temporal . '/servidor.log', 'a']], $canales);
    comprobar_catalogo(is_resource($servidor), 'Debe iniciar el servidor de prueba');
    $url = 'http://' . $direccion . '/imagen-catalogo.php';
    for ($intento = 0; $intento < 50; $intento++) {
        $conexion = @stream_socket_client('tcp://' . $direccion, $codigo, $mensaje, 0.1);
        if ($conexion !== false) {
            fclose($conexion);
            break;
        }
        usleep(20000);
    }

    $inicio = pedir_catalogo($url);
    $imagen = comprobar_jpeg_catalogo($inicio);
    comprobar_catalogo(!file_exists($temporal . '/db-consultada'), 'La tarjeta de inicio no debe consultar DB');
    comprobar_pixel_catalogo($imagen, 130, 275, [255, 255, 255], 'El logo grande debe conservar el margen izquierdo');
    comprobar_pixel_catalogo($imagen, 1070, 275, [255, 255, 255], 'El logo grande debe conservar el margen derecho');
    comprobar_pixel_catalogo($imagen, 160, 275, [0, 0, 0], 'Debe conservar el extremo izquierdo del logo');
    comprobar_pixel_catalogo($imagen, 1040, 275, [100, 100, 100], 'Debe conservar el extremo derecho del logo');
    comprobar_pixel_catalogo($imagen, 500, 25, [0, 0, 0], 'Debe conservar la parte superior del logo');
    comprobar_pixel_catalogo($imagen, 700, 500, [100, 100, 100], 'Debe conservar la parte inferior del logo');
    for ($y = 150; $y <= 490; $y += 20) {
        for ($x = 170; $x <= 1030; $x += 20) {
            comprobar_pixel_catalogo($imagen, $x, $y, $x < 600 ? [0, 0, 0] : [100, 100, 100],
                'El logo debe mostrarse completo sin título ni categoría superpuestos');
        }
    }
    comprobar_blanco_catalogo($imagen, 60, 300, 130, 425, 'No debe añadir un título fuera del logo');
    comprobar_blanco_catalogo($imagen, 1070, 300, 1140, 425, 'No debe añadir una cabecera de categoría fuera del logo');
    comprobar_blanco_catalogo($imagen, 60, 612, 1140, 629, 'Debe quitar el pie de dominio y evitar una tercera línea de descripción');
    comprobar_texto_catalogo($imagen, 518, 560, 'Debe mostrar la descripción grande debajo del logo');
    comprobar_texto_catalogo($imagen, 570, 606, 'La descripción completa de inicio debe ocupar dos líneas visibles');
    imagedestroy($imagen);
    comprobar_catalogo(pedir_catalogo($url . '?titulo=FALSO&descripcion=FALSA&foto=otra&producto=1')['cuerpo'] === $inicio['cuerpo'],
        'La tarjeta de inicio debe ignorar contenido proporcionado por el visitante');

    foreach (['0', '-1', '01', '1.0', '1e2', '1 OR 1=1', '', '999999999999999999999'] as $idInvalido) {
        $respuesta = pedir_catalogo($url . '?categoria=' . rawurlencode($idInvalido));
        comprobar_catalogo($respuesta['estado'] === 404 && ($respuesta['cache-control'] ?? '') === 'no-store', 'Debe rechazar IDs inválidos');
    }
    comprobar_catalogo(pedir_catalogo($url . '?categoria%5B%5D=1')['estado'] === 404, 'Debe rechazar categorías de tipo array');
    comprobar_catalogo(!file_exists($temporal . '/db-consultada'), 'Los IDs inválidos no deben consultar DB');
    $categoria = pedir_catalogo($url . '?categoria=1');
    $imagen = comprobar_jpeg_catalogo($categoria);
    comprobar_catalogo($categoria['cuerpo'] !== $inicio['cuerpo'], 'La categoría debe mostrar su descripción correspondiente');
    comprobar_texto_catalogo($imagen, 518, 606, 'La categoría debe mostrar su descripción ampliada');
    comprobar_blanco_catalogo($imagen, 60, 612, 1140, 629, 'La categoría no debe añadir un pie de dominio');
    imagedestroy($imagen);
    comprobar_catalogo(pedir_catalogo($url . '?categoria=1&titulo=FALSO&descripcion=FALSA&precio=100')['cuerpo'] === $categoria['cuerpo'],
        'La imagen debe ignorar título, descripción y precio proporcionados por el visitante');
    foreach ([2, 8] as $idValido) {
        $respuesta = pedir_catalogo($url . '?categoria=' . $idValido);
        $imagen = comprobar_jpeg_catalogo($respuesta);
        comprobar_catalogo($respuesta['cuerpo'] !== $inicio['cuerpo'], 'Los nombres largos deben aparecer dentro de su descripción correspondiente');
        comprobar_texto_catalogo($imagen, 518, 606, 'Los acentos y palabras largas deben seguir siendo legibles');
        comprobar_blanco_catalogo($imagen, 60, 612, 1140, 629, 'Una descripción larga debe limitarse a dos líneas');
        comprobar_pixel_catalogo($imagen, 30, 590, [255, 255, 255], 'La descripción debe conservar su margen izquierdo');
        comprobar_pixel_catalogo($imagen, 1170, 590, [255, 255, 255], 'La descripción debe conservar su margen derecho');
        imagedestroy($imagen);
    }
    foreach ([3, 4, 5, 6, 7, 9, 404] as $idNoPublico) {
        $respuesta = pedir_catalogo($url . '?categoria=' . $idNoPublico);
        comprobar_catalogo($respuesta['estado'] === 404 && ($respuesta['cache-control'] ?? '') === 'no-store', 'Una categoría no publicada debe responder 404 sin caché');
    }
    $dbCaida = pedir_catalogo($url . '?categoria=98');
    comprobar_catalogo($dbCaida['estado'] === 503 && ($dbCaida['cache-control'] ?? '') === 'no-store', 'Una DB no disponible debe responder 503 sin caché');

    foreach ([$url, $url . '?categoria=1'] as $urlValida) {
        $get = pedir_catalogo($urlValida);
        $head = pedir_catalogo($urlValida, 'HEAD');
        comprobar_catalogo($head['estado'] === 200 && $head['cuerpo'] === '' && ($head['etag'] ?? '') === $get['etag']
            && ($head['content-length'] ?? '') === $get['content-length'], 'HEAD debe conservar tamaño y ETag sin cuerpo');
        $cache = pedir_catalogo($urlValida, 'GET', ['If-None-Match: W/' . $get['etag']]);
        comprobar_catalogo($cache['estado'] === 304 && $cache['cuerpo'] === '', 'Debe admitir validación de caché');
    }
    $categorias[1]['nombre'] = 'Librería, papelería y útiles';
    file_put_contents($temporal . '/categorias.json', json_encode($categorias, JSON_UNESCAPED_UNICODE));
    $actualizada = pedir_catalogo($url . '?categoria=1');
    comprobar_catalogo($actualizada['cuerpo'] !== $categoria['cuerpo'] && $actualizada['etag'] !== $categoria['etag'], 'Una descripción actualizada debe modificar la tarjeta y su ETag');
    $post = pedir_catalogo($url, 'POST');
    comprobar_catalogo($post['estado'] === 405 && ($post['allow'] ?? '') === 'GET, HEAD', 'Debe rechazar otros métodos');
    unlink($temporal . '/img/social/frani-logo.png');
    $sinLogo = pedir_catalogo($url);
    comprobar_catalogo($sinLogo['estado'] === 503 && ($sinLogo['cache-control'] ?? '') === 'no-store', 'Un logo ausente debe fallar sin caché');
    fwrite(STDOUT, "OK: JPEG 1200×630, logo grande completo, descripción 24pt en dos líneas, sin título ni pie, IDs públicos, HEAD y caché sin sesión.\n");
} catch (Throwable $error) {
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . "\n");
    $salida = 1;
} finally {
    if (is_resource($servidor)) {
        proc_terminate($servidor);
        foreach ($canales as $canal) {
            fclose($canal);
        }
        proc_close($servidor);
    }
    if (is_dir($temporal)) {
        $archivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($temporal, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($archivos as $archivo) {
            if ($archivo->isDir() && !$archivo->isLink()) {
                rmdir($archivo->getPathname());
            } else {
                unlink($archivo->getPathname());
            }
        }
        rmdir($temporal);
    }
}
exit($salida);
