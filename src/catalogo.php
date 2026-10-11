<?php
declare(strict_types=1);

/** Los rewrites de categorías descartan GET; REQUEST_URI conserva la página original. */
function catalogo_numero_pagina(): int
{
    parse_str((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY), $parametros);
    return filter_var($parametros['pagina'] ?? $_GET['pagina'] ?? 1, FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 40001]]) ?: 1;
}

/** Comparte los filtros y el orden entre el catálogo completo y sus páginas. */
function catalogo_consulta(string $busqueda, ?int $categoriaId): array
{
    $palabras = preg_split('/\s+/u', trim($busqueda), -1, PREG_SPLIT_NO_EMPTY);
    $condiciones = [];
    $parametros = [];

    if ($categoriaId !== null) {
        $condiciones[] = 'p.id_categoria = ?';
        $parametros[] = $categoriaId;
    }

    foreach ($palabras as $palabra) {
        // Los signos % y _ se buscan como texto, no como comodines de SQL.
        $patron = '%' . strtr($palabra, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $condiciones[] = "(p.producto LIKE ? ESCAPE '!' OR p.descripcion LIKE ? ESCAPE '!' OR c.nombre LIKE ? ESCAPE '!')";
        array_push($parametros, $patron, $patron, $patron);
    }

    $sql = ' FROM productos p INNER JOIN categorias c ON c.id = p.id_categoria';
    if ($condiciones) {
        $sql .= ' WHERE ' . implode(' AND ', $condiciones);
    }
    $orden = $categoriaId !== null
        ? ' ORDER BY p.precio ASC, p.id ASC'
        : ' ORDER BY GREATEST(COALESCE(p.modificado, p.agregado), p.agregado) DESC, p.id DESC';
    return ['sql' => $sql, 'orden' => $orden, 'parametros' => $parametros,
        'tiene_palabras' => (bool) $palabras];
}

function catalogo_seleccion(): string
{
    return 'SELECT p.producto, p.uri, p.foto, p.precio, p.stock,
                c.nombre AS categoria, c.uri AS categoria_uri';
}

/** La portada conserva sus novedades; una búsqueda consulta todo el catálogo. */
function catalogo_productos(mysqli $db, string $busqueda = '', ?int $categoriaId = null): mysqli_result
{
    $consulta = catalogo_consulta($busqueda, $categoriaId);
    $sql = catalogo_seleccion() . $consulta['sql'] . $consulta['orden'];
    if (!$consulta['tiene_palabras'] && $categoriaId === null) {
        $sql .= ' LIMIT 25';
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($consulta['parametros']);
    $resultado = $stmt->get_result();
    $stmt->close();
    return $resultado;
}

/** @return array{resultado: mysqli_result, total: int, siguiente: ?int} */
function catalogo_pagina(mysqli $db, string $busqueda = '', ?int $categoriaId = null, int $offset = 0): array
{
    if ($offset < 0) {
        throw new InvalidArgumentException('El desplazamiento del catálogo debe ser positivo o cero.');
    }

    $consulta = catalogo_consulta($busqueda, $categoriaId);
    $stmtTotal = $db->prepare('SELECT COUNT(*) AS total' . $consulta['sql']);
    $stmtTotal->execute($consulta['parametros']);
    $total = (int) $stmtTotal->get_result()->fetch_assoc()['total'];
    $stmtTotal->close();

    $stmt = $db->prepare(catalogo_seleccion() . $consulta['sql'] . $consulta['orden'] . ' LIMIT 25 OFFSET ?');
    $stmt->execute([...$consulta['parametros'], $offset]);
    $resultado = $stmt->get_result();
    $stmt->close();

    $consumidos = $resultado->num_rows;
    return ['resultado' => $resultado, 'total' => $total,
        'siguiente' => $consumidos > 0 && $offset + $consumidos < $total ? $offset + $consumidos : null];
}
