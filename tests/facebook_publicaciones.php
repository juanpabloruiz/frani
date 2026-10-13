<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/facebook_publicaciones.php
// Las tres tablas temporales ocultan las reales únicamente en esta conexión.
// Todos los POST de Facebook se sustituyen por respuestas locales: no publica
// contenido, no usa tokens reales y no modifica productos ni la cola del sitio.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../src/conexion.php';
require_once __DIR__ . '/../src/facebook.php';

function verificar_facebook(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function igual_facebook($esperado, $actual, string $mensaje): void
{
    verificar_facebook(
        $esperado === $actual,
        $mensaje . ': esperado ' . var_export($esperado, true)
            . ', obtenido ' . var_export($actual, true)
    );
}

function producto_fixture_facebook(mysqli $db, string $nombre = 'Vaso Stitch & sorbete'): int
{
    $stmt = $db->prepare(
        'INSERT INTO productos (producto, uri, foto, descripcion, costo, precio, stock, id_categoria)
         VALUES (?, ?, NULL, ?, 7000.00, 13000.00, 4, 1)'
    );
    $uri = 'vaso-stitch-' . bin2hex(random_bytes(4));
    $descripcion = 'Vaso celeste con sorbete. Consultá disponibilidad.';
    $stmt->bind_param('sss', $nombre, $uri, $descripcion);
    $stmt->execute();
    $id = (int) $db->insert_id;
    $stmt->close();
    return $id;
}

function job_fixture_facebook(mysqli $db, int $productoId): int
{
    $db->begin_transaction();
    try {
        verificar_facebook(facebook_encolar_producto($db, $productoId, 'alta'), 'El producto de prueba debe encolarse');
        $id = (int) $db->insert_id;
        $db->commit();
        return $id;
    } catch (Throwable $error) {
        $db->rollback();
        throw $error;
    }
}

function leer_job_facebook(mysqli $db, int $id): array
{
    $stmt = $db->prepare('SELECT * FROM facebook_publicaciones WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $job = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    verificar_facebook(is_array($job), 'Debe existir la publicación pendiente ' . $id);
    return $job;
}

function contar_jobs_facebook(mysqli $db): int
{
    return (int) $db->query('SELECT COUNT(*) FROM facebook_publicaciones')->fetch_row()[0];
}

function vaciar_cola_facebook(mysqli $db): void
{
    $db->query('DELETE FROM facebook_publicaciones');
}

function contenido_job_facebook(mysqli $db, int $id): array
{
    $datos = json_decode((string) leer_job_facebook($db, $id)['contenido'], true, 512, JSON_THROW_ON_ERROR);
    verificar_facebook(is_array($datos), 'El contenido guardado debe ser JSON de un objeto');
    return $datos;
}

$variables = [
    'FACEBOOK_AUTO_PUBLISH' => '1',
    'FACEBOOK_PAGE_ID' => '555387371639380',
    'FACEBOOK_PAGE_ACCESS_TOKEN' => 'token_fixture_no_real',
    'FACEBOOK_GRAPH_VERSION' => 'v26.0',
    'APP_DOMAIN' => 'catalogo.example.test',
];
$entornoAnterior = [];
foreach ($variables as $nombre => $valor) {
    $entornoAnterior[$nombre] = getenv($nombre);
    putenv($nombre . '=' . $valor);
}

$config = [
    'page_id' => '555387371639380',
    'access_token' => 'token_fixture_no_real',
    'version' => 'v26.0',
];
$db = null;
$tablasTemporales = [];
$codigoSalida = 0;

try {
    $db = conexion();
    $db->query(
        'CREATE TEMPORARY TABLE categorias (
            id INT UNSIGNED NOT NULL PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            uri VARCHAR(200) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci'
    );
    $tablasTemporales[] = 'categorias';
    $db->query('INSERT INTO categorias (id, nombre, uri) VALUES (1, "General", "general"), (2, "Juguetes", "juguetes")');
    $db->query(
        'CREATE TEMPORARY TABLE productos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            producto VARCHAR(200) NOT NULL,
            uri VARCHAR(200) NULL,
            foto VARCHAR(255) NULL,
            descripcion TEXT NULL,
            costo DECIMAL(10,2) NOT NULL DEFAULT 0,
            precio DECIMAL(10,2) NOT NULL DEFAULT 0,
            stock INT NULL,
            id_categoria INT UNSIGNED NOT NULL,
            agregado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modificado TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_productos_uri (uri)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci'
    );
    $tablasTemporales[] = 'productos';

    $migracion = file_get_contents(__DIR__ . '/../migrations/20261013_facebook_publicaciones.sql');
    verificar_facebook(is_string($migracion), 'Debe existir la migración de la cola');
    $sqlTemporal = preg_replace(
        '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?=`?facebook_publicaciones`?\s*\()/i',
        'CREATE TEMPORARY TABLE ',
        $migracion,
        1,
        $reemplazos
    );
    verificar_facebook($reemplazos === 1 && is_string($sqlTemporal), 'La migración debe crear la tabla de publicaciones');
    $db->query($sqlTemporal);
    $tablasTemporales[] = 'facebook_publicaciones';

    $productoId = producto_fixture_facebook($db);
    $producto = facebook_producto_leer($db, $productoId);
    verificar_facebook(is_array($producto), 'La lectura devuelve el producto existente');
    igual_facebook('General', $producto['categoria'], 'La lectura incluye el nombre de categoría');
    igual_facebook('general', $producto['categoria_uri'], 'La lectura incluye su URI canónica');

    putenv('FACEBOOK_AUTO_PUBLISH=0');
    igual_facebook(false, facebook_activado(), 'La automatización respeta el interruptor');
    igual_facebook(false, facebook_encolar_producto($db, $productoId, 'alta'), 'Desactivada no encola');
    igual_facebook(0, contar_jobs_facebook($db), 'Desactivada deja vacía la cola');
    putenv('FACEBOOK_AUTO_PUBLISH=1');

    $jobId = job_fixture_facebook($db, $productoId);
    $contenido = contenido_job_facebook($db, $jobId);
    verificar_facebook(isset($contenido['message'], $contenido['link']), 'El snapshot tiene mensaje y enlace');
    verificar_facebook(str_contains($contenido['message'], $producto['producto']), 'El mensaje muestra el nombre del producto');
    verificar_facebook(str_contains($contenido['message'], '13.000'), 'El mensaje muestra el precio de venta');
    verificar_facebook(!str_contains($contenido['message'], '7.000'), 'El mensaje no publica el costo interno');
    igual_facebook(
        sitio_url(producto_path($producto['uri'], $producto['categoria_uri'])),
        $contenido['link'],
        'El snapshot usa el enlace canónico público del producto'
    );
    verificar_facebook(!str_contains(json_encode($contenido, JSON_THROW_ON_ERROR), $config['access_token']), 'El snapshot no almacena el token');
    vaciar_cola_facebook($db);

    $db->begin_transaction();
    $antes = facebook_producto_leer($db, $productoId, true);
    igual_facebook(false, facebook_encolar_producto($db, $productoId, 'edicion', $antes), 'Guardar sin cambios no encola');
    $db->query('UPDATE productos SET costo = 8000.00 WHERE id = ' . $productoId);
    igual_facebook(false, facebook_encolar_producto($db, $productoId, 'edicion', $antes), 'Cambiar solamente el costo no encola');
    $db->commit();
    igual_facebook(0, contar_jobs_facebook($db), 'Los guardados sin cambios públicos no generan publicaciones');

    $cambios = [
        ['producto', 'Vaso Stitch actualizado'],
        ['foto', 'foto_prueba'],
        ['descripcion', 'Nueva descripción pública'],
        ['precio', '14500.00'],
        ['stock', '7'],
        ['id_categoria', '2'],
    ];
    foreach ($cambios as [$campo, $valor]) {
        $db->begin_transaction();
        $antes = facebook_producto_leer($db, $productoId, true);
        $stmt = $db->prepare('UPDATE productos SET ' . $campo . ' = ? WHERE id = ?');
        $stmt->bind_param('si', $valor, $productoId);
        $stmt->execute();
        $stmt->close();
        verificar_facebook(facebook_encolar_producto($db, $productoId, 'edicion', $antes), 'Cambiar ' . $campo . ' debe encolar');
        $db->commit();
    }
    igual_facebook(count($cambios), contar_jobs_facebook($db), 'Cada cambio público genera una publicación');
    vaciar_cola_facebook($db);

    $db->begin_transaction();
    $antesRollback = facebook_producto_leer($db, $productoId, true);
    $db->query('UPDATE productos SET precio = 15000.00 WHERE id = ' . $productoId);
    verificar_facebook(facebook_encolar_producto($db, $productoId, 'edicion', $antesRollback), 'La edición transaccional encola');
    igual_facebook(1, contar_jobs_facebook($db), 'La publicación existe dentro de la transacción');
    $db->rollback();
    igual_facebook(0, contar_jobs_facebook($db), 'Un rollback también elimina la publicación');
    igual_facebook($antesRollback['precio'], facebook_producto_leer($db, $productoId)['precio'], 'El rollback conserva el precio anterior');
    fwrite(STDOUT, "OK: interruptor, snapshots públicos, cambios relevantes y rollback de producto y cola.\n");

    $llamadas = [];
    $transporteExito = static function (string $method, string $url, array $headers, string $body) use (&$llamadas, $config): array {
        $llamadas[] = [$method, $url, $headers, $body];
        igual_facebook('POST', $method, 'Publicar usa POST');
        igual_facebook('https://graph.facebook.com/' . $config['version'] . '/' . $config['page_id'] . '/feed', $url, 'Publicar usa el feed de la página configurada');
        verificar_facebook(!str_contains($url, $config['access_token']), 'La URL de API no expone credenciales');
        parse_str($body, $datos);
        verificar_facebook(isset($datos['message'], $datos['link']), 'El POST envía mensaje y enlace');
        return ['status' => 200, 'body' => json_encode(['id' => $config['page_id'] . '_123456'], JSON_THROW_ON_ERROR)];
    };
    $jobId = job_fixture_facebook($db, $productoId);
    $resultados = facebook_procesar_cola($db, $config, 10, $transporteExito);
    igual_facebook(1, count($llamadas), 'Una publicación pendiente produce un único POST');
    igual_facebook('publicado', leer_job_facebook($db, $jobId)['estado'], 'Una respuesta exitosa queda publicada');
    igual_facebook($config['page_id'] . '_123456', leer_job_facebook($db, $jobId)['publicacion_id'], 'Guarda el identificador devuelto por Facebook');
    igual_facebook(1, (int) leer_job_facebook($db, $jobId)['intentos'], 'Cuenta el intento exitoso');
    igual_facebook([['id' => $jobId, 'estado' => 'publicado']], $resultados, 'El procesador identifica la publicación y su resultado');
    facebook_procesar_cola($db, $config, 10, $transporteExito);
    igual_facebook(1, count($llamadas), 'Una publicación enviada no se vuelve a publicar');
    vaciar_cola_facebook($db);

    $rechazos = 0;
    $transporteRechazo = static function ($method, $url, $headers, $body) use (&$rechazos): array {
        $rechazos++;
        return ['status' => 403, 'body' => json_encode(['error' => [
            'message' => 'Falta pages_manage_posts', 'type' => 'OAuthException', 'code' => 200,
        ]], JSON_THROW_ON_ERROR)];
    };
    $jobId = job_fixture_facebook($db, $productoId);
    facebook_procesar_cola($db, $config, 10, $transporteRechazo);
    igual_facebook('fallido', leer_job_facebook($db, $jobId)['estado'], 'Un rechazo permanente de permisos queda fallido');
    verificar_facebook(trim((string) leer_job_facebook($db, $jobId)['ultimo_error']) !== '', 'El rechazo guarda un diagnóstico');
    facebook_procesar_cola($db, $config, 10, $transporteRechazo);
    igual_facebook(1, $rechazos, 'El permiso rechazado no produce reintentos automáticos');
    vaciar_cola_facebook($db);

    $transitorios = 0;
    $transporteTransitorio = static function ($method, $url, $headers, $body) use (&$transitorios): array {
        $transitorios++;
        return ['status' => 429, 'body' => json_encode(['error' => [
            'message' => 'Límite temporal de solicitudes', 'type' => 'OAuthException', 'code' => 4, 'is_transient' => true,
        ]], JSON_THROW_ON_ERROR)];
    };
    $jobs = [];
    for ($i = 0; $i < 3; $i++) {
        $jobs[] = job_fixture_facebook($db, producto_fixture_facebook($db, 'Producto del lote ' . $i));
    }
    facebook_procesar_cola($db, $config, 2, $transporteTransitorio);
    igual_facebook(2, $transitorios, 'El límite de lote limita los envíos');
    igual_facebook('reintentar', leer_job_facebook($db, $jobs[0])['estado'], 'Un error transitorio programa un reintento');
    igual_facebook('reintentar', leer_job_facebook($db, $jobs[1])['estado'], 'El segundo error transitorio también programa un reintento');
    igual_facebook('pendiente', leer_job_facebook($db, $jobs[2])['estado'], 'El excedente del lote permanece pendiente');
    igual_facebook(
        1,
        (int) $db->query('SELECT disponible > CURRENT_TIMESTAMP FROM facebook_publicaciones WHERE id = ' . $jobs[0])->fetch_row()[0],
        'Los reintentos tienen una espera en el reloj de la base'
    );
    $db->query('UPDATE facebook_publicaciones SET estado = "omitido" WHERE id = ' . $jobs[2]);
    facebook_procesar_cola($db, $config, 2, $transporteTransitorio);
    igual_facebook(2, $transitorios, 'El reintento espera hasta su fecha disponible');
    $db->query('UPDATE facebook_publicaciones SET disponible = DATE_SUB(NOW(), INTERVAL 1 MINUTE), intentos = 4 WHERE estado = "reintentar"');
    facebook_procesar_cola($db, $config, 2, $transporteTransitorio);
    igual_facebook(4, $transitorios, 'Los reintentos vencidos vuelven a intentarse');
    foreach (array_slice($jobs, 0, 2) as $id) {
        igual_facebook('fallido', leer_job_facebook($db, $id)['estado'], 'Al alcanzar cinco intentos deja de reintentar');
        igual_facebook(5, (int) leer_job_facebook($db, $id)['intentos'], 'Respeta el máximo de cinco intentos');
    }
    vaciar_cola_facebook($db);
    fwrite(STDOUT, "OK: publicación exitosa, rechazo de permisos, espera, límite de lote y límite de reintentos.\n");

    $idOrden = producto_fixture_facebook($db, 'Producto con dos precios sucesivos');
    $jobAnterior = job_fixture_facebook($db, $idOrden);
    $db->begin_transaction();
    $antes = facebook_producto_leer($db, $idOrden, true);
    $db->query('UPDATE productos SET precio = 16000.00 WHERE id = ' . $idOrden);
    verificar_facebook(facebook_encolar_producto($db, $idOrden, 'edicion', $antes), 'El nuevo precio genera una segunda publicación');
    $jobPosterior = (int) $db->insert_id;
    $db->commit();
    $idIndependiente = producto_fixture_facebook($db, 'Otro producto que puede publicarse');
    $jobIndependiente = job_fixture_facebook($db, $idIndependiente);

    $mensajesOrden = [];
    $transporteOrden = static function ($method, $url, $headers, $body) use (&$mensajesOrden, $config): array {
        parse_str($body, $payload);
        $mensajesOrden[] = $payload['message'];
        if (count($mensajesOrden) === 1) {
            return ['status' => 429, 'body' => json_encode(['error' => [
                'message' => 'Reintentar más tarde', 'code' => 4, 'is_transient' => true,
            ]], JSON_THROW_ON_ERROR)];
        }
        return ['status' => 200, 'body' => json_encode(['id' => $config['page_id'] . '_' . (1000 + count($mensajesOrden))], JSON_THROW_ON_ERROR)];
    };
    facebook_procesar_cola($db, $config, 10, $transporteOrden);
    igual_facebook(2, count($mensajesOrden), 'El fallo de un producto permite publicar otros productos');
    verificar_facebook(str_contains($mensajesOrden[0], '13.000'), 'Primero se intenta la novedad con el precio anterior');
    verificar_facebook(str_contains($mensajesOrden[1], 'Otro producto que puede publicarse'), 'Otro producto continúa mientras se espera el reintento');
    igual_facebook('reintentar', leer_job_facebook($db, $jobAnterior)['estado'], 'La primera novedad queda esperando');
    igual_facebook('pendiente', leer_job_facebook($db, $jobPosterior)['estado'], 'La segunda novedad del mismo producto no adelanta a la anterior');
    igual_facebook('publicado', leer_job_facebook($db, $jobIndependiente)['estado'], 'La novedad independiente se publica');
    facebook_procesar_cola($db, $config, 10, $transporteOrden);
    igual_facebook(2, count($mensajesOrden), 'La segunda novedad espera mientras el reintento anterior no está disponible');
    $db->query('UPDATE facebook_publicaciones SET disponible = DATE_SUB(NOW(), INTERVAL 1 MINUTE) WHERE id = ' . $jobAnterior);
    facebook_procesar_cola($db, $config, 10, $transporteOrden);
    igual_facebook(4, count($mensajesOrden), 'Al resolver el reintento se publican las dos novedades pendientes');
    verificar_facebook(str_contains($mensajesOrden[2], '13.000') && !str_contains($mensajesOrden[2], '16.000'), 'El reintento conserva el snapshot con el precio anterior');
    verificar_facebook(str_contains($mensajesOrden[3], '16.000'), 'El precio nuevo se publica después del anterior');
    igual_facebook('publicado', leer_job_facebook($db, $jobAnterior)['estado'], 'La primera novedad finaliza publicada');
    igual_facebook('publicado', leer_job_facebook($db, $jobPosterior)['estado'], 'La segunda novedad finaliza publicada');
    vaciar_cola_facebook($db);

    $jobId = job_fixture_facebook($db, $productoId);
    $conexionBloqueo = new mysqli(
        (string) getenv('DB_HOST'), (string) getenv('DB_USER'), (string) getenv('DB_PASSWORD'),
        (string) getenv('DB_NAME'), (int) getenv('DB_PORT')
    );
    try {
        igual_facebook(1, (int) $conexionBloqueo->query("SELECT GET_LOCK('frani_facebook_publicaciones', 0)")->fetch_row()[0], 'La conexión auxiliar puede tomar el lock de publicación');
        $llamadasConLock = count($llamadas);
        igual_facebook([], facebook_procesar_cola($db, $config, 10, $transporteExito), 'Una segunda ejecución no procesa con el lock ocupado');
        igual_facebook($llamadasConLock, count($llamadas), 'Con el lock ocupado no realiza solicitudes');
        igual_facebook('pendiente', leer_job_facebook($db, $jobId)['estado'], 'Con el lock ocupado conserva la publicación pendiente');
        igual_facebook(0, (int) leer_job_facebook($db, $jobId)['intentos'], 'Con el lock ocupado no registra un intento');
    } finally {
        $conexionBloqueo->query("SELECT RELEASE_LOCK('frani_facebook_publicaciones')");
        $conexionBloqueo->close();
    }
    facebook_procesar_cola($db, $config, 10, $transporteExito);
    igual_facebook($llamadasConLock + 1, count($llamadas), 'Después de liberar el lock se permite procesar');
    igual_facebook('publicado', leer_job_facebook($db, $jobId)['estado'], 'La publicación bloqueada finaliza al liberar el lock');
    vaciar_cola_facebook($db);
    fwrite(STDOUT, "OK: orden de novedades por producto, snapshots en reintentos y exclusión entre conexiones.\n");

    $timeouts = 0;
    $transporteTimeout = static function ($method, $url, $headers, $body) use (&$timeouts): array {
        $timeouts++;
        throw new RuntimeException('Tiempo de espera agotado tras enviar la solicitud');
    };
    $jobId = job_fixture_facebook($db, $productoId);
    facebook_procesar_cola($db, $config, 10, $transporteTimeout);
    igual_facebook('incierto', leer_job_facebook($db, $jobId)['estado'], 'Un timeout queda incierto para evitar duplicados');
    facebook_procesar_cola($db, $config, 10, $transporteTimeout);
    igual_facebook(1, $timeouts, 'Una publicación incierta no se reenvía automáticamente');
    vaciar_cola_facebook($db);

    $jobId = job_fixture_facebook($db, $productoId);
    $db->query('UPDATE facebook_publicaciones SET estado = "enviando", intentos = 1 WHERE id = ' . $jobId);
    $llamadasAntes = count($llamadas);
    facebook_procesar_cola($db, $config, 10, $transporteExito);
    igual_facebook('incierto', leer_job_facebook($db, $jobId)['estado'], 'Un envío interrumpido se recupera como incierto');
    igual_facebook($llamadasAntes, count($llamadas), 'La recuperación de un envío interrumpido no duplica el POST');
    vaciar_cola_facebook($db);

    $idBorrado = producto_fixture_facebook($db, 'Producto que se elimina antes de publicar');
    $jobId = job_fixture_facebook($db, $idBorrado);
    $db->query('DELETE FROM productos WHERE id = ' . $idBorrado);
    facebook_procesar_cola($db, $config, 10, $transporteExito);
    igual_facebook('omitido', leer_job_facebook($db, $jobId)['estado'], 'Un producto eliminado se omite');
    igual_facebook($llamadasAntes, count($llamadas), 'Un producto eliminado no se publica');
    vaciar_cola_facebook($db);

    $jobId = job_fixture_facebook($db, $productoId);
    $otraPagina = $config;
    $otraPagina['page_id'] = '999999999999999';
    facebook_procesar_cola($db, $otraPagina, 10, $transporteExito);
    igual_facebook($llamadasAntes, count($llamadas), 'Una publicación para Frani no se envía a otra página');
    igual_facebook('pendiente', leer_job_facebook($db, $jobId)['estado'], 'La publicación conserva su destino pendiente');
    fwrite(STDOUT, "OK: timeout sin duplicados, recuperación de envíos, productos eliminados y destino de página.\n");
    fwrite(STDOUT, "OK: pruebas aisladas de Facebook, sin datos reales ni solicitudes de red.\n");
} catch (Throwable $error) {
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . "\n");
    $codigoSalida = 1;
} finally {
    if ($db instanceof mysqli) {
        $db->rollback();
        foreach (array_reverse($tablasTemporales) as $tabla) {
            $db->query('DROP TEMPORARY TABLE IF EXISTS ' . $tabla);
        }
    }
    foreach ($entornoAnterior as $nombre => $valor) {
        putenv($valor === false ? $nombre : $nombre . '=' . $valor);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_abort();
    }
}
exit($codigoSalida);
