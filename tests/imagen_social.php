<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/imagen_social.php
// Usa imágenes y un servidor PHP temporales; no modifica datos de la aplicación.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

function comprobar_imagen(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function pedir_imagen(string $url, string $metodo = 'GET', array $cabeceras = []): array
{
    $contexto = stream_context_create(['http' => [
        'method' => $metodo,
        'header' => implode("\r\n", $cabeceras),
        'ignore_errors' => true,
        'timeout' => 5,
    ]]);
    $cuerpo = @file_get_contents($url, false, $contexto);
    $cabecerasRespuesta = $http_response_header ?? [];
    preg_match('/\s(\d{3})\s/', $cabecerasRespuesta[0] ?? '', $estado);
    $resultado = ['estado' => (int) ($estado[1] ?? 0), 'cuerpo' => $cuerpo === false ? '' : $cuerpo];
    foreach ($cabecerasRespuesta as $cabecera) {
        if (str_contains($cabecera, ':')) {
            [$nombre, $valor] = explode(':', $cabecera, 2);
            $resultado[strtolower($nombre)] = trim($valor);
        }
    }
    return $resultado;
}

function comprobar_jpeg(array $respuesta): GdImage
{
    comprobar_imagen($respuesta['estado'] === 200, 'La vista previa debe responder 200');
    comprobar_imagen(($respuesta['content-type'] ?? '') === 'image/jpeg', 'Debe enviar image/jpeg');
    comprobar_imagen(($respuesta['cache-control'] ?? '') === 'public, max-age=86400', 'Debe permitir caché pública');
    comprobar_imagen(($respuesta['x-robots-tag'] ?? '') === 'noindex', 'La imagen debe llevar noindex');
    $datos = getimagesizefromstring($respuesta['cuerpo']);
    comprobar_imagen($datos !== false && $datos[0] === 1200 && $datos[1] === 630 && $datos[2] === IMAGETYPE_JPEG,
        'La respuesta debe ser un JPEG de 1200 × 630');
    comprobar_imagen((int) ($respuesta['content-length'] ?? 0) === strlen($respuesta['cuerpo']), 'Content-Length debe coincidir');
    return imagecreatefromstring($respuesta['cuerpo']);
}

function comprobar_pixel(GdImage $imagen, int $x, int $y, array $esperado, string $mensaje): void
{
    $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, $y));
    foreach (['red', 'green', 'blue'] as $indice => $canal) {
        comprobar_imagen(abs($color[$canal] - $esperado[$indice]) < 15, $mensaje);
    }
}

function comprobar_texto_imagen(GdImage $imagen, int $arriba, int $abajo, string $mensaje): void
{
    $oscuros = 0;
    for ($y = $arriba; $y <= $abajo; $y++) {
        for ($x = 648; $x <= 1152; $x++) {
            $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, $y));
            if ($color['red'] + $color['green'] + $color['blue'] < 450) {
                $oscuros++;
            }
        }
    }
    comprobar_imagen($oscuros > 100, $mensaje);
}

