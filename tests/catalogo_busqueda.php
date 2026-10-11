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

function pagina_busqueda(mysqli $db, string $busqueda = '', ?int $categoria = null, int $offset = 0): array
{
    $pagina = catalogo_pagina($db, $busqueda, $categoria, $offset);
    $uris = array_column($pagina['resultado']->fetch_all(MYSQLI_ASSOC), 'uri');
    $pagina['resultado']->free();
    return ['uris' => $uris, 'total' => $pagina['total'], 'siguiente' => $pagina['siguiente']];
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
    verificar_busqueda_igual(['uris' => [], 'total' => 0, 'siguiente' => null], pagina_busqueda($db),
        'Primera página vacía no anuncia más resultados');
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
    $paginaInicio = pagina_busqueda($db);
    verificar_busqueda_igual($inicio, $paginaInicio['uris'], 'Primera página mantiene las novedades');
    verificar_busqueda_igual(29, $paginaInicio['total'], 'El total de portada incluye productos posteriores a los primeros 25');
    verificar_busqueda_igual(25, $paginaInicio['siguiente'], 'La siguiente página empieza tras las filas consumidas');
    verificar_busqueda_igual(['uris' => ['reciente-02', 'reciente-01', 'tesoro-juguete', 'tesoro-antiguo'],
        'total' => 29, 'siguiente' => null], pagina_busqueda($db, '', null, 25),
        'La última página global incluye productos antiguos sin repetir la primera');
    verificar_busqueda_igual(['uris' => [], 'total' => 29, 'siguiente' => null], pagina_busqueda($db, '', null, 1000),
        'Desplazamiento mayor al catálogo conserva total y termina');
    $paginaGeneral = pagina_busqueda($db, '', 1);
    verificar_busqueda_igual(array_slice(array_column($generales, 'uri'), 0, 25), $paginaGeneral['uris'],
        'Categoría entrega el primer lote en orden de precio');
    verificar_busqueda_igual(28, $paginaGeneral['total'], 'El total de categoría excluye las demás');
    verificar_busqueda_igual(array_slice(array_column($generales, 'uri'), 25), pagina_busqueda($db, '', 1, 25)['uris'],
        'Categoría entrega los productos restantes');

    insertar_busqueda($db, 'Lápiz Mágico', 'lapiz-magico', 1, 'Para decoración delicada');
    insertar_busqueda($db, 'Lápiz seco', 'lapiz-seco', 1);
    verificar_busqueda_igual(['lapiz-magico'], uris_busqueda($db, 'LAPIZ mágico'), 'Búsqueda ignora mayúsculas y acentos');
    verificar_busqueda_igual(['uris' => ['lapiz-magico'], 'total' => 1, 'siguiente' => null],
        pagina_busqueda($db, 'LAPIZ mágico', 1), 'La paginación comparte filtros de palabras, acentos y categoría');
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

    $db->query("INSERT INTO categorias VALUES (5, 'Lotes', 'lotes')");
    $lotes = [];
    for ($numero = 1; $numero <= 53; $numero++) {
        $sufijo = sprintf('%02d', $numero);
        $lotes[] = 'lote-' . $sufijo;
        insertar_busqueda($db, 'Lote paginado ' . $sufijo, 'lote-' . $sufijo, $numero <= 51 ? 5 : 2,
            null, 20, '2026-01-01 00:00:00');
    }
    $paginasGlobales = [];
    $offsetPagina = 0;
    do {
        $pagina = pagina_busqueda($db, 'lote paginado', null, $offsetPagina);
        verificar_busqueda_igual(53, $pagina['total'], 'Todas las páginas de búsqueda mantienen el total completo');
        $paginasGlobales[] = $pagina['uris'];
        $offsetPagina = $pagina['siguiente'];
    } while ($offsetPagina !== null);
    $lotesGlobales = array_merge(...$paginasGlobales);
    verificar_busqueda_igual([25, 25, 3], array_map('count', $paginasGlobales), 'Búsqueda se divide en lotes de 25');
    verificar_busqueda_igual(array_reverse($lotes), $lotesGlobales,
        'Empates de fecha mantienen orden estable por ID sin omisiones ni duplicados');
    verificar_busqueda_igual(53, count(array_unique($lotesGlobales)), 'Ninguna página repite productos');
    $paginasCategoria = [];
    $offsetPagina = 0;
    do {
        $pagina = pagina_busqueda($db, 'lote paginado', 5, $offsetPagina);
        verificar_busqueda_igual(51, $pagina['total'], 'Búsqueda paginada respeta el alcance de categoría');
        $paginasCategoria[] = $pagina['uris'];
        $offsetPagina = $pagina['siguiente'];
    } while ($offsetPagina !== null);
    verificar_busqueda_igual([25, 25, 1], array_map('count', $paginasCategoria), 'Último lote de categoría termina con sus filas reales');
    verificar_busqueda_igual(array_slice($lotes, 0, 51), array_merge(...$paginasCategoria),
        'Empates de precio mantienen orden estable por ID y excluyen otras categorías');
    verificar_busqueda_igual(51, pagina_busqueda($db, '', 5)['total'], 'Categoría vacía de búsqueda también se pagina');
    verificar_busqueda_igual(['uris' => [], 'total' => 0, 'siguiente' => null], pagina_busqueda($db, 'lote ausente', 5),
        'La búsqueda paginada sin coincidencias finaliza');
    $rechazoOffsetNegativo = false;
    try {
        catalogo_pagina($db, '', null, -1);
    } catch (InvalidArgumentException $error) {
        $rechazoOffsetNegativo = true;
    }
    verificar_busqueda($rechazoOffsetNegativo, 'La función rechaza desplazamientos negativos');

    foreach ([
        '/buscar-productos.php?busqueda%5B%5D=lapiz',
        '/buscar-productos.php?busqueda=%FF',
        '/buscar-productos.php?busqueda=' . rawurlencode(str_repeat('ñ', 201)),
        '/buscar-productos.php?categoria%5B%5D=1',
        '/buscar-productos.php?categoria=0', '/buscar-productos.php?categoria=-1',
        '/buscar-productos.php?categoria=abc',
        '/buscar-productos.php?offset%5B%5D=0', '/buscar-productos.php?offset=',
        '/buscar-productos.php?offset=-1', '/buscar-productos.php?offset=abc',
        '/buscar-productos.php?offset=1.5', '/buscar-productos.php?offset=1000001',
        '/buscar-productos.php?offset=1e2',
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
        verificar_busqueda(!array_key_exists('siguiente', $datos), 'La respuesta sin offset conserva su contrato original');
        verificar_busqueda(!str_contains($datos['html'], '<script>alert("consulta")'), 'La búsqueda HTTP no inyecta HTML');
    }
    foreach ([0, 25, 1000000] as $offsetHttp) {
        $respuesta = pedir_busqueda('/buscar-productos.php?offset=' . $offsetHttp);
        verificar_busqueda_igual(200, $respuesta['estado'], 'Desplazamiento válido responde 200');
        $datos = json_decode($respuesta['cuerpo'], true, 512, JSON_THROW_ON_ERROR);
        verificar_busqueda(is_string($datos['html'] ?? null) && is_int($datos['total'] ?? null), 'Página incluye HTML y total entero');
        verificar_busqueda(array_key_exists('siguiente', $datos) && ($datos['siguiente'] === null || is_int($datos['siguiente'])),
            'Página incluye el siguiente desplazamiento o null');
        if ($offsetHttp > $datos['total']) {
            verificar_busqueda_igual('', $datos['html'], 'Página agotada no añade mensajes vacíos a los productos');
            verificar_busqueda_igual(null, $datos['siguiente'], 'Página agotada finaliza el scroll');
        }
    }
    $respuestaVacia = pedir_busqueda('/buscar-productos.php?offset=0&busqueda=zzzz-prueba-catalogo-sin-coincidencias-482987');
    $paginaVacia = json_decode($respuestaVacia['cuerpo'], true, 512, JSON_THROW_ON_ERROR);
    verificar_busqueda_igual(0, $paginaVacia['total'], 'Búsqueda paginada HTTP sin coincidencias tiene total cero');
    verificar_busqueda_igual(null, $paginaVacia['siguiente'], 'Búsqueda paginada HTTP sin coincidencias finaliza');
    verificar_busqueda(str_contains($paginaVacia['html'], 'No hay resultados'), 'Primera página vacía conserva el mensaje útil');
    verificar_busqueda_igual(200, pedir_busqueda('/')['estado'], 'Portada pública disponible');
    if ($categoriaReal) {
        verificar_busqueda_igual(200, pedir_busqueda(categoria_path($categoriaReal['uri']))['estado'], 'Categoría pública disponible');
        verificar_busqueda_igual(200, pedir_busqueda('/buscar-productos.php?categoria=' . $categoriaReal['id'])['estado'],
            'Búsqueda con categoría real disponible');
    }
    $rutasPaginadas = [base_path()];
    if ($categoriaReal) $rutasPaginadas[] = categoria_path($categoriaReal['uri']);
    foreach ($rutasPaginadas as $rutaPaginada) {
        $primeraHtml = pedir_busqueda($rutaPaginada);
        $segundaHtml = pedir_busqueda($rutaPaginada . '?pagina=2');
        verificar_busqueda_igual(200, $segundaHtml['estado'], 'Segunda página disponible sin JavaScript');
        preg_match_all('/class="card(?: h-100)? product-card-link" href="([^"]+)"/', $primeraHtml['cuerpo'], $primeraCards);
        preg_match_all('/class="card(?: h-100)? product-card-link" href="([^"]+)"/', $segundaHtml['cuerpo'], $segundaCards);
        verificar_busqueda(count($primeraCards[1]) <= 25 && count($segundaCards[1]) <= 25, 'HTML limitado a una tanda por página');
        verificar_busqueda_igual([], array_values(array_intersect($primeraCards[1], $segundaCards[1])),
            'Segunda página HTML no repite tarjetas de la primera');
        $canonicaPagina = sitio_url(ltrim($rutaPaginada, '/')) . '?pagina=2';
        verificar_busqueda(str_contains($segundaHtml['cuerpo'], '<link rel="canonical" href="' . e($canonicaPagina) . '">'),
            'Cada página de respaldo tiene su propia canónica');
    }
    if ($categoriaReal) {
        $redireccionPagina = pedir_busqueda('/categoria.php?id=' . $categoriaReal['id'] . '&pagina=2');
        verificar_busqueda_igual(301, $redireccionPagina['estado'], 'Alias de categoría redirige a su ruta pública');
        verificar_busqueda(stripos($redireccionPagina['cabeceras'], 'Location: ' . categoria_path($categoriaReal['uri']) . '?pagina=2') !== false,
            'La redirección conserva la página solicitada');
    }
    fwrite(STDOUT, "OK: catálogo completo, páginas de 25, totales, orden estable, categorías, búsqueda, acentos, literales, HTML seguro y validación HTTP.\n");
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
