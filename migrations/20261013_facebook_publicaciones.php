<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__ . '/../src/conexion.php';

conexion()->query(file_get_contents(__DIR__ . '/20261013_facebook_publicaciones.sql'));
fwrite(STDOUT, "Tabla de publicaciones de Facebook preparada. No se encolaron productos anteriores.\n");
