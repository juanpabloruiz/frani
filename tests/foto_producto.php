<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/foto_producto.php
// Usa imágenes sintéticas y un directorio temporal; no modifica el catálogo.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

function comprobar_foto(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

/** Simula el JPEG que exporta el editor después de recortar una foto más grande. */
function crear_recorte_producto(string $ruta, int $ancho, int $alto): void
{
    $original = imagecreatetruecolor(1000, 1000);
    imagefill($original, 0, 0, imagecolorallocate($original, 255, 255, 255));
    imagefilledrectangle($original, 100, 120, 100 + $ancho - 1, 120 + $alto - 1,
        imagecolorallocate($original, 0, 180, 0));
    imagefilledrectangle($original, 100, 120, 100 + intdiv($ancho, 2) - 1, 120 + $alto - 1,
        imagecolorallocate($original, 220, 0, 0));
    $recorte = imagecrop($original, ['x' => 100, 'y' => 120, 'width' => $ancho, 'height' => $alto]);
    comprobar_foto($recorte !== false, 'Debe crear el recorte sintético');
    comprobar_foto(imagejpeg($recorte, $ruta, 95), 'Debe exportar el recorte como JPEG');
    unset($recorte);
    unset($original);
}

function archivo_foto_producto(string $ruta, int $error = UPLOAD_ERR_OK): array
{
    return [
        'name' => basename($ruta),
        'tmp_name' => $ruta,
        // No se confía en este MIME declarado: subir_foto debe inspeccionar el archivo.
        'type' => 'image/jpeg',
        'size' => filesize($ruta),
        'error' => $error,
    ];
}

function comprobar_salida_producto(string $ruta, int $ancho, int $alto, int $tipo): void
{
    comprobar_foto(is_file($ruta), 'Debe guardar la variante ' . basename($ruta));
    $info = getimagesize($ruta);
    comprobar_foto($info !== false && $info[0] === $ancho && $info[1] === $alto && $info[2] === $tipo,
        'La variante debe conservar las dimensiones, la proporción y el formato del recorte');
    $imagen = imagecreatefromstring(file_get_contents($ruta));
    comprobar_foto($imagen !== false, 'La variante guardada debe poder decodificarse');
    foreach ([[intdiv($ancho, 4), [220, 0, 0]], [intdiv(3 * $ancho, 4), [0, 180, 0]]] as [$x, $esperado]) {
        $color = imagecolorsforindex($imagen, imagecolorat($imagen, $x, intdiv($alto, 2)));
        foreach (['red', 'green', 'blue'] as $indice => $canal) {
            comprobar_foto(abs($color[$canal] - $esperado[$indice]) < 20,
                'La variante debe mantener el contenido del recorte sin márgenes añadidos');
        }
    }
    unset($imagen);
}

$temporal = sys_get_temp_dir() . '/frani-foto-producto-' . bin2hex(random_bytes(8));
$salida = 0;
try {
    comprobar_foto(mkdir($temporal, 0700), 'Debe crear el directorio temporal');
    // La sesión inicializada por funciones.php también queda aislada y se limpia.
    session_save_path($temporal);
    require_once __DIR__ . '/../src/funciones.php';

    $destino = $temporal . '/productos';
    $nombres = [];
    foreach (['rectangular' => [720, 480], 'cuadrado' => [512, 512]] as $forma => [$ancho, $alto]) {
        $ruta = $temporal . '/' . $forma . '.jpg';
        crear_recorte_producto($ruta, $ancho, $alto);
        $nombre = subir_foto(archivo_foto_producto($ruta), $destino);
        comprobar_foto(is_string($nombre) && preg_match('/^\d{8}_\d{6}_[a-f0-9]{8}$/D', $nombre) === 1,
            'El recorte debe guardarse con un nombre generado por el servidor');
        comprobar_foto(!in_array($nombre, $nombres, true), 'Cada recorte debe recibir un nombre distinto');
        $nombres[] = $nombre;
        comprobar_salida_producto($destino . '/' . $nombre . '.jpg', $ancho, $alto, IMAGETYPE_JPEG);
        comprobar_salida_producto($destino . '/' . $nombre . '.webp', $ancho, $alto, IMAGETYPE_WEBP);
    }

    $archivosAntes = glob($destino . '/*');
    $falsoJpeg = $temporal . '/falso.jpg';
    file_put_contents($falsoJpeg, 'Esto no es una imagen aunque la extensión y el MIME declarado sean JPEG.');
    comprobar_foto(subir_foto(archivo_foto_producto($falsoJpeg), $destino) === null,
        'Debe rechazar archivos cuyo contenido no sea un MIME de imagen permitido');

    $enorme = $temporal . '/demasiado-grande.jpg';
    copy($temporal . '/rectangular.jpg', $enorme);
    $archivo = fopen($enorme, 'ab');
    comprobar_foto($archivo !== false, 'Debe abrir el JPEG grande de prueba');
    fwrite($archivo, str_repeat("\0", 5 * 1024 * 1024));
    fclose($archivo);
    clearstatcache(true, $enorme);
    comprobar_foto(filesize($enorme) > 5 * 1024 * 1024 && mime_content_type($enorme) === 'image/jpeg',
        'La prueba del límite debe usar un JPEG de más de 5 MiB');
    comprobar_foto(subir_foto(archivo_foto_producto($enorme), $destino) === null,
        'Debe rechazar fotos que superen los 5 MiB');
    comprobar_foto(subir_foto(archivo_foto_producto($temporal . '/cuadrado.jpg', UPLOAD_ERR_PARTIAL), $destino) === null,
        'Debe rechazar una subida incompleta');
    comprobar_foto(glob($destino . '/*') === $archivosAntes, 'Los archivos rechazados no deben crear variantes');

    foreach ($nombres as $nombre) {
        eliminar_fotos($nombre, $destino);
        comprobar_foto(!file_exists($destino . '/' . $nombre . '.jpg') && !file_exists($destino . '/' . $nombre . '.webp'),
            'La limpieza debe eliminar las dos variantes de cada foto');
    }
    comprobar_foto(glob($destino . '/*') === [], 'No deben quedar variantes después de eliminarlas');
    fwrite(STDOUT, "OK: recortes JPEG rectangular y cuadrado, dimensiones y contenido JPG/WebP, MIME real, límite de 5 MiB, subida incompleta y limpieza.\n");
} catch (Throwable $error) {
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . "\n");
    $salida = 1;
} finally {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    if (is_dir($temporal)) {
        $archivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($temporal, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($archivos as $archivo) {
            if ($archivo->isDir()) {
                rmdir($archivo->getPathname());
            } else {
                unlink($archivo->getPathname());
            }
        }
        rmdir($temporal);
    }
}
exit($salida);
