<?php
declare(strict_types=1);

// Sesión persistente: no expira ni por inactividad, ni al cerrar el navegador,
// ni al reiniciar el sistema. Solo se cierra con el botón Salir del panel.
if (session_status() === PHP_SESSION_NONE) {
    $vidaSesion = 10 * 365 * 24 * 3600; // 10 años (~sin expiración)
    ini_set('session.gc_maxlifetime', (string) $vidaSesion);
    session_set_cookie_params($vidaSesion, '/', '', false, true);
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

function producto_path(string $uri, string $categoriaUri): string
{
    return base_path(rawurlencode($categoriaUri) . '/' . rawurlencode($uri));
}

function categoria_path(string $uri): string
{
    return base_path(rawurlencode($uri));
}

function sitio_url(string $ruta = ''): string
{
    $dominio = getenv('APP_DOMAIN') ?: 'frani.ar';
    return 'https://' . $dominio . base_path($ruta);
}

/** Convierte un título en un segmento de URL ASCII, de hasta 200 caracteres. */
function generar_uri(string $titulo, string $fallback = 'producto'): string
{
    $titulo = strtr($titulo, [
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Ã' => 'A', 'Å' => 'A',
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a', 'å' => 'a',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Õ' => 'O',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ñ' => 'N', 'ñ' => 'n', 'Ç' => 'C', 'ç' => 'c',
        'Æ' => 'AE', 'æ' => 'ae', 'Œ' => 'OE', 'œ' => 'oe', 'ß' => 'ss',
    ]);
    // También admite acentos escritos como caracteres Unicode combinados.
    $titulo = preg_replace('/\p{Mn}+/u', '', $titulo) ?? $titulo;
    // Evita transliteraciones de emoji como «😀» a «:D» que inventan letras.
    $titulo = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $titulo) ?? $titulo;
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $titulo);
    if ($ascii !== false) {
        $titulo = $ascii;
    }

    $uri = preg_replace('/[^a-z0-9]+/', '-', strtolower($titulo));
    $uri = trim($uri, '-');
    $uri = rtrim(substr($uri, 0, 200), '-');

    return $uri !== '' ? $uri : $fallback;
}

/**
 * Asigna una URI solamente si falta; el índice UNIQUE resuelve incluso altas
 * simultáneas con el mismo título. No altera la fecha ni las URI ya publicadas.
 */
function asignar_uri_producto(mysqli $db, int $idProducto): string
{
    return asignar_uri_registro($db, 'productos', $idProducto);
}

function uri_categoria_reservada(string $uri): bool
{
    return in_array($uri, [
        'panel', 'productos', 'producto', 'categoria', 'test', 'img', 'css', 'js',
        'fontawesome', 'index', 'api', 'admin', 'robots', 'sitemap', 'migrations',
        'assets', 'cabecera', 'conexion', 'funciones', 'menu', 'pie',
    ], true);
}

function asignar_uri_categoria(mysqli $db, int $idCategoria): string
{
    return asignar_uri_registro($db, 'categorias', $idCategoria);
}

/** Comparte la asignación entre las dos tablas permitidas. */
function asignar_uri_registro(mysqli $db, string $tabla, int $id): string
{
    $campoTitulo = match ($tabla) {
        'productos' => 'producto',
        'categorias' => 'nombre',
        default => throw new InvalidArgumentException('Tabla de URI inválida.'),
    };
    $consulta = $db->prepare("SELECT {$campoTitulo} AS titulo, uri FROM {$tabla} WHERE id = ?");
    $consulta->bind_param('i', $id);
    $consulta->execute();
    $registro = $consulta->get_result()->fetch_assoc();
    $consulta->close();

    if ($registro === null) {
        throw new RuntimeException('Registro no encontrado al generar su URI.');
    }
    if ($registro['uri'] !== null && $registro['uri'] !== '') {
        return $registro['uri'];
    }

    $base = generar_uri($registro['titulo'], $tabla === 'categorias' ? 'categoria' : 'producto');
    $stmt = $db->prepare(
        "UPDATE {$tabla} SET uri = ?, modificado = modificado
         WHERE id = ? AND (uri IS NULL OR uri = '')"
    );
    $uri = $base;
    $stmt->bind_param('si', $uri, $id);

    try {
        for ($numero = 1; ; $numero++) {
            $sufijo = $numero === 1 ? '' : '-' . $numero;
            $uri = rtrim(substr($base, 0, 200 - strlen($sufijo)), '-') . $sufijo;
            if ($tabla === 'categorias' && uri_categoria_reservada($uri)) {
                continue;
            }
            try {
                $stmt->execute();
            } catch (mysqli_sql_exception $error) {
                if ($error->getCode() === 1062) {
                    continue;
                }
                throw $error;
            }

            if ($stmt->affected_rows > 0) {
                return $uri;
            }
            // Otra petición pudo completar el mismo producto mientras tanto.
            // Lectura actual: REPEATABLE READ puede conservar un snapshot con NULL.
            $consulta = $db->prepare("SELECT uri FROM {$tabla} WHERE id = ? FOR UPDATE");
            $consulta->bind_param('i', $id);
            $consulta->execute();
            $actual = $consulta->get_result()->fetch_assoc();
            $consulta->close();
            if ($actual === null || empty($actual['uri'])) {
                throw new RuntimeException('No se pudo asignar la URI del registro.');
            }
            return $actual['uri'];
        }
    } finally {
        $stmt->close();
    }
}

