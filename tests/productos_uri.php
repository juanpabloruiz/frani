<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/productos_uri.php
// La tabla temporal oculta productos solamente en esta conexión. Los datos reales
// no se modifican; MariaDB elimina la tabla temporal al cerrar la conexión.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../src/conexion.php';

function comprobar(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function comprobar_igual($esperado, $actual, string $mensaje): void
{
    comprobar(
        $esperado === $actual,
        $mensaje . ': esperado ' . var_export($esperado, true)
            . ', obtenido ' . var_export($actual, true)
    );
}

function insertar_producto_prueba(mysqli $db, string $producto, ?string $uri = null, ?string $modificado = '2020-01-02 03:04:05'): int
{
    $stmt = $db->prepare(
        'INSERT INTO productos (producto, uri, agregado, modificado) VALUES (?, ?, "2020-01-01 00:00:00", ?)'
    );
    $stmt->bind_param('sss', $producto, $uri, $modificado);
    $stmt->execute();
    $id = (int) $db->insert_id;
    $stmt->close();
    return $id;
}

function leer_producto_prueba(mysqli $db, int $id): array
{
    $stmt = $db->prepare('SELECT producto, uri, agregado, modificado FROM productos WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    comprobar(is_array($producto), 'Debe existir el producto de prueba ' . $id);
    return $producto;
}

$db = null;
$tablaTemporalCreada = false;

try {
    $casos = [
        ['Masa mágica por 12', 'masa-magica-por-12'],
        ['ÁÉÍÓÚÜÑ', 'aeiouun'],
        ["CAFE\u{0301} NIN\u{0303}O", 'cafe-nino'],
        ['  ¡NIÑO! / ÑANDÚ -- Útil, ¿sí?  ', 'nino-nandu-util-si'],
        ['__Hola...   Mundo///---__', 'hola-mundo'],
        ['Oferta 50% + IVA', 'oferta-50-iva'],
        ['😀 🧸 🎁', 'producto'],
        ['', 'producto'],
        ['   ', 'producto'],
    ];
    foreach ($casos as [$titulo, $esperado]) {
        comprobar_igual($esperado, generar_uri($titulo), 'Normalización del título ' . $titulo);
    }

    $malicioso = generar_uri('<script>alert("XSS")</script> ../ Admin? a=b#c&d');
    comprobar(
        preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $malicioso) === 1,
        'Un título con HTML o caracteres de URL solamente debe generar letras ASCII, números y guiones'
    );
    comprobar_igual(str_repeat('a', 200), generar_uri(str_repeat('Á', 260)), 'La URI debe limitarse a 200 caracteres');

    $db = conexion();
    $db->query(
        'CREATE TEMPORARY TABLE productos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            producto VARCHAR(200) NOT NULL,
            uri VARCHAR(200) NULL,
            agregado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modificado TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY idx_productos_uri (uri)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci'
    );
    $tablaTemporalCreada = true;

    $idOriginal = insertar_producto_prueba($db, 'Masa mágica por 12');
    $originalAntes = leer_producto_prueba($db, $idOriginal);
    comprobar_igual('masa-magica-por-12', asignar_uri_producto($db, $idOriginal), 'URI del primer título');
    $originalDespues = leer_producto_prueba($db, $idOriginal);
    comprobar_igual($originalAntes['agregado'], $originalDespues['agregado'], 'Asignar URI debe conservar agregado');
    comprobar_igual($originalAntes['modificado'], $originalDespues['modificado'], 'Asignar URI debe conservar modificado');

    $idDuplicado = insertar_producto_prueba($db, 'Masa mágica por 12');
    comprobar_igual('masa-magica-por-12-2', asignar_uri_producto($db, $idDuplicado), 'Un título duplicado debe obtener sufijo -2');
    $idDuplicado3 = insertar_producto_prueba($db, 'Masa mágica por 12');
    comprobar_igual('masa-magica-por-12-3', asignar_uri_producto($db, $idDuplicado3), 'Un tercer título duplicado debe obtener sufijo -3');
    $idTermina2 = insertar_producto_prueba($db, 'Masa mágica por 12 2');
    comprobar_igual('masa-magica-por-12-2-2', asignar_uri_producto($db, $idTermina2), 'Un título que termina en 2 también debe resolver su colisión');

    $tituloLargo = str_repeat('a', 200);
    $idLargo = insertar_producto_prueba($db, $tituloLargo);
    $idLargoDuplicado = insertar_producto_prueba($db, $tituloLargo);
    comprobar_igual($tituloLargo, asignar_uri_producto($db, $idLargo), 'Una URI larga debe conservar hasta 200 caracteres');
    $uriLargaDuplicada = asignar_uri_producto($db, $idLargoDuplicado);
    comprobar_igual(str_repeat('a', 198) . '-2', $uriLargaDuplicada, 'El sufijo único debe caber dentro del límite de 200 caracteres');

    $stmt = $db->prepare('UPDATE productos SET producto = ? WHERE id = ?');
    $tituloNuevo = 'Otro nombre con ácentos';
    $stmt->bind_param('si', $tituloNuevo, $idOriginal);
    $stmt->execute();
    $stmt->close();
    $antesReasignar = leer_producto_prueba($db, $idOriginal);
    comprobar_igual('masa-magica-por-12', asignar_uri_producto($db, $idOriginal), 'Cambiar el título no debe cambiar un enlace existente');
    comprobar_igual($antesReasignar, leer_producto_prueba($db, $idOriginal), 'Reasignar una URI existente no debe modificar la fila');

    $idNulo = insertar_producto_prueba($db, 'Muñeca pequeña', null, null);
    $idVacio = insertar_producto_prueba($db, 'Lápices de colores', '');
    $idExistente = insertar_producto_prueba($db, 'Título actualizado', 'enlace-publicado');
    $nuloAntes = leer_producto_prueba($db, $idNulo);
    $vacioAntes = leer_producto_prueba($db, $idVacio);
    $existenteAntes = leer_producto_prueba($db, $idExistente);
    comprobar_igual(2, completar_uri_productos($db), 'El backfill debe completar únicamente las URI pendientes');
    $nuloDespues = leer_producto_prueba($db, $idNulo);
    $vacioDespues = leer_producto_prueba($db, $idVacio);
    comprobar_igual('muneca-pequena', $nuloDespues['uri'], 'El backfill debe completar las URI NULL');
    comprobar_igual('lapices-de-colores', $vacioDespues['uri'], 'El backfill debe completar las URI vacías');
    foreach (['agregado', 'modificado'] as $campo) {
        comprobar_igual($nuloAntes[$campo], $nuloDespues[$campo], 'El backfill debe conservar ' . $campo . ' del producto con URI NULL');
        comprobar_igual($vacioAntes[$campo], $vacioDespues[$campo], 'El backfill debe conservar ' . $campo . ' del producto con URI vacía');
    }
    comprobar_igual($existenteAntes, leer_producto_prueba($db, $idExistente), 'El backfill debe conservar enlaces ya publicados');

    $filasAntes = $db->query('SELECT * FROM productos ORDER BY id')->fetch_all(MYSQLI_ASSOC);
    comprobar_igual(0, completar_uri_productos($db), 'Un segundo backfill no debe volver a completar productos');
    comprobar_igual($filasAntes, $db->query('SELECT * FROM productos ORDER BY id')->fetch_all(MYSQLI_ASSOC), 'Repetir el backfill debe ser idempotente');
    comprobar_igual(0, (int) $db->query('SELECT COUNT(*) FROM productos WHERE uri IS NULL OR uri = ""')->fetch_row()[0], 'Todos los productos deben tener URI');

    fwrite(STDOUT, "OK: normalización, colisiones, estabilidad de enlaces y backfill sin alterar timestamps.\n");
} catch (Throwable $error) {
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . "\n");
    exit(1);
} finally {
    if ($tablaTemporalCreada && $db instanceof mysqli) {
        $db->query('DROP TEMPORARY TABLE productos');
    }
}
