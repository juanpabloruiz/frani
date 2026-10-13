<?php
declare(strict_types=1);

require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/facebook_api.php';

function facebook_activado(): bool
{
    return filter_var(getenv('FACEBOOK_AUTO_PUBLISH') ?: 'false', FILTER_VALIDATE_BOOLEAN);
}

/** Las credenciales se usan solamente en el proceso que llama a Meta. */
function facebook_configuracion(): array
{
    $config = [
        'page_id' => trim(getenv('FACEBOOK_PAGE_ID') ?: ''),
        'access_token' => trim(getenv('FACEBOOK_PAGE_ACCESS_TOKEN') ?: ''),
        'version' => trim(getenv('FACEBOOK_GRAPH_VERSION') ?: 'v26.0'),
    ];
    if (!preg_match('/\A[0-9]+\z/', $config['page_id']) || $config['access_token'] === ''
        || !preg_match('/\Av[0-9]+\.0\z/', $config['version'])
        || preg_match('/[\r\n]/', $config['access_token'])) {
        throw new RuntimeException('Completar FACEBOOK_PAGE_ID, FACEBOOK_PAGE_ACCESS_TOKEN y FACEBOOK_GRAPH_VERSION.');
    }
    return $config;
}

function facebook_producto_leer(mysqli $db, int $id, bool $bloquear = false): ?array
{
    $stmt = $db->prepare('SELECT p.*, c.nombre AS categoria, c.uri AS categoria_uri
        FROM productos p INNER JOIN categorias c ON c.id = p.id_categoria
        WHERE p.id = ?' . ($bloquear ? ' FOR UPDATE' : ''));
    $stmt->execute([$id]);
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $producto;
}

/** Compara datos que se muestran en el catálogo, sin incluir costo ni timestamps. */
function facebook_producto_contenido(array $producto): array
{
    return [
        'producto' => (string) $producto['producto'],
        'foto' => (string) ($producto['foto'] ?? ''),
        'descripcion' => (string) ($producto['descripcion'] ?? ''),
        'precio' => number_format((float) $producto['precio'], 2, '.', ''),
        'stock' => $producto['stock'] === null ? null : (int) $producto['stock'],
        'id_categoria' => (int) $producto['id_categoria'],
    ];
}

/** Debe llamarse dentro de la misma transacción que guarda el producto. */
function facebook_encolar_producto(mysqli $db, int $id, string $tipo, ?array $antes = null): bool
{
    if (!facebook_activado()) {
        return false;
    }
    if (!in_array($tipo, ['alta', 'edicion'], true)) {
        throw new InvalidArgumentException('Tipo de publicación no admitido.');
    }
    $pagina = trim(getenv('FACEBOOK_PAGE_ID') ?: '');
    if (!preg_match('/\A[0-9]+\z/', $pagina)) {
        throw new RuntimeException('Falta configurar FACEBOOK_PAGE_ID.');
    }
    $producto = facebook_producto_leer($db, $id);
    if ($producto === null) {
        return false;
    }
    if ($antes !== null && facebook_producto_contenido($antes) === facebook_producto_contenido($producto)) {
        return false;
    }
    if (empty($producto['uri']) || empty($producto['categoria_uri'])) {
        throw new RuntimeException('El producto necesita una URL pública antes de encolar la publicación.');
    }
    $nombre = seo_texto((string) $producto['producto']);
    $mensaje = ($tipo === 'alta' ? 'Nuevo en Frani' : 'Novedad en Frani') . "\n\n"
        . $nombre . "\nPrecio: $ " . moneda($producto['precio']) . ' ARS';
    $descripcion = seo_texto((string) ($producto['descripcion'] ?? ''), 1500);
    if ($descripcion !== '') {
        $mensaje .= "\n\n" . $descripcion;
    }
    if ($producto['stock'] !== null) {
        $mensaje .= "\n\n" . ((int) $producto['stock'] > 0 ? 'Disponible.' : 'Consultá por la reposición.');
    }
    $contenido = json_encode([
        'message' => $mensaje,
        'link' => sitio_url(ltrim(producto_path($producto['uri'], $producto['categoria_uri']), '/')),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $stmt = $db->prepare('INSERT INTO facebook_publicaciones (producto_id, pagina_id, tipo, contenido)
        VALUES (?, ?, ?, ?)');
    $stmt->execute([$id, $pagina, $tipo, $contenido]);
    $stmt->close();
    return true;
}

/**
 * Un lock de conexión cubre también la llamada HTTP. Un envío interrumpido es
 * incierto: Facebook puede haber creado el post aunque no recibamos su respuesta.
 */
function facebook_procesar_cola(mysqli $db, array $config, int $limite = 10, ?callable $transport = null): array
{
    $lock = $db->query("SELECT GET_LOCK('frani_facebook_publicaciones', 0)")->fetch_row()[0];
    if ((int) $lock !== 1) {
        return [];
    }
    $resultados = [];
    try {
        $stmt = $db->prepare('UPDATE facebook_publicaciones
            SET estado = "incierto", ultimo_error = "El proceso se interrumpió durante el envío; revisar la página."
            WHERE estado = "enviando" AND pagina_id = ?');
        $stmt->execute([$config['page_id']]);
        $stmt->close();
        for ($n = 0; $n < max(0, $limite); $n++) {
            $db->begin_transaction();
            try {
                $stmt = $db->prepare('SELECT actual.* FROM facebook_publicaciones actual
                    WHERE actual.pagina_id = ? AND actual.estado IN ("pendiente", "reintentar")
                        AND actual.disponible <= CURRENT_TIMESTAMP
                        AND NOT EXISTS (
                            SELECT 1 FROM facebook_publicaciones anterior
                            WHERE anterior.producto_id = actual.producto_id
                                AND anterior.pagina_id = actual.pagina_id AND anterior.id < actual.id
                                AND anterior.estado IN ("pendiente", "reintentar", "enviando")
                        )
                    ORDER BY actual.id LIMIT 1 FOR UPDATE');
                $stmt->execute([$config['page_id']]);
                $evento = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($evento === null) {
                    $db->commit();
                    break;
                }
                $stmt = $db->prepare('UPDATE facebook_publicaciones
                    SET estado = "enviando", intentos = intentos + 1 WHERE id = ?');
                $stmt->execute([$evento['id']]);
                $stmt->close();
                $db->commit();
            } catch (Throwable $error) {
                $db->rollback();
                throw $error;
            }

            if (facebook_producto_leer($db, (int) $evento['producto_id']) === null) {
                $resultado = ['estado' => 'omitido', 'post_id' => null, 'error' => 'El producto ya no existe.'];
            } else {
                try {
                    $payload = json_decode($evento['contenido'], true, 512, JSON_THROW_ON_ERROR);
                    if (!is_array($payload) || !is_string($payload['message'] ?? null)
                        || !is_string($payload['link'] ?? null)) {
                        throw new UnexpectedValueException();
                    }
                    $resultado = facebook_api_publicar($config, $payload, $transport);
                } catch (JsonException | UnexpectedValueException $error) {
                    $resultado = ['estado' => 'fallido', 'post_id' => null, 'error' => 'La publicación pendiente tiene datos inválidos.'];
                }
            }
            $intentos = (int) $evento['intentos'] + 1;
            if ($resultado['estado'] === 'reintentar' && $intentos >= 5) {
                $resultado['estado'] = 'fallido';
                $resultado['error'] = 'Meta rechazó la publicación luego de cinco intentos.';
            }
            $espera = min(3600, 30 * (2 ** min(7, max(0, $intentos - 1))));
            $stmt = $db->prepare('UPDATE facebook_publicaciones
                SET estado = ?, publicacion_id = ?, ultimo_error = ?,
                    disponible = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL ? SECOND)
                WHERE id = ?');
            $stmt->execute([$resultado['estado'], $resultado['post_id'], $resultado['error'], $espera, $evento['id']]);
            $stmt->close();
            $resultados[] = ['id' => (int) $evento['id'], 'estado' => $resultado['estado']];
        }
    } finally {
        $db->query("SELECT RELEASE_LOCK('frani_facebook_publicaciones')");
    }
    return $resultados;
}
