<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/categorias_uri.php
// Ambas tablas son temporales y ocultan los datos reales sólo en esta conexión.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../src/conexion.php';

function verificar_categoria(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function verificar_categoria_igual($esperado, $actual, string $mensaje): void
{
    verificar_categoria(
        $esperado === $actual,
        $mensaje . ': esperado ' . var_export($esperado, true)
            . ', obtenido ' . var_export($actual, true)
    );
}

function insertar_categoria_prueba(mysqli $db, string $nombre, ?string $uri = null, ?string $modificado = '2020-01-02 03:04:05'): int
{
    $stmt = $db->prepare(
        'INSERT INTO categorias (nombre, uri, agregado, modificado) VALUES (?, ?, "2020-01-01 00:00:00", ?)'
    );
    $stmt->bind_param('sss', $nombre, $uri, $modificado);
    $stmt->execute();
    $id = (int) $db->insert_id;
    $stmt->close();
    return $id;
}

function leer_categoria_prueba(mysqli $db, int $id): array
{
    $stmt = $db->prepare('SELECT nombre, uri, agregado, modificado FROM categorias WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $categoria = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    verificar_categoria(is_array($categoria), 'Debe existir la categoría de prueba ' . $id);
    return $categoria;
}

function insertar_relacionado_prueba(mysqli $db, string $nombre, int $idCategoria, ?string $uri): int
{
    $stmt = $db->prepare(
        'INSERT INTO productos (producto, id_categoria, uri, foto, precio) VALUES (?, ?, ?, "foto-prueba.jpg", 1250.50)'
    );
    $stmt->bind_param('sis', $nombre, $idCategoria, $uri);
    $stmt->execute();
    $id = (int) $db->insert_id;
    $stmt->close();
    return $id;
}

function verificar_relacionados(array $productos, int $actual, array $categoriasPorProducto, array $urisPorCategoria): void
{
    verificar_categoria(count($productos) <= 3, 'Deben mostrarse como máximo tres productos relacionados');
    $ids = [];
    foreach ($productos as $producto) {
        foreach (['id', 'producto', 'uri', 'foto', 'precio', 'categoria_uri'] as $campo) {
            verificar_categoria(array_key_exists($campo, $producto), 'Debe devolverse el campo ' . $campo . ' del producto relacionado');
        }
        $id = (int) $producto['id'];
        verificar_categoria($id !== $actual, 'El producto actual debe excluirse de los relacionados');
        verificar_categoria(!in_array($id, $ids, true), 'Un producto relacionado no debe aparecer duplicado');
        verificar_categoria(isset($categoriasPorProducto[$id]), 'Debe devolverse un producto elegible con URI');
        $idCategoria = $categoriasPorProducto[$id];
        verificar_categoria_igual($urisPorCategoria[$idCategoria], $producto['categoria_uri'], 'El enlace debe usar la URI de la categoría correspondiente');
        verificar_categoria_igual(
            '/' . $producto['categoria_uri'] . '/' . $producto['uri'],
            producto_path($producto['uri'], $producto['categoria_uri']),
            'El enlace relacionado debe incorporar categoría y producto'
        );
        verificar_categoria_igual('foto-prueba.jpg', $producto['foto'], 'Debe devolverse la foto del relacionado');
        verificar_categoria_igual(1250.5, (float) $producto['precio'], 'Debe devolverse el precio del relacionado');
        $ids[] = $id;
    }
}

$db = null;
$categoriasTemporales = false;
$productosTemporales = false;
$codigoSalida = 0;

try {
    verificar_categoria_igual('/munecas-y-bebes', categoria_path('munecas-y-bebes'), 'Ruta de categoría');
    verificar_categoria_igual('/masas/masa-magica-por-12', producto_path('masa-magica-por-12', 'masas'), 'Ruta jerárquica de producto');

    $db = conexion();
    $db->query(
        'CREATE TEMPORARY TABLE categorias (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            uri VARCHAR(200) NULL,
            agregado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modificado TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_categorias_nombre (nombre),
            UNIQUE KEY uk_categorias_uri (uri)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci'
    );
    $categoriasTemporales = true;
    $db->query(
        'CREATE TEMPORARY TABLE productos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            producto VARCHAR(200) NOT NULL,
            id_categoria INT UNSIGNED NOT NULL,
            uri VARCHAR(200) NULL,
            foto VARCHAR(255) NULL,
            descripcion TEXT NULL,
            precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            stock INT NULL,
            agregado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            modificado TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_productos_uri (uri)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci'
    );
    $productosTemporales = true;

    $idOriginal = insertar_categoria_prueba($db, 'Muñecas y bebés');
    $originalAntes = leer_categoria_prueba($db, $idOriginal);
    verificar_categoria_igual('munecas-y-bebes', asignar_uri_categoria($db, $idOriginal), 'Normalización de acentos y ñ en categorías');
    $originalDespues = leer_categoria_prueba($db, $idOriginal);
    foreach (['agregado', 'modificado'] as $campo) {
        verificar_categoria_igual($originalAntes[$campo], $originalDespues[$campo], 'Asignar URI debe conservar ' . $campo);
    }
    $idDuplicado = insertar_categoria_prueba($db, 'Muñecas / y bebés');
    verificar_categoria_igual('munecas-y-bebes-2', asignar_uri_categoria($db, $idDuplicado), 'Una categoría con el mismo nombre normalizado debe recibir sufijo');
    $idCompuesto = insertar_categoria_prueba($db, "CAFE\u{0301} NIN\u{0303}O");
    verificar_categoria_igual('cafe-nino', asignar_uri_categoria($db, $idCompuesto), 'Acentos Unicode combinados en categorías');

    foreach (['panel', 'productos', 'producto', 'categoria', 'test', 'img', 'css', 'js', 'fontawesome', 'index', 'api', 'admin', 'robots', 'sitemap', 'migrations', 'assets'] as $reservado) {
        $id = insertar_categoria_prueba($db, strtoupper($reservado));
        verificar_categoria_igual($reservado . '-2', asignar_uri_categoria($db, $id), 'La categoría debe evitar la ruta reservada ' . $reservado);
    }
    $idPanelDuplicado = insertar_categoria_prueba($db, 'Panel!');
    verificar_categoria_igual('panel-3', asignar_uri_categoria($db, $idPanelDuplicado), 'Las rutas reservadas también deben resolver colisiones entre sufijos');
    $idVacio = insertar_categoria_prueba($db, '');
    verificar_categoria_igual('categoria-3', asignar_uri_categoria($db, $idVacio), 'Un nombre vacío debe usar un fallback de categoría sin invadir rutas reservadas');
    $idEmoji = insertar_categoria_prueba($db, '😀 🎁');
    verificar_categoria_igual('categoria-4', asignar_uri_categoria($db, $idEmoji), 'Un nombre de emoji debe usar el fallback único de categoría');

    $idLargo = insertar_categoria_prueba($db, str_repeat('Æ', 150));
    $idLargoDuplicado = insertar_categoria_prueba($db, str_repeat('Æ', 149) . '!');
    verificar_categoria_igual(str_repeat('ae', 100), asignar_uri_categoria($db, $idLargo), 'La URI transliterada debe limitarse a 200 caracteres');
    verificar_categoria_igual(str_repeat('ae', 99) . '-2', asignar_uri_categoria($db, $idLargoDuplicado), 'El sufijo debe respetar el límite de 200 caracteres');

    $stmt = $db->prepare('UPDATE categorias SET nombre = ? WHERE id = ?');
    $nombreActualizado = 'Bebés y juguetes';
    $stmt->bind_param('si', $nombreActualizado, $idOriginal);
    $stmt->execute();
    $stmt->close();
    $antesReasignar = leer_categoria_prueba($db, $idOriginal);
    verificar_categoria_igual('munecas-y-bebes', asignar_uri_categoria($db, $idOriginal), 'Editar el nombre debe conservar enlaces publicados');
    verificar_categoria_igual($antesReasignar, leer_categoria_prueba($db, $idOriginal), 'Asignar una URI existente no debe alterar la categoría');

    $idNulo = insertar_categoria_prueba($db, 'Juegos didácticos', null, null);
    $idCadenaVacia = insertar_categoria_prueba($db, 'Arte y diseño', '');
    $idExistente = insertar_categoria_prueba($db, 'Nombre actualizado', 'enlace-publicado');
    $nuloAntes = leer_categoria_prueba($db, $idNulo);
    $cadenaVaciaAntes = leer_categoria_prueba($db, $idCadenaVacia);
    $existenteAntes = leer_categoria_prueba($db, $idExistente);
    verificar_categoria_igual(2, completar_uri_categorias($db), 'Debe completar solamente las URI NULL o vacías');
    $nuloDespues = leer_categoria_prueba($db, $idNulo);
    $cadenaVaciaDespues = leer_categoria_prueba($db, $idCadenaVacia);
    verificar_categoria_igual('juegos-didacticos', $nuloDespues['uri'], 'Debe completar la URI NULL');
    verificar_categoria_igual('arte-y-diseno', $cadenaVaciaDespues['uri'], 'Debe completar la URI vacía');
    foreach (['agregado', 'modificado'] as $campo) {
        verificar_categoria_igual($nuloAntes[$campo], $nuloDespues[$campo], 'El backfill debe conservar ' . $campo . ' de la categoría NULL');
        verificar_categoria_igual($cadenaVaciaAntes[$campo], $cadenaVaciaDespues[$campo], 'El backfill debe conservar ' . $campo . ' de la categoría vacía');
    }
    verificar_categoria_igual($existenteAntes, leer_categoria_prueba($db, $idExistente), 'El backfill debe conservar categorías con enlace publicado');
    $filasAntes = $db->query('SELECT * FROM categorias ORDER BY id')->fetch_all(MYSQLI_ASSOC);
    verificar_categoria_igual(0, completar_uri_categorias($db), 'Un segundo backfill debe devolver cero');
    verificar_categoria_igual($filasAntes, $db->query('SELECT * FROM categorias ORDER BY id')->fetch_all(MYSQLI_ASSOC), 'El backfill de categorías debe ser idempotente');

    $idPreferida = insertar_categoria_prueba($db, 'Categoría preferida', 'categoria-preferida');
    $idAlternativa = insertar_categoria_prueba($db, 'Alternativas', 'alternativas');
    $idSinCategoriaUri = insertar_categoria_prueba($db, 'Sin enlace de categoría');
    $idCategoriaVacia = insertar_categoria_prueba($db, 'Enlace de categoría vacío', '');
    $actual = insertar_relacionado_prueba($db, 'Producto actual', $idPreferida, 'producto-actual');
    $categoriasPorProducto = [];
    $mismaCategoria = [];
    for ($numero = 1; $numero <= 4; $numero++) {
        $id = insertar_relacionado_prueba($db, 'Relacionado ' . $numero, $idPreferida, 'relacionado-' . $numero);
        $mismaCategoria[] = $id;
        $categoriasPorProducto[$id] = $idPreferida;
    }
    $alternativas = [];
    for ($numero = 1; $numero <= 2; $numero++) {
        $id = insertar_relacionado_prueba($db, 'Alternativa ' . $numero, $idAlternativa, 'alternativa-' . $numero);
        $alternativas[] = $id;
        $categoriasPorProducto[$id] = $idAlternativa;
    }
    insertar_relacionado_prueba($db, 'Sin URI de producto', $idPreferida, null);
    insertar_relacionado_prueba($db, 'URI de producto vacía', $idPreferida, '');
    insertar_relacionado_prueba($db, 'Sin URI de categoría', $idSinCategoriaUri, 'sin-categoria-uri');
    insertar_relacionado_prueba($db, 'URI de categoría vacía', $idCategoriaVacia, 'categoria-uri-vacia');
    $urisPorCategoria = [$idPreferida => 'categoria-preferida', $idAlternativa => 'alternativas'];

    $relacionados = productos_relacionados($db, $actual, $idPreferida);
    verificar_categoria_igual(3, count($relacionados), 'Debe devolver tres productos si la misma categoría tiene suficientes candidatos');
    verificar_relacionados($relacionados, $actual, $categoriasPorProducto, $urisPorCategoria);
    foreach ($relacionados as $producto) {
        verificar_categoria(in_array((int) $producto['id'], $mismaCategoria, true), 'Debe preferir productos de la misma categoría');
    }

    $db->query('DELETE FROM productos WHERE id IN (' . $mismaCategoria[2] . ', ' . $mismaCategoria[3] . ')');
    $relacionados = productos_relacionados($db, $actual, $idPreferida);
    verificar_categoria_igual(3, count($relacionados), 'Debe completar tres resultados con otra categoría cuando faltan candidatos');
    verificar_relacionados($relacionados, $actual, $categoriasPorProducto, $urisPorCategoria);
    $idsRelacionados = array_map(static fn (array $producto): int => (int) $producto['id'], $relacionados);
    verificar_categoria(in_array($mismaCategoria[0], $idsRelacionados, true) && in_array($mismaCategoria[1], $idsRelacionados, true), 'El fallback debe conservar todos los candidatos disponibles de la categoría actual');
    verificar_categoria_igual('categoria-preferida', $relacionados[0]['categoria_uri'], 'Los candidatos de la misma categoría deben aparecer primero');
    verificar_categoria_igual('categoria-preferida', $relacionados[1]['categoria_uri'], 'Los candidatos de la misma categoría deben preceder al fallback');
    verificar_categoria_igual('alternativas', $relacionados[2]['categoria_uri'], 'El tercer candidato debe completar desde otra categoría');

    $db->query('DELETE FROM productos WHERE id IN (' . $mismaCategoria[1] . ', ' . $alternativas[0] . ', ' . $alternativas[1] . ')');
    $relacionados = productos_relacionados($db, $actual, $idPreferida);
    verificar_categoria_igual(1, count($relacionados), 'Un catálogo pequeño debe devolver sólo los candidatos elegibles existentes');
    verificar_relacionados($relacionados, $actual, $categoriasPorProducto, $urisPorCategoria);
    verificar_categoria_igual($mismaCategoria[0], (int) $relacionados[0]['id'], 'Debe conservar el único candidato elegible');
    $db->query('DELETE FROM productos WHERE id = ' . $mismaCategoria[0]);
    verificar_categoria_igual([], productos_relacionados($db, $actual, $idPreferida), 'Sin otros productos elegibles debe devolver una lista vacía');

    fwrite(STDOUT, "OK: URI de categorías, rutas jerárquicas y productos relacionados con fallback.\n");
} catch (Throwable $error) {
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . "\n");
    $codigoSalida = 1;
} finally {
    if ($db instanceof mysqli) {
        if ($productosTemporales) {
            $db->query('DROP TEMPORARY TABLE productos');
        }
        if ($categoriasTemporales) {
            $db->query('DROP TEMPORARY TABLE categorias');
        }
    }
}

exit($codigoSalida);
