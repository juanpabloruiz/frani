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

/** Botones de paginación reutilizables: HTML servidor y respuesta de búsqueda. */
function catalogo_paginacion_html(int $pagina, int $total, int $porPagina): string
{
    $paginas = max(1, (int) ceil($total / max(1, $porPagina)));
    $pagina = min(max(1, $pagina), $paginas);

    $html = '<ul class="pagination pagination-sm flex-wrap justify-content-center mb-0">';
    if ($pagina <= 1) {
        $html .= '<li class="page-item disabled"><span class="page-link" aria-label="Página anterior">&#8249;</span></li>';
    } else {
        $anterior = $pagina - 1;
        $html .= '<li class="page-item"><a class="page-link" href="?pagina=' . $anterior
            . '" data-pagina="' . $anterior . '" aria-controls="productosCatalogo"'
            . ' aria-label="Página anterior">&#8249;</a></li>';
    }

    $inicio = max(1, $pagina - 2);
    $fin = min($paginas, $inicio + 4);
    $inicio = max(1, $fin - 4);

    if ($inicio > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="?pagina=1" data-pagina="1"'
            . ' aria-controls="productosCatalogo">1</a></li>';
        if ($inicio > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
    }
    for ($numero = $inicio; $numero <= $fin; $numero++) {
        if ($numero === $pagina) {
            $html .= '<li class="page-item active"><span class="page-link" aria-current="page">'
                . $numero . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="?pagina=' . $numero
                . '" data-pagina="' . $numero . '" aria-controls="productosCatalogo">' . $numero . '</a></li>';
        }
    }
    if ($fin < $paginas) {
        if ($fin < $paginas - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="?pagina=' . $paginas
            . '" data-pagina="' . $paginas . '" aria-controls="productosCatalogo">' . $paginas . '</a></li>';
    }

    if ($pagina >= $paginas) {
        $html .= '<li class="page-item disabled"><span class="page-link" aria-label="Página siguiente">&#8250;</span></li>';
    } else {
        $siguiente = $pagina + 1;
        $html .= '<li class="page-item"><a class="page-link" href="?pagina=' . $siguiente
            . '" data-pagina="' . $siguiente . '" aria-controls="productosCatalogo"'
            . ' aria-label="Página siguiente">&#8250;</a></li>';
    }
    $html .= '</ul>';
    return $html;
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
function catalogo_pagina(mysqli $db, string $busqueda = '', ?int $categoriaId = null, int $offset = 0, int $porPagina = 25): array
{
    if ($offset < 0) {
        throw new InvalidArgumentException('El desplazamiento del catálogo debe ser positivo o cero.');
    }
    if ($porPagina < 1 || $porPagina > 100) {
        throw new InvalidArgumentException('El tamaño de página del catálogo debe estar entre 1 y 100.');
    }

    $consulta = catalogo_consulta($busqueda, $categoriaId);
    $stmtTotal = $db->prepare('SELECT COUNT(*) AS total' . $consulta['sql']);
    $stmtTotal->execute($consulta['parametros']);
    $total = (int) $stmtTotal->get_result()->fetch_assoc()['total'];
    $stmtTotal->close();

    $stmt = $db->prepare(catalogo_seleccion() . $consulta['sql'] . $consulta['orden'] . ' LIMIT ? OFFSET ?');
    $stmt->execute([...$consulta['parametros'], $porPagina, $offset]);
    $resultado = $stmt->get_result();
    $stmt->close();

    $consumidos = $resultado->num_rows;
    return ['resultado' => $resultado, 'total' => $total,
        'siguiente' => $consumidos > 0 && $offset + $consumidos < $total ? $offset + $consumidos : null];
}
