<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/categorias');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');

if ($id <= 0 || $nombre === '') {
    redireccionar('panel/categorias');
}

$db = conexion();

$db->begin_transaction();
try {
    $stmt = $db->prepare("UPDATE categorias SET nombre = ? WHERE id = ?");
    $stmt->bind_param('si', $nombre, $id);
    $stmt->execute();
    $stmt->close();
    asignar_uri_categoria($db, $id);
    $db->commit();
} catch (Throwable $error) {
    $db->rollback();
    throw $error;
}

respaldar_bd();

redireccionar('panel/categorias#categoria-' . $id);
