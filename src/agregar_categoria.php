<?php
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=UTF-8');

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre === '') {
    echo json_encode(['ok' => false, 'error' => 'El nombre no puede estar vacío.']);
    exit;
}

$sentencia = $conexion->prepare("INSERT IGNORE INTO categorias (nombre) VALUES (?)");
$sentencia->bind_param('s', $nombre);
$sentencia->execute();

if ($sentencia->affected_rows > 0) {
    $id = $sentencia->insert_id;
    $sentencia->close();
    echo json_encode(['ok' => true, 'id' => (int) $id, 'nombre' => $nombre]);
} else {
    $sentencia->close();
    $existe = $conexion->query("SELECT id, nombre FROM categorias WHERE nombre = '" . $conexion->real_escape_string($nombre) . "' LIMIT 1");
    $fila = $existe->fetch_assoc();
    echo json_encode(['ok' => true, 'id' => (int) $fila['id'], 'nombre' => $fila['nombre']]);
}
