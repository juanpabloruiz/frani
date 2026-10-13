<?php
require_once __DIR__ . '/../../conexion.php';
require_once __DIR__ . '/../../facebook.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/productos');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$producto = trim($_POST['producto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$stock = $_POST['stock'] !== '' && $_POST['stock'] !== null ? (int) $_POST['stock'] : null;
$costo = (float) ($_POST['costo'] ?? '0');
$precio = (float) ($_POST['precio'] ?? '0');
$idCategoria = (int) ($_POST['id_categoria'] ?? 0);

if ($id <= 0 || $producto === '' || $idCategoria <= 0) {
    redireccionar('panel/productos');
}

$db = conexion();
$descripcionDB = $descripcion !== '' ? $descripcion : null;

$fotoActual = null;
$nuevaFoto = null;
$directorio = __DIR__ . '/../../img/productos';

if (!empty($_FILES['foto']['name'])) {
    $nuevaFoto = subir_foto($_FILES['foto'], $directorio);
}

$db->begin_transaction();
try {
    $antes = facebook_producto_leer($db, $id, true);
    if ($antes === null) {
        throw new RuntimeException('El producto ya no existe.');
    }
    $fotoActual = $antes['foto'];
    $fotoNueva = $nuevaFoto ?? $fotoActual;
    $stmt = $db->prepare(
        "UPDATE productos
        SET producto = ?, foto = ?, descripcion = ?, costo = ?, precio = ?, stock = ?, id_categoria = ?
        WHERE id = ?"
    );
    $stmt->bind_param('sssddiii', $producto, $fotoNueva, $descripcionDB, $costo, $precio, $stock, $idCategoria, $id);
    $stmt->execute();
    $stmt->close();
    asignar_uri_producto($db, $id);
    facebook_encolar_producto($db, $id, 'edicion', $antes);
    $db->commit();
} catch (Throwable $error) {
    $db->rollback();
    if ($nuevaFoto !== null) {
        eliminar_fotos($nuevaFoto, $directorio);
    }
    throw $error;
}

if ($nuevaFoto !== null && $fotoActual !== null) {
    eliminar_fotos($fotoActual, $directorio);
}

respaldar_bd();

redireccionar('panel/productos#producto-' . $id);
