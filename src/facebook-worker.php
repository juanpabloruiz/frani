<?php
declare(strict_types=1);

// Nunca permitir que una URL pública dispare publicaciones.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/facebook.php';
// El worker no usa ni mantiene sesiones web.
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

$opcion = $argv[1] ?? '--once';
try {
    if ($opcion === '--validar') {
        $pagina = facebook_api_pagina(facebook_configuracion());
        fwrite(STDOUT, 'Conexión verificada: ' . $pagina['name'] . ' (ID ' . $pagina['id'] . "). No se publicó contenido.\n");
        exit;
    }
    if ($opcion === '--estado') {
        $db = conexion();
        $totales = $db->query('SELECT estado, COUNT(*) AS cantidad FROM facebook_publicaciones GROUP BY estado')->fetch_all(MYSQLI_ASSOC);
        $ultimas = $db->query('SELECT id, producto_id, pagina_id, tipo, estado, intentos, publicacion_id, ultimo_error
            FROM facebook_publicaciones ORDER BY id DESC LIMIT 20')->fetch_all(MYSQLI_ASSOC);
        fwrite(STDOUT, json_encode(['activado' => facebook_activado(), 'totales' => $totales, 'ultimas' => $ultimas], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n");
        exit;
    }
    if (preg_match('/\A--reintentar=([1-9][0-9]*)\z/', $opcion, $partes)) {
        $stmt = conexion()->prepare('UPDATE facebook_publicaciones SET estado = "pendiente", intentos = 0,
            disponible = CURRENT_TIMESTAMP, ultimo_error = NULL WHERE id = ? AND estado = "fallido"');
        $stmt->execute([(int) $partes[1]]);
        fwrite(STDOUT, $stmt->affected_rows === 1 ? "Publicación preparada para reintentar.\n" : "No hay una publicación fallida con ese ID.\n");
        $stmt->close();
        exit;
    }
    if (!in_array($opcion, ['--once', '--loop'], true)) {
        fwrite(STDERR, "Uso: --validar, --estado, --once, --loop o --reintentar=ID (solo fallidas).\n");
        exit(1);
    }
    do {
        if (facebook_activado()) {
            $resultados = facebook_procesar_cola(conexion(), facebook_configuracion());
            foreach ($resultados as $resultado) {
                fwrite(STDOUT, 'Publicación ' . $resultado['id'] . ': ' . $resultado['estado'] . "\n");
            }
        } elseif ($opcion === '--once') {
            fwrite(STDOUT, "La publicación automática está desactivada.\n");
        }
        if ($opcion === '--loop') {
            sleep(15);
        }
    } while ($opcion === '--loop');
} catch (Throwable $error) {
    // No mostrar excepciones que puedan contener credenciales o datos de transporte.
    fwrite(STDERR, "No se pudo completar el proceso de Facebook. Revisar la configuración, la conexión y la migración.\n");
    exit(1);
}