/** Completa todos los títulos existentes sin cambiar sus fechas o enlaces. */
function completar_uri_productos(mysqli $db): int
{
    $pendientes = $db->query("SELECT id FROM productos WHERE uri IS NULL OR uri = '' ORDER BY id");
    $cantidad = 0;
    while ($producto = $pendientes->fetch_assoc()) {
        asignar_uri_producto($db, (int) $producto['id']);
        $cantidad++;
    }
    $pendientes->free();
    return $cantidad;
}

function completar_uri_categorias(mysqli $db): int
{
    $pendientes = $db->query("SELECT id FROM categorias WHERE uri IS NULL OR uri = '' ORDER BY id");
    $cantidad = 0;
    while ($categoria = $pendientes->fetch_assoc()) {
        asignar_uri_categoria($db, (int) $categoria['id']);
        $cantidad++;
    }
    $pendientes->free();
    return $cantidad;
}

/** Prioriza la misma categoría y completa hasta tres sugerencias disponibles. */
function productos_relacionados(mysqli $db, int $idProducto, int $idCategoria): array
{
    $stmt = $db->prepare(
        "SELECT p.id, p.producto, p.uri, p.foto, p.precio, c.uri AS categoria_uri
         FROM productos p
         INNER JOIN categorias c ON c.id = p.id_categoria
         WHERE p.id <> ? AND p.uri IS NOT NULL AND p.uri <> ''
             AND c.uri IS NOT NULL AND c.uri <> ''
         ORDER BY (p.id_categoria = ?) DESC,
             GREATEST(COALESCE(p.modificado, p.agregado), p.agregado) DESC, p.id DESC
         LIMIT 3"
    );
    $stmt->bind_param('ii', $idProducto, $idCategoria);
    $stmt->execute();
    $relacionados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $relacionados;
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

/**
 * Muestra un importe en la tabla. Si el campo está vacío (NULL) o vale 0,
 * devuelve cadena vacía para que nunca aparezca un "0,00" engañoso.
 */
function mostrar_monto($valor): string
{
    if ($valor === null || $valor === '') {
        return '';
    }

    $monto = (float) $valor;

    return $monto == 0.0 ? '' : moneda($monto);
}

/**
 * Convierte un monto/envío de formulario a float, o NULL si el campo quedó
 * vacío (o vale 0). Así la base guarda el campo vacío en lugar de 0.00.
 */
function monto_post($valor): ?float
{
    if (!is_scalar($valor)) {
        return null;
    }

    $valor = trim((string) $valor);

    if ($valor === '') {
        return null;
    }

    $monto = (float) $valor;

    return $monto == 0.0 ? null : $monto;
}

/**
 * Suma varios montos del mismo campo (por ejemplo las dos líneas de pago).
 * Si ninguno tiene valor, o la suma da 0, devuelve NULL.
 */
function sumar_montos(array $valores): ?float
{
    $suma = 0.0;

    foreach ($valores as $valor) {
        $monto = monto_post($valor);
        if ($monto !== null) {
            $suma += $monto;
        }
    }

    return $suma == 0.0 ? null : round($suma, 2);
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
