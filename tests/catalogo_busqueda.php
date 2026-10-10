<?php
declare(strict_types=1);

// Ejecutar: docker compose exec -T web php /var/www/backup/tests/catalogo_busqueda.php
// Las tablas temporales ocultan el catálogo sólo en esta conexión. Las peticiones
// HTTP posteriores leen el catálogo real y nunca modifican sus datos.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../src/conexion.php';
require_once __DIR__ . '/../src/catalogo.php';

function verificar_busqueda(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

function verificar_busqueda_igual($esperado, $actual, string $mensaje): void
{
    verificar_busqueda($esperado === $actual, $mensaje . ': esperado '
        . var_export($esperado, true) . ', obtenido ' . var_export($actual, true));
}

function insertar_busqueda(mysqli $db, string $nombre, string $uri, int $categoria,
    ?string $descripcion = null, float $precio = 10, string $fecha = '2010-01-01 00:00:00'): void
{
    $stmt = $db->prepare(
        'INSERT INTO productos (producto, uri, id_categoria, descripcion, precio, agregado)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('ssisds', $nombre, $uri, $categoria, $descripcion, $precio, $fecha);
    $stmt->execute();
    $stmt->close();
}

function uris_busqueda(mysqli $db, string $busqueda = '', ?int $categoria = null): array
{
    $resultado = catalogo_productos($db, $busqueda, $categoria);
    $uris = array_column($resultado->fetch_all(MYSQLI_ASSOC), 'uri');
    $resultado->free();
    return $uris;
}

function renderizar_busqueda(mysqli $db, string $busquedaCatalogo = '', ?int $categoriaCatalogo = null): string
{
    $resultado = catalogo_productos($db, $busquedaCatalogo, $categoriaCatalogo);
    ob_start();
    try {
        include __DIR__ . '/../src/_catalogo-productos.php';
        return (string) ob_get_contents();
    } finally {
        ob_end_clean();
        $resultado->free();
    }
}

function pedir_busqueda(string $ruta, string $metodo = 'GET'): array
{
    $contexto = stream_context_create(['http' => [
        'method' => $metodo, 'ignore_errors' => true, 'follow_location' => 0, 'timeout' => 10,
    ]]);
    $origen = rtrim(getenv('CATALOGO_TEST_BASE_URL') ?: 'http://127.0.0.1', '/');
    $cuerpo = file_get_contents($origen . $ruta, false, $contexto);
    verificar_busqueda($cuerpo !== false, 'Debe responder ' . $metodo . ' ' . $ruta);
    $cabeceras = $http_response_header ?? [];
    preg_match('/\s(\d{3})\s/', $cabeceras[0] ?? '', $estado);
    return ['cuerpo' => $cuerpo, 'estado' => (int) ($estado[1] ?? 0),
        'cabeceras' => implode("\n", $cabeceras)];
}

$db = null;
$categoriasTemporales = false;
$productosTemporales = false;
$codigoSalida = 0;

try {
    $db = conexion();
    $categoriaInexistente = (int) $db->query('SELECT COALESCE(MAX(id), 0) + 1 AS id FROM categorias')->fetch_assoc()['id'];
    $categoriaReal = $db->query('SELECT id, uri FROM categorias WHERE uri IS NOT NULL AND uri <> "" ORDER BY id LIMIT 1')->fetch_assoc();
    $db->query(
        'CREATE TEMPORARY TABLE categorias (
            id INT UNSIGNED NOT NULL PRIMARY KEY, nombre VARCHAR(150) NOT NULL,
            uri VARCHAR(200) NOT NULL
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci'
    );
    $categoriasTemporales = true;
    $db->query("INSERT INTO categorias VALUES (1, 'General', 'general'),
        (2, 'Juguetes Ágiles', 'juguetes-agiles'), (3, 'Vacía', 'vacia')");
    $db->query(
        'CREATE TEMPORARY TABLE productos (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            producto VARCHAR(200) NOT NULL, uri VARCHAR(200) NOT NULL,
            id_categoria INT UNSIGNED NOT NULL, descripcion TEXT NULL,
            foto VARCHAR(255) NULL, precio DECIMAL(10,2) NOT NULL DEFAULT 0,
            stock INT NULL, agregado TIMESTAMP NOT NULL,
            modificado TIMESTAMP NULL
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci'
    );
    $productosTemporales = true;

    verificar_busqueda_igual([], uris_busqueda($db), 'Inicio sin productos');
    verificar_busqueda(str_contains(renderizar_busqueda($db), 'No hay productos'), 'Mensaje del catálogo vacío');
    insertar_busqueda($db, 'Tesoro antiguo', 'tesoro-antiguo', 1, 'Fuera del inicio', 5);
    insertar_busqueda($db, 'Tesoro juguete', 'tesoro-juguete', 2);
    for ($numero = 1; $numero <= 27; $numero++) {
        $sufijo = sprintf('%02d', $numero);
        insertar_busqueda($db, 'Reciente ' . $sufijo, 'reciente-' . $sufijo, 1,
            null, 100 + $numero, '2025-01-' . $sufijo . ' 00:00:00');
    }
    $inicio = uris_busqueda($db);
    verificar_busqueda_igual(25, count($inicio), 'Inicio conserva los últimos 25 productos');
    verificar_busqueda_igual('reciente-27', $inicio[0], 'Inicio ordena por fecha descendente');
    verificar_busqueda(!in_array('tesoro-antiguo', $inicio, true), 'El producto antiguo queda fuera del inicio');
    verificar_busqueda_igual($inicio, uris_busqueda($db, " \t\n "), 'Espacios equivalen a búsqueda vacía');
    $tesoros = uris_busqueda($db, 'tesoro');
    sort($tesoros);
    verificar_busqueda_igual(['tesoro-antiguo', 'tesoro-juguete'], $tesoros, 'Búsqueda global encuentra productos anteriores a los últimos 25');
    verificar_busqueda_igual(27, count(uris_busqueda($db, 'reciente')), 'La búsqueda no limita los resultados a 25');
    verificar_busqueda_igual(['tesoro-antiguo'], uris_busqueda($db, 'tesoro', 1), 'General excluye productos de otras categorías');
    verificar_busqueda_igual(['tesoro-juguete'], uris_busqueda($db, 'tesoro', 2), 'Cada categoría conserva su alcance');
    $generales = catalogo_productos($db, '', 1)->fetch_all(MYSQLI_ASSOC);
    verificar_busqueda_igual(28, count($generales), 'Categoría sin búsqueda muestra todos sus productos');
    verificar_busqueda_igual('tesoro-antiguo', $generales[0]['uri'], 'Categoría ordena por precio ascendente');
    $precios = array_map('floatval', array_column($generales, 'precio'));
    $preciosOrdenados = $precios;
    sort($preciosOrdenados);
    verificar_busqueda_igual($preciosOrdenados, $precios, 'Todos los precios de categoría están ordenados');

    insertar_busqueda($db, 'Lápiz Mágico', 'lapiz-magico', 1, 'Para decoración delicada');
    insertar_busqueda($db, 'Lápiz seco', 'lapiz-seco', 1);
    verificar_busqueda_igual(['lapiz-magico'], uris_busqueda($db, 'LAPIZ mágico'), 'Búsqueda ignora mayúsculas y acentos');
    verificar_busqueda_igual(['lapiz-magico'], uris_busqueda($db, "  decoración\tGENERAL\nmagico  "),
        'Todas las palabras deben coincidir entre nombre, descripción y categoría');
    verificar_busqueda_igual(['lapiz-magico'], uris_busqueda($db, 'delicada'), 'La descripción es buscable');
    verificar_busqueda_igual(['tesoro-juguete'], uris_busqueda($db, 'AGILES'), 'El nombre de categoría es buscable sin acentos');
    verificar_busqueda_igual([], uris_busqueda($db, 'lapiz inexistente'), 'Una palabra ausente excluye el producto');

    foreach ([
        ['Descuento 50%', 'descuento-literal'], ['Descuento 50X', 'descuento-otro'],
        ['Código_A', 'codigo-literal'], ['CódigoXA', 'codigo-otro'],
        ['Oferta!', 'oferta-literal'], ['OfertaX', 'oferta-otra'],
        ["O'Reilly", 'apostrofo'],
    ] as [$nombre, $uri]) {
        insertar_busqueda($db, $nombre, $uri, 1);
    }
    foreach (['%' => 'descuento-literal', '_' => 'codigo-literal', '!' => 'oferta-literal', "O'Reilly" => 'apostrofo'] as $termino => $uri) {
        verificar_busqueda_igual([$uri], uris_busqueda($db, $termino), 'Buscar literalmente ' . $termino);
    }
    verificar_busqueda_igual([], uris_busqueda($db, "%' OR 1=1 --"), 'Texto de búsqueda no altera la consulta SQL');

    $nombrePeligroso = '<img src=x onerror="alert(1)"> & "Kit"';
    $categoriaPeligrosa = 'General <script>alert("cat")</script> & "otra"';
    $stmtCategoria = $db->prepare('INSERT INTO categorias VALUES (4, ?, "categoria-segura")');
    $stmtCategoria->bind_param('s', $categoriaPeligrosa);
    $stmtCategoria->execute();
    $stmtCategoria->close();
    insertar_busqueda($db, $nombrePeligroso, 'kit-seguro', 4, null, 1234.5);
    $html = renderizar_busqueda($db, 'Kit', 4);
    verificar_busqueda(str_contains($html, e($nombrePeligroso)) && str_contains($html, e($categoriaPeligrosa)),
        'Los nombres se escapan al renderizar las cards');
    verificar_busqueda(!str_contains($html, '<img src=x') && !str_contains($html, '<script>alert'), 'Las cards no permiten inyectar HTML');
    verificar_busqueda(str_contains($html, 'href="/categoria-segura/kit-seguro"'), 'El resultado enlaza a la ficha con su categoría');
    verificar_busqueda(str_contains($html, moneda(1234.5)), 'El resultado conserva el precio visible');
    verificar_busqueda(str_contains(renderizar_busqueda($db, '', 3), 'No hay productos'), 'Categoría vacía tiene su propio mensaje');
    verificar_busqueda(str_contains(renderizar_busqueda($db, 'zzzz-inexistente', 1), 'No hay resultados'), 'Búsqueda sin coincidencias tiene su propio mensaje');
    verificar_busqueda(!str_contains(renderizar_busqueda($db, '<script>alert("busqueda")</script>'), '<script>alert'),
        'El mensaje vacío no permite inyectar HTML desde la búsqueda');

    foreach ([
        '/buscar-productos.php?busqueda%5B%5D=lapiz',
        '/buscar-productos.php?busqueda=%FF',
        '/buscar-productos.php?busqueda=' . rawurlencode(str_repeat('ñ', 201)),
        '/buscar-productos.php?categoria%5B%5D=1',
        '/buscar-productos.php?categoria=0', '/buscar-productos.php?categoria=-1',
        '/buscar-productos.php?categoria=abc',
    ] as $ruta) {
        $respuesta = pedir_busqueda($ruta);
        verificar_busqueda_igual(400, $respuesta['estado'], 'Rechazar parámetros inválidos: ' . $ruta);
    }
    verificar_busqueda_igual(404, pedir_busqueda('/buscar-productos.php?categoria=' . $categoriaInexistente)['estado'],
        'Categoría inexistente responde 404');
    $post = pedir_busqueda('/buscar-productos.php', 'POST');
    verificar_busqueda_igual(405, $post['estado'], 'El endpoint permite sólo GET');
    verificar_busqueda(stripos($post['cabeceras'], 'Allow: GET') !== false, '405 informa el método permitido');
    foreach (['', str_repeat('ñ', 200), '<script>alert("consulta")</script>'] as $busqueda) {
        $respuesta = pedir_busqueda('/buscar-productos.php?busqueda=' . rawurlencode($busqueda));
        verificar_busqueda_igual(200, $respuesta['estado'], 'Búsqueda válida responde 200');
        verificar_busqueda(stripos($respuesta['cabeceras'], 'application/json') !== false, 'Resultados se sirven como JSON');
        verificar_busqueda(stripos($respuesta['cabeceras'], 'noindex') !== false, 'Endpoint no indexable');
        $datos = json_decode($respuesta['cuerpo'], true, 512, JSON_THROW_ON_ERROR);
        verificar_busqueda(is_string($datos['html'] ?? null) && is_int($datos['total'] ?? null), 'Respuesta incluye HTML y total');
        verificar_busqueda(!str_contains($datos['html'], '<script>alert("consulta")'), 'La búsqueda HTTP no inyecta HTML');
    }
    verificar_busqueda_igual(200, pedir_busqueda('/')['estado'], 'Portada pública disponible');
    if ($categoriaReal) {
        verificar_busqueda_igual(200, pedir_busqueda(categoria_path($categoriaReal['uri']))['estado'], 'Categoría pública disponible');
        verificar_busqueda_igual(200, pedir_busqueda('/buscar-productos.php?categoria=' . $categoriaReal['id'])['estado'],
            'Búsqueda con categoría real disponible');
    }
    fwrite(STDOUT, "OK: catálogo completo, alcance por categoría, orden, acentos, palabras, descripción, literales, HTML seguro y validación HTTP.\n");
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
