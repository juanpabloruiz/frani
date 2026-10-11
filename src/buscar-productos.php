<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

function error_busqueda(int $estado, string $mensaje): never
{
    http_response_code($estado);
    echo json_encode(['error' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET');
    error_busqueda(405, 'Método no permitido.');
}

$busqueda = $_GET['busqueda'] ?? '';
if (!is_string($busqueda) || preg_match('/\A.{0,200}\z/us', $busqueda) !== 1) {
    error_busqueda(400, 'La búsqueda debe tener hasta 200 caracteres.');
}
$busquedaCatalogo = trim(preg_replace('/\s+/u', ' ', $busqueda));
$categoriaCatalogo = null;
if (array_key_exists('categoria', $_GET)) {
    $categoriaCatalogo = filter_var($_GET['categoria'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($categoriaCatalogo === false) {
        error_busqueda(400, 'Categoría inválida.');
    }
}
$paginado = array_key_exists('offset', $_GET);
$offset = 0;
if ($paginado) {
    $offset = is_string($_GET['offset'])
        ? filter_var($_GET['offset'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]])
        : false;
    if ($offset === false) {
        error_busqueda(400, 'Desplazamiento inválido.');
    }
}

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/catalogo.php';

$nivelBuffer = ob_get_level();
try {
    $db = conexion();
    if ($categoriaCatalogo !== null) {
        $stmt = $db->prepare('SELECT id FROM categorias WHERE id = ?');
        $stmt->execute([$categoriaCatalogo]);
        $existe = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        if (!$existe) {
            error_busqueda(404, 'Categoría no encontrada.');
        }
    }

    if ($paginado) {
        $pagina = catalogo_pagina($db, $busquedaCatalogo, $categoriaCatalogo, $offset);
        $resultado = $pagina['resultado'];
        $total = $pagina['total'];
    } else {
        $resultado = catalogo_productos($db, $busquedaCatalogo, $categoriaCatalogo);
        $total = $resultado->num_rows;
    }
    ob_start();
    if (!$paginado || $offset === 0 || $resultado->num_rows > 0) {
        include __DIR__ . '/_catalogo-productos.php';
    }
    $html = ob_get_clean();
    $respuesta = ['html' => $html, 'total' => $total];
    if ($paginado) {
        $respuesta['siguiente'] = $pagina['siguiente'];
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
} catch (Throwable $error) {
    while (ob_get_level() > $nivelBuffer) {
        ob_end_clean();
    }
    error_log('Error en búsqueda de catálogo: ' . $error->getMessage());
    error_busqueda(503, 'No se pudo buscar. Intentá nuevamente.');
}
