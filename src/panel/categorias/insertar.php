<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/categorias');
}

verificar_CSRF();

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre === '') {
    redireccionar('panel/categorias');
}

$db = conexion();

$db->begin_transaction();
try {
    $stmt = $db->prepare("INSERT IGNORE INTO categorias (nombre) VALUES (?)");
    $stmt->bind_param('s', $nombre);
    $stmt->execute();
    $id = (int) $stmt->insert_id;
    $stmt->close();

    if ($id === 0) {
        $stmt = $db->prepare("SELECT id FROM categorias WHERE nombre = ?");
        $stmt->bind_param('s', $nombre);
        $stmt->execute();
        $id = (int) $stmt->get_result()->fetch_assoc()['id'];
        $stmt->close();
    }
    asignar_uri_categoria($db, $id);
    $db->commit();
} catch (Throwable $error) {
    $db->rollback();
    throw $error;
}

respaldar_bd();

redireccionar('panel/categorias');
