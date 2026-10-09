<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit;
}

verificar_CSRF();

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Nombre vacío']);
    exit;
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
        $stmt2 = $db->prepare("SELECT id FROM categorias WHERE nombre = ?");
        $stmt2->bind_param('s', $nombre);
        $stmt2->execute();
        $id = (int) $stmt2->get_result()->fetch_assoc()['id'];
        $stmt2->close();
    }
    asignar_uri_categoria($db, $id);
    $db->commit();
} catch (Throwable $error) {
    $db->rollback();
    throw $error;
}

respaldar_bd();

echo json_encode(['ok' => true, 'id' => (int) $id, 'nombre' => $nombre]);
