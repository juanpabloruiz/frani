<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/productos');
}

verificar_CSRF();

$producto = trim($_POST['producto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');
$stock = $_POST['stock'] !== '' && $_POST['stock'] !== null ? (int) $_POST['stock'] : null;
$costo = (float) ($_POST['costo'] ?? '0');
$precio = (float) ($_POST['precio'] ?? '0');
$idCategoria = (int) ($_POST['id_categoria'] ?? 0);

if ($producto === '' || $idCategoria <= 0) {
    redireccionar('panel/productos');
}

$db = conexion();
$descripcionDB = $descripcion !== '' ? $descripcion : null;
$observacionesDB = $observaciones !== '' ? $observaciones : null;

$foto = null;
if (!empty($_FILES['foto']['name'])) {
    $directorio = __DIR__ . '/../../img/productos';
    $foto = subir_foto($_FILES['foto'], $directorio);
}

$stmt = $db->prepare(
    "INSERT INTO productos (producto, foto, descripcion, observaciones, costo, precio, stock, id_categoria)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('ssssddii', $producto, $foto, $descripcionDB, $observacionesDB, $costo, $precio, $stock, $idCategoria);
$stmt->execute();
$idNuevo = $stmt->insert_id;
$stmt->close();

respaldar_bd();

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'id' => (int) $idNuevo, 'nombre' => $producto, 'precio' => (int) $precio]);
    exit;
}

redireccionar('panel/productos');
