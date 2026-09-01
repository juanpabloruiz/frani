<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '');

function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function base_path(string $ruta = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($ruta, '/');

    if ($base === '') {
        return '/' . $path;
    }

    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function redireccionar(string $ruta = ''): void
{
    header('Location: ' . base_path($ruta));
    exit;
}

function numero($valor) {
    $valor = trim($valor);
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return is_numeric($valor) ? $valor : 0;
}

function subir_foto(array $archivo, string $directorio): ?string
{
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    $mime = mime_content_type($archivo['tmp_name']);
    if (!isset($tiposPermitidos[$mime])) {
        return null;
    }

    $tamanioMaximo = 5 * 1024 * 1024;
    if ($archivo['size'] > $tamanioMaximo) {
        return null;
    }

    if (!is_dir($directorio)) {
        mkdir($directorio, 0755, true);
    }

    $hash = date('Ymd_His') . '_' . bin2hex(random_bytes(4));
    $nombreBase = $hash;

    $origen = $archivo['tmp_name'];
    $anchoMax = 800;

    $info = getimagesize($origen);
    if ($info === false) {
        return null;
    }

    [$anchoOriginal, $altoOriginal] = $info;

    if ($anchoOriginal > $anchoMax) {
        $altoMax = (int) round($altoOriginal * ($anchoMax / $anchoOriginal));
    } else {
        $anchoMax = $anchoOriginal;
        $altoMax = $altoOriginal;
    }

    $imagen = match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($origen),
        'image/png'  => imagecreatefrompng($origen),
        'image/webp' => imagecreatefromwebp($origen),
        default      => null,
    };

    if ($imagen === null) {
        return null;
    }

    $redimensionada = imagecreatetruecolor($anchoMax, $altoMax);
    imagecopyresampled($redimensionada, $imagen, 0, 0, 0, 0, $anchoMax, $altoMax, $anchoOriginal, $altoOriginal);

    if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($redimensionada, false);
        imagesavealpha($redimensionada, true);
    }

    imagejpeg($redimensionada, $directorio . '/' . $nombreBase . '.jpg', 85);
    imagewebp($redimensionada, $directorio . '/' . $nombreBase . '.webp', 85);

    imagedestroy($imagen);
    imagedestroy($redimensionada);

    return $nombreBase;
}

function eliminar_fotos(string $nombreBase, string $directorio): void
{
    $extensiones = ['jpg', 'webp'];
    foreach ($extensiones as $ext) {
        $archivo = $directorio . '/' . $nombreBase . '.' . $ext;
        if (file_exists($archivo)) {
            unlink($archivo);
        }
    }
}

function respaldar_bd(): void
{
    $host = getenv('DB_HOST');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASSWORD');
    $name = getenv('DB_NAME');
    $port = (int) getenv('DB_PORT');

    $destino = getenv('BACKUP_DIR');
    if ($destino === false || $destino === '') {
        $destino = '/var/www/backup/respaldo.sql';
    }

    $cmd = sprintf(
        'mysqldump --single-transaction --routines -h %s -P %d -u %s -p%s %s > %s',
        escapeshellarg($host),
        $port,
        escapeshellarg($user),
        escapeshellarg($pass),
        escapeshellarg($name),
        escapeshellarg($destino)
    );

    @exec($cmd);
}

function moneda($valor): string
{
    return number_format((float) $valor, 2, ',', '.');
}

function numero_limpio($valor): string
{
    if ($valor === null || $valor === '') {
        return '';
    }
    $n = (float) $valor;
    if ($n == 0) {
        return '';
    }
    if (floor($n) == $n) {
        return number_format($n, 0, '.', '');
    }
    return number_format($n, 2, '.', '');
}