$temporal = sys_get_temp_dir() . '/frani-imagen-social-' . bin2hex(random_bytes(8));
$servidor = null;
$canales = [];
$salida = 0;
try {
    mkdir($temporal . '/img/productos', 0700, true);
    copy(__DIR__ . '/../src/imagen-social.php', $temporal . '/imagen-social.php');
    copy(__DIR__ . '/../src/seo.php', $temporal . '/seo.php');
    mkdir($temporal . '/fonts', 0700);
    foreach (['Lato-Regular.ttf', 'Lato-Bold.ttf'] as $fuente) {
        comprobar_imagen(copy(__DIR__ . '/../src/fonts/' . $fuente, $temporal . '/fonts/' . $fuente), 'Debe copiar la fuente de la tarjeta');
    }
    // Simula el límite de acceso a DB y conserva los helpers de texto reales.
    file_put_contents($temporal . '/funciones.php', <<<'PHP'
<?php
session_start();
putenv('APP_DOMAIN=frani.ar');
PHP);
    file_put_contents($temporal . '/conexion.php', <<<'PHP'
<?php
require_once __DIR__ . '/funciones.php';
file_put_contents(__DIR__ . '/db-cargada', '1');
function conexion() {
    return new class {
        public function prepare(string $sql) {
            if (!str_contains($sql, 'INNER JOIN categorias') || !str_contains($sql, 'WHERE p.id = ?')) {
                throw new RuntimeException('La consulta debe estar preparada y limitarse al catálogo público');
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
                    $datos = json_decode(file_get_contents(__DIR__ . '/productos.json'), true);
                    return $datos[(string) $this->id] ?? null;
                }
                public function close(): void {}
            };
        }
    };
}
PHP);
    $productos = [
        1 => ['id' => 1, 'producto' => 'Masa mágica de colores',
            'descripcion' => 'Masa para crear figuras. Colores surtidos y textura suave.', 'foto' => 'prueba',
            'precio' => '3500.00', 'categoria' => 'Juguetes', 'categoria_uri' => 'juguetes'],
        2 => ['id' => 2, 'producto' => str_repeat('Ñandú', 40),
            'descripcion' => str_repeat('Descripción áéíóú con &amp; detalles <b>seguros</b>. ', 8), 'foto' => 'prueba',
            'precio' => '99999999.99', 'categoria' => str_repeat('Categoría ', 25), 'categoria_uri' => 'juguetes'],
        3 => ['id' => 3, 'producto' => 'Cuaderno', 'descripcion' => null, 'foto' => null,
            'precio' => '0.00', 'categoria' => 'Papelería', 'categoria_uri' => 'papeleria'],
    ];
    file_put_contents($temporal . '/productos.json', json_encode($productos, JSON_UNESCAPED_UNICODE));

    $logo = imagecreatetruecolor(120, 60);
    imagefill($logo, 0, 0, imagecolorallocate($logo, 0, 180, 0));
    imagepng($logo, $temporal . '/img/logo.png');
    imagedestroy($logo);
    $producto = imagecreatetruecolor(100, 200);
    imagefilledrectangle($producto, 0, 0, 99, 99, imagecolorallocate($producto, 230, 0, 0));
    imagefilledrectangle($producto, 0, 100, 99, 199, imagecolorallocate($producto, 0, 0, 230));
    imagejpeg($producto, $temporal . '/img/productos/prueba.jpg', 100);
    imagedestroy($producto);
    file_put_contents($temporal . '/img/productos/roto.jpg', 'contenido que no es una imagen');
    copy($temporal . '/img/productos/prueba.jpg', $temporal . '/fuera.jpg');
    symlink($temporal . '/fuera.jpg', $temporal . '/img/productos/enlace.jpg');

    $socket = stream_socket_server('tcp://127.0.0.1:0', $codigo, $mensaje);
    comprobar_imagen($socket !== false, 'Debe poder reservar un puerto de prueba');
    $direccion = stream_socket_get_name($socket, false);
    fclose($socket);
    $servidor = proc_open([PHP_BINARY, '-S', $direccion, '-t', $temporal],
        [0 => ['pipe', 'r'], 1 => ['file', $temporal . '/servidor.log', 'a'], 2 => ['file', $temporal . '/servidor.log', 'a']], $canales);
    comprobar_imagen(is_resource($servidor), 'Debe iniciar el servidor PHP temporal');
    $url = 'http://' . $direccion . '/imagen-social.php';
    for ($intento = 0; $intento < 50; $intento++) {
        $conexion = @stream_socket_client('tcp://' . $direccion, $codigo, $mensaje, 0.1);
        if ($conexion !== false) {
            fclose($conexion);
            break;
        }
        usleep(20000);
    }

    $respuesta = pedir_imagen($url . '?foto=prueba&v=123');
    $imagen = comprobar_jpeg($respuesta);
    comprobar_pixel($imagen, 100, 300, [255, 255, 255], 'El margen lateral debe ser blanco');
    comprobar_pixel($imagen, 600, 15, [230, 0, 0], 'Debe conservar la parte superior del producto');
    comprobar_pixel($imagen, 600, 615, [0, 0, 230], 'Debe conservar la parte inferior del producto');
    imagedestroy($imagen);

    $fallback = pedir_imagen($url);
    $imagen = comprobar_jpeg($fallback);
    comprobar_pixel($imagen, 600, 315, [0, 180, 0], 'Sin foto debe mostrar el logo');
    imagedestroy($imagen);
    foreach (['inexistente', '../fuera', 'prueba.jpg', "prueba\n", str_repeat('a', 256), 'enlace', 'roto'] as $foto) {
        $invalida = pedir_imagen($url . '?foto=' . rawurlencode($foto));
        comprobar_imagen($invalida['cuerpo'] === $fallback['cuerpo'], 'La foto inválida debe usar el logo: ' . json_encode($foto));
    }
    $array = pedir_imagen($url . '?foto%5B%5D=prueba');
    comprobar_imagen($array['cuerpo'] === $fallback['cuerpo'], 'Un parámetro foto de tipo array debe usar el logo');

    $head = pedir_imagen($url . '?foto=prueba', 'HEAD');
    comprobar_imagen($head['estado'] === 200 && $head['cuerpo'] === '', 'HEAD debe responder sin cuerpo');
    comprobar_imagen(($head['etag'] ?? '') === $respuesta['etag'], 'HEAD debe conservar el ETag de GET');
    comprobar_imagen(($head['content-length'] ?? '') === $respuesta['content-length'], 'HEAD debe informar el tamaño de GET');
    $cache = pedir_imagen($url . '?foto=prueba', 'GET', ['If-None-Match: W/' . $respuesta['etag']]);
    comprobar_imagen($cache['estado'] === 304 && $cache['cuerpo'] === '', 'Un ETag vigente debe responder 304');
    $post = pedir_imagen($url, 'POST');
    comprobar_imagen($post['estado'] === 405 && ($post['allow'] ?? '') === 'GET, HEAD', 'Debe rechazar otros métodos');

    foreach (['0', '-1', '01', '1.0', '1e2', '1 OR 1=1', '', '999999999999999999999'] as $idInvalido) {
        $invalida = pedir_imagen($url . '?producto=' . rawurlencode($idInvalido));
        comprobar_imagen($invalida['estado'] === 404 && ($invalida['cache-control'] ?? '') === 'no-store',
            'Un ID inválido debe responder 404 sin caché: ' . $idInvalido);
    }
    comprobar_imagen(pedir_imagen($url . '?producto%5B%5D=1')['estado'] === 404, 'Debe rechazar un ID de tipo array');
    comprobar_imagen(!file_exists($temporal . '/db-cargada'), 'El modo foto/logo y los IDs inválidos no deben cargar DB ni sesión');

    $tarjeta = pedir_imagen($url . '?producto=1');
    $imagen = comprobar_jpeg($tarjeta);
    comprobar_imagen(!isset($tarjeta['set-cookie']) && !isset($tarjeta['pragma']) && !isset($tarjeta['expires']),
        'La tarjeta debe liberar los encabezados de sesión');
    comprobar_pixel($imagen, 300, 40, [230, 0, 0], 'La tarjeta debe conservar la parte superior de la foto');
    comprobar_pixel($imagen, 300, 590, [0, 0, 230], 'La tarjeta debe conservar la parte inferior de la foto');
    comprobar_pixel($imagen, 610, 315, [241, 246, 249], 'El panel de texto debe tener fondo claro');
    comprobar_texto_imagen($imagen, 145, 285, 'Debe mostrar el título');
    comprobar_texto_imagen($imagen, 300, 400, 'Debe mostrar la descripción');
    comprobar_texto_imagen($imagen, 485, 535, 'Debe mostrar el precio');
    imagedestroy($imagen);
    $datosFalsos = pedir_imagen($url . '?producto=1&foto=roto&titulo=FALSO&precio=9999&descripcion=FALSA');
    comprobar_imagen($datosFalsos['cuerpo'] === $tarjeta['cuerpo'], 'Los valores de la tarjeta solo deben provenir de DB');
    $headProducto = pedir_imagen($url . '?producto=1', 'HEAD');
    comprobar_imagen($headProducto['estado'] === 200 && $headProducto['cuerpo'] === ''
        && ($headProducto['etag'] ?? '') === $tarjeta['etag'], 'HEAD debe conservar el ETag de la tarjeta');
    $cacheProducto = pedir_imagen($url . '?producto=1', 'GET', ['If-None-Match: ' . $tarjeta['etag']]);
    comprobar_imagen($cacheProducto['estado'] === 304 && $cacheProducto['cuerpo'] === '', 'La tarjeta debe admitir 304');

    $productos[1]['precio'] = '4600.00';
    file_put_contents($temporal . '/productos.json', json_encode($productos, JSON_UNESCAPED_UNICODE));
    $precioActual = pedir_imagen($url . '?producto=1');
    comprobar_imagen($precioActual['cuerpo'] !== $tarjeta['cuerpo'] && $precioActual['etag'] !== $tarjeta['etag'], 'Un precio actualizado debe cambiar la tarjeta y el ETag');
    foreach ([2, 3] as $idValido) {
        $imagen = comprobar_jpeg(pedir_imagen($url . '?producto=' . $idValido));
        comprobar_texto_imagen($imagen, 145, 285, 'El título largo o sin descripción debe seguir siendo visible');
        comprobar_texto_imagen($imagen, 300, 400, 'La descripción o su alternativa debe seguir siendo visible');
        comprobar_texto_imagen($imagen, 485, 535, 'El precio debe seguir siendo visible sin solaparse');
        comprobar_pixel($imagen, 1180, 315, [241, 246, 249], 'El texto debe respetar el margen derecho');
        imagedestroy($imagen);
    }
    $inexistente = pedir_imagen($url . '?producto=404');
    comprobar_imagen($inexistente['estado'] === 404 && ($inexistente['cache-control'] ?? '') === 'no-store', 'Un producto inexistente debe responder 404 sin caché');
    $dbCaida = pedir_imagen($url . '?producto=98');
    comprobar_imagen($dbCaida['estado'] === 503 && ($dbCaida['cache-control'] ?? '') === 'no-store', 'Un fallo de DB debe responder 503 sin precio inventado');

    unlink($temporal . '/img/logo.png');
    $imagen = comprobar_jpeg(pedir_imagen($url . '?foto=ausente'));
    comprobar_pixel($imagen, 600, 315, [255, 255, 255], 'Sin logo disponible debe responder un lienzo blanco válido');
    imagedestroy($imagen);
    fwrite(STDOUT, "OK: JPEG 1200×630, foto completa, tarjeta con datos actuales, texto largo, fallback, IDs, HEAD y caché pública sin sesión.\n");
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
