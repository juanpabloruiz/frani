<?php
declare(strict_types=1);

/** La portada conserva sus novedades; una búsqueda consulta todo el catálogo. */
function catalogo_productos(mysqli $db, string $busqueda = '', ?int $categoriaId = null): mysqli_result
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

    $sql = 'SELECT p.producto, p.uri, p.foto, p.precio, p.stock,
                c.nombre AS categoria, c.uri AS categoria_uri
            FROM productos p INNER JOIN categorias c ON c.id = p.id_categoria';
    if ($condiciones) {
        $sql .= ' WHERE ' . implode(' AND ', $condiciones);
    }
    $sql .= $categoriaId !== null
        ? ' ORDER BY p.precio ASC, p.id ASC'
        : ' ORDER BY GREATEST(COALESCE(p.modificado, p.agregado), p.agregado) DESC, p.id DESC';
    if (!$palabras && $categoriaId === null) {
        $sql .= ' LIMIT 25';
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($parametros);
    $resultado = $stmt->get_result();
    $stmt->close();
    return $resultado;
}
