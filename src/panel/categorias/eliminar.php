<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redireccionar('panel/categorias');
}

$db = conexion();

$stmt = $db->prepare("DELETE FROM categorias WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

redireccionar('panel/categorias');
