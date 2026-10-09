<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../src/conexion.php';

$db = conexion();
$bloqueo = $db->query("SELECT GET_LOCK('frani_categorias_uri', 10) AS adquirido")->fetch_assoc();
if ((int) $bloqueo['adquirido'] !== 1) {
    fwrite(STDERR, "Otra migración de categorías está en curso. Reintentar más tarde.\n");
    exit(1);
}

try {
    $db->query('ALTER TABLE categorias ADD COLUMN IF NOT EXISTS uri VARCHAR(200) DEFAULT NULL AFTER nombre');
    $db->query('ALTER TABLE categorias ADD UNIQUE INDEX IF NOT EXISTS uk_categorias_uri (uri)');
    $db->begin_transaction();
    try {
        $cantidad = completar_uri_categorias($db);
        $db->commit();
    } catch (Throwable $error) {
        $db->rollback();
        throw $error;
    }
    fwrite(STDOUT, "Migración completada: {$cantidad} URI de categorías generadas.\n");
} finally {
    $db->query("SELECT RELEASE_LOCK('frani_categorias_uri')");
}
