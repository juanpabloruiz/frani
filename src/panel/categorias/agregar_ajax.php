<?php
require_once __DIR__ . '/../../conexion.php';
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

$stmt = $db->prepare("INSERT IGNORE INTO categorias (nombre) VALUES (?)");
$stmt->bind_param('s', $nombre);
$stmt->execute();
$id = $stmt->insert_id;
$stmt->close();

if ($id === 0) {
    $stmt2 = $db->prepare("SELECT id FROM categorias WHERE nombre = ?");
    $stmt2->bind_param('s', $nombre);
    $stmt2->execute();
    $id = $stmt2->get_result()->fetch_assoc()['id'];
    $stmt2->close();
}

respaldar_bd();

echo json_encode(['ok' => true, 'id' => (int) $id, 'nombre' => $nombre]);
