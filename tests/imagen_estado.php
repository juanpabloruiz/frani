<?php
declare(strict_types=1);

// docker compose exec -T web php /var/www/backup/tests/imagen_estado.php
// Prueba el endpoint real con datos y archivos temporales, sin modificar la BD.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

function comprobar_estado(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function pedir_estado(string $url, string $metodo = 'GET', array $cabeceras = []): array
{
    $contexto = stream_context_create(['http' => [
        'method' => $metodo, 'header' => implode("\r\n", $cabeceras), 'ignore_errors' => true, 'timeout' => 8,
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

function comprobar_jpeg_estado(array $respuesta): GdImage
{
    comprobar_estado($respuesta['estado'] === 200, 'La imagen debe responder 200');
    comprobar_estado(($respuesta['content-type'] ?? '') === 'image/jpeg', 'Debe enviar image/jpeg');
    comprobar_estado(($respuesta['cache-control'] ?? '') === 'public, max-age=86400', 'Debe permitir caché pública');
    comprobar_estado(($respuesta['x-robots-tag'] ?? '') === 'noindex', 'Debe llevar noindex');
    comprobar_estado(!isset($respuesta['set-cookie']) && !isset($respuesta['pragma']) && !isset($respuesta['expires']),
        'Debe liberar la sesión y quitar sus encabezados');
    $datos = getimagesizefromstring($respuesta['cuerpo']);
    comprobar_estado($datos !== false && $datos[0] === 1080 && $datos[1] === 1920 && $datos[2] === IMAGETYPE_JPEG,
        'Debe ser JPEG de 1080×1920, proporción 9:16');
    comprobar_estado((int) ($respuesta['content-length'] ?? 0) === strlen($respuesta['cuerpo']), 'Content-Length debe coincidir');
    return imagecreatefromstring($respuesta['cuerpo']);
}

function pixel_estado(GdImage $imagen, int $x, int $y, array $esperado, string $mensaje): void
{
    $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, $y));
    foreach (['red', 'green', 'blue'] as $indice => $canal) {
        comprobar_estado(abs($color[$canal] - $esperado[$indice]) < 18, $mensaje);
    }
}

function tinta_estado(GdImage $imagen, int $arriba, int $abajo, string $mensaje): void
{
    $claros = 0;
    for ($y = $arriba; $y <= $abajo; $y += 2) {
        for ($x = 60; $x <= 1020; $x += 2) {
            $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, $y));
            if ($color['red'] + $color['green'] + $color['blue'] > 650) {
                $claros++;
            }
        }
    }
    comprobar_estado($claros > 100, $mensaje);
}

$temporal = sys_get_temp_dir() . '/frani-imagen-estado-' . bin2hex(random_bytes(8));
$servidor = null;
$canales = [];
$salida = 0;
try {
    mkdir($temporal . '/img/social', 0700, true);
    mkdir($temporal . '/img/productos', 0700, true);
    mkdir($temporal . '/fonts', 0700);
    foreach (['imagen-estado.php', 'seo.php', 'funciones.php'] as $archivo) {
        comprobar_estado(copy(__DIR__ . '/../src/' . $archivo, $temporal . '/' . $archivo), 'Debe usar el endpoint y helpers reales');
    }
    foreach (['Lato-Regular.ttf', 'Lato-Bold.ttf'] as $fuente) {
        comprobar_estado(copy(__DIR__ . '/../src/fonts/' . $fuente, $temporal . '/fonts/' . $fuente), 'Debe copiar la tipografía real');
    }
    $logo = imagecreatetruecolor(400, 220);
    imagefill($logo, 0, 0, imagecolorallocate($logo, 20, 100, 170));
    imagepng($logo, $temporal . '/img/social/frani-logo.png');
    imagedestroy($logo);
    // Cuadrantes distintos permiten comprobar foto completa y proporción.
    $foto = imagecreatetruecolor(600, 600);
    imagefilledrectangle($foto, 0, 0, 299, 299, imagecolorallocate($foto, 200, 20, 20));
    imagefilledrectangle($foto, 300, 0, 599, 299, imagecolorallocate($foto, 20, 170, 20));
    imagefilledrectangle($foto, 0, 300, 299, 599, imagecolorallocate($foto, 20, 20, 200));
    imagefilledrectangle($foto, 300, 300, 599, 599, imagecolorallocate($foto, 210, 180, 20));
    imagejpeg($foto, $temporal . '/img/productos/fixture.jpg', 100);
    imagedestroy($foto);
    file_put_contents($temporal . '/conexion.php', <<<'PHP'
<?php
require_once __DIR__ . '/funciones.php';
putenv('APP_DOMAIN=frani.ar');
function conexion() {
    file_put_contents(__DIR__ . '/db-consultada', '1');
    return new class {
        public function prepare(string $sql) {
            if (!str_contains($sql, 'SELECT p.id, p.producto, p.descripcion, p.foto, p.precio, p.uri')
                || !str_contains($sql, 'WHERE p.id = ? LIMIT 1')) {
                throw new RuntimeException('Debe consultar el ID con sentencia preparada');
            }
            return new class {
                private int $id;
                public function bind_param(string $tipo, int &$id): void {
                    if ($tipo !== 'i') { throw new RuntimeException('El ID debe ser entero'); }
                    $this->id = $id;
                }
                public function execute(): void {
                    if (session_status() === PHP_SESSION_ACTIVE) { throw new RuntimeException('Debe liberar la sesión antes de consultar'); }
                    if ($this->id === 98) { throw new RuntimeException('Error DB simulado'); }
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
    $base = ['id' => 1, 'producto' => 'Masa mágica por 12', 'descripcion' => 'Creá, jugá y divertite. Colores para compartir.',
        'foto' => 'fixture', 'precio' => '12500.50', 'uri' => 'masa-magica', 'categoria' => 'Juguetes', 'categoria_uri' => 'juguetes'];
    $productos = [
        1 => $base,
        2 => array_replace($base, ['id' => 2, 'producto' => str_repeat('Ñandú', 45), 'descripcion' => str_repeat('Árbol, lápiz y juguetes. ', 30)]),
        3 => array_replace($base, ['id' => 3, 'foto' => '../fixture']),
        4 => array_replace($base, ['id' => 4, 'uri' => null]),
        5 => array_replace($base, ['id' => 5, 'categoria_uri' => 'panel']),
        6 => array_replace($base, ['id' => 6, 'uri' => '../privado']),
        7 => array_replace($base, ['id' => 7, 'categoria_uri' => 'JUGUETES']),
        8 => array_replace($base, ['id' => 8, 'precio' => 'no es precio']),
        9 => array_replace($base, ['id' => 9, 'foto' => 'simbolo']),
        10 => array_replace($base, ['id' => 10, 'foto' => null, 'descripcion' => '']),
        11 => array_replace($base, ['id' => 11, 'uri' => str_repeat('a', 201)]),
        12 => array_replace($base, ['id' => 12, 'precio' => '1.234567e+200']),
    ];
    file_put_contents($temporal . '/productos.json', json_encode($productos, JSON_UNESCAPED_UNICODE));
    symlink($temporal . '/img/productos/fixture.jpg', $temporal . '/img/productos/simbolo.jpg');
    $socket = stream_socket_server('tcp://127.0.0.1:0', $codigo, $mensaje);
    comprobar_estado($socket !== false, 'Debe reservar un puerto de prueba');
    $direccion = stream_socket_get_name($socket, false);
    fclose($socket);
    $servidor = proc_open([PHP_BINARY, '-S', $direccion, '-t', $temporal],
        [0 => ['pipe', 'r'], 1 => ['file', $temporal . '/servidor.log', 'a'], 2 => ['file', $temporal . '/servidor.log', 'a']], $canales);
    comprobar_estado(is_resource($servidor), 'Debe iniciar servidor temporal');
    for ($intento = 0; $intento < 50; $intento++) {
        $conexion = @stream_socket_client('tcp://' . $direccion, $codigo, $mensaje, 0.1);
        if ($conexion !== false) { fclose($conexion); break; }
        usleep(20000);
    }
    $url = 'http://' . $direccion . '/imagen-estado.php';
    foreach (['', '?producto=0', '?producto=-1', '?producto=01', '?producto=1.0', '?producto=1e2',
        '?producto=99999999999999999999', '?producto%5B%5D=1', '?producto=1&descargar=0', '?producto=1&descargar%5B%5D=1'] as $consulta) {
        $respuesta = pedir_estado($url . $consulta);
        comprobar_estado($respuesta['estado'] === 404 && ($respuesta['cache-control'] ?? '') === 'no-store', 'Debe rechazar parámetros inválidos sin caché');
    }
    comprobar_estado(!file_exists($temporal . '/db-consultada'), 'Parámetros inválidos no deben consultar DB');
    $principal = pedir_estado($url . '?producto=1');
    $imagen = comprobar_jpeg_estado($principal);
    pixel_estado($imagen, 15, 290, [200, 20, 20], 'Debe conservar esquina superior izquierda a todo el ancho');
    pixel_estado($imagen, 1065, 290, [20, 170, 20], 'Debe conservar esquina superior derecha a todo el ancho');
    pixel_estado($imagen, 15, 1340, [20, 20, 200], 'Debe conservar esquina inferior izquierda sin recortar');
    pixel_estado($imagen, 1065, 1340, [210, 180, 20], 'Debe conservar esquina inferior derecha sin recortar');
    tinta_estado($imagen, 132, 190, 'Debe incluir Frani grande en la cabecera');
    tinta_estado($imagen, 1470, 1545, 'Debe incluir nombre grande y blanco');
    tinta_estado($imagen, 1660, 1735, 'Debe incluir precio grande y blanco');
    pixel_estado($imagen, 75, 130, [20, 32, 40], 'La marca debe llevar una base oscura legible');
    pixel_estado($imagen, 540, 1840, [20, 32, 40], 'Debe reservar margen inferior oscuro para controles');
    $colorFondo = imagecolorsforindex($imagen, imagecolorat($imagen, 20, 30));
    comprobar_estado(min($colorFondo['red'], $colorFondo['green'], $colorFondo['blue']) < 200,
        'El fondo debe derivar de la foto, evitando un gran bloque blanco');
    imagedestroy($imagen);
    comprobar_estado(pedir_estado($url . '?producto=1&foto=otra&precio=1&titulo=FALSO&descripcion=FALSA')['cuerpo'] === $principal['cuerpo'],
        'Debe ignorar contenido inventado vía query');
    $descarga = pedir_estado($url . '?producto=1&descargar=1');
    comprobar_estado($descarga['cuerpo'] === $principal['cuerpo']
        && ($descarga['content-disposition'] ?? '') === 'attachment; filename="frani-estado-1.jpg"', 'Debe descargar el mismo JPEG con nombre fijo');
    $head = pedir_estado($url . '?producto=1&descargar=1', 'HEAD');
    comprobar_estado($head['estado'] === 200 && $head['cuerpo'] === '' && $head['etag'] === $principal['etag']
        && $head['content-length'] === $principal['content-length'], 'HEAD debe conservar tamaño y ETag sin cuerpo');
    foreach (['W/' . $principal['etag'], '"otro", ' . $principal['etag'], '*'] as $etag) {
        $cache = pedir_estado($url . '?producto=1', 'GET', ['If-None-Match: ' . $etag]);
        comprobar_estado($cache['estado'] === 304 && $cache['cuerpo'] === '', 'Debe responder 304 al validar ETag');
    }
    $larga = comprobar_jpeg_estado(pedir_estado($url . '?producto=2'));
    tinta_estado($larga, 1470, 1545, 'Texto UTF-8 largo debe ocupar primera línea');
    tinta_estado($larga, 1555, 1627, 'Texto largo debe ocupar segunda línea');
    pixel_estado($larga, 30, 1600, [20, 32, 40], 'Texto largo debe respetar margen izquierdo');
    pixel_estado($larga, 1050, 1600, [20, 32, 40], 'Texto largo debe respetar margen derecho');
    pixel_estado($larga, 200, 1765, [20, 32, 40], 'No debe añadir descripción o dominio pequeños debajo del precio');
    imagedestroy($larga);
    foreach ([3, 9, 10] as $idSinFoto) {
        $sinFoto = comprobar_jpeg_estado(pedir_estado($url . '?producto=' . $idSinFoto));
        pixel_estado($sinFoto, 540, 750, [20, 100, 170], 'Foto ausente/insegura debe usar logo local');
        imagedestroy($sinFoto);
    }
    $enorme = comprobar_jpeg_estado(pedir_estado($url . '?producto=12'));
    tinta_estado($enorme, 1680, 1735, 'Incluso un precio extremo debe tener texto legible en lugar de reducirse a un punto');
    pixel_estado($enorme, 1050, 1710, [20, 32, 40], 'El precio extremo debe respetar el margen derecho');
    imagedestroy($enorme);
    foreach ([4, 5, 6, 7, 11, 404] as $idNoPublico) {
        $respuesta = pedir_estado($url . '?producto=' . $idNoPublico);
        comprobar_estado($respuesta['estado'] === 404 && ($respuesta['cache-control'] ?? '') === 'no-store', 'Producto no publicado debe responder 404 sin caché');
    }
    foreach ([8, 98] as $idError) {
        $respuesta = pedir_estado($url . '?producto=' . $idError);
        comprobar_estado($respuesta['estado'] === 503 && ($respuesta['cache-control'] ?? '') === 'no-store'
            && !isset($respuesta['set-cookie']), 'Error de datos o DB debe responder 503 sin sesión ni caché');
    }
    $productos[1]['precio'] = '15999.00';
    file_put_contents($temporal . '/productos.json', json_encode($productos, JSON_UNESCAPED_UNICODE));
    $precioNuevo = pedir_estado($url . '?producto=1');
    comprobar_estado($precioNuevo['cuerpo'] !== $principal['cuerpo'] && $precioNuevo['etag'] !== $principal['etag'], 'Un precio actualizado debe cambiar imagen y ETag');
    $productos[1]['producto'] = 'Nueva presentación de juguetes';
    file_put_contents($temporal . '/productos.json', json_encode($productos, JSON_UNESCAPED_UNICODE));
    comprobar_estado(pedir_estado($url . '?producto=1')['cuerpo'] !== $precioNuevo['cuerpo'], 'Cambiar nombre debe cambiar imagen');
    unlink($temporal . '/img/productos/fixture.jpg');
    comprobar_jpeg_estado(pedir_estado($url . '?producto=1'));
    $post = pedir_estado($url . '?producto=1', 'POST');
    comprobar_estado($post['estado'] === 405 && ($post['allow'] ?? '') === 'GET, HEAD', 'Debe rechazar otros métodos');
    unlink($temporal . '/img/social/frani-logo.png');
    $sinLogo = pedir_estado($url . '?producto=1');
    comprobar_estado($sinLogo['estado'] === 503 && ($sinLogo['cache-control'] ?? '') === 'no-store', 'Logo ausente debe fallar sin caché');
    fwrite(STDOUT, "OK: JPEG 1080×1920, foto grande completa, fondo desenfocado, Frani/nombre/precio grandes, texto UTF-8, descarga, HEAD/ETag, sin sesión y errores públicos.\n");
} catch (Throwable $error) {
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . "\n");
    $salida = 1;
} finally {
    if (is_resource($servidor)) {
        proc_terminate($servidor);
        foreach ($canales as $canal) { fclose($canal); }
        proc_close($servidor);
    }
    if (is_dir($temporal)) {
        $archivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($temporal, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($archivos as $archivo) {
            if ($archivo->isDir() && !$archivo->isLink()) { rmdir($archivo->getPathname()); }
            else { unlink($archivo->getPathname()); }
        }
        rmdir($temporal);
    }
}
exit($salida);
