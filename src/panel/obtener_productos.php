<?php
require_once __DIR__ . '/../conexion.php';

$db = conexion();
$resultado = $db->query("SELECT id, producto, precio, stock FROM productos ORDER BY producto ASC");
$productos = [];

while ($row = $resultado->fetch_assoc()) {
    $productos[] = [
        'id' => (int) $row['id'],
        'producto' => $row['producto'],
        'precio' => (float) $row['precio'],
        'stock' => $row['stock'] !== null ? (int) $row['stock'] : null,
    ];
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($productos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
