<?php
declare(strict_types=1);

// Ejecutar por CLI: nunca exponer una migración como página pública.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../src/conexion.php';

$db = conexion();
$bloqueo = $db->query("SELECT GET_LOCK('frani_productos_uri', 10) AS adquirido")->fetch_assoc();
if ((int) $bloqueo['adquirido'] !== 1) {
    fwrite(STDERR, "Otra migración de URI está en curso. Reintentar más tarde.\n");
    exit(1);
}

try {
    // NULL permite incorporar el campo y asignarlo dentro de la transacción del alta.
    $db->query('ALTER TABLE productos ADD COLUMN IF NOT EXISTS uri VARCHAR(200) DEFAULT NULL AFTER producto');
    $db->query('ALTER TABLE productos ADD UNIQUE INDEX IF NOT EXISTS uk_productos_uri (uri)');
    $db->begin_transaction();
    try {
        $cantidad = completar_uri_productos($db);
        $db->commit();
    } catch (Throwable $error) {
        $db->rollback();
        throw $error;
    }
    fwrite(STDOUT, "Migración completada: {$cantidad} URI generadas.\n");
} finally {
    $db->query("SELECT RELEASE_LOCK('frani_productos_uri')");
}
