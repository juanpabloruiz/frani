<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/productos');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$producto = trim($_POST['producto'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');
$stock = $_POST['stock'] !== '' && $_POST['stock'] !== null ? (int) $_POST['stock'] : null;
$costo = (float) ($_POST['costo'] ?? '0');
$precio = (float) ($_POST['precio'] ?? '0');
$idCategoria = (int) ($_POST['id_categoria'] ?? 0);

if ($id <= 0 || $producto === '' || $idCategoria <= 0) {
    redireccionar('panel/productos');
}

$db = conexion();
$descripcionDB = $descripcion !== '' ? $descripcion : null;
$observacionesDB = $observaciones !== '' ? $observaciones : null;

$fotoActual = null;
$stmtFoto = $db->prepare("SELECT foto FROM productos WHERE id = ?");
$stmtFoto->bind_param('i', $id);
$stmtFoto->execute();
$stmtFoto->bind_result($fotoActual);
$stmtFoto->fetch();
$stmtFoto->close();

$fotoNueva = $fotoActual;

if (!empty($_FILES['foto']['name'])) {
    $directorio = __DIR__ . '/../../img/productos';
    $nuevaFoto = subir_foto($_FILES['foto'], $directorio);
    if ($nuevaFoto !== null) {
        if ($fotoActual !== null) {
            eliminar_fotos($fotoActual, $directorio);
        }
        $fotoNueva = $nuevaFoto;
    }
}

$stmt = $db->prepare(
    "UPDATE productos
    SET producto = ?, foto = ?, descripcion = ?, observaciones = ?, costo = ?, precio = ?, stock = ?, id_categoria = ?
    WHERE id = ?"
);
$stmt->bind_param('ssssddiii', $producto, $fotoNueva, $descripcionDB, $observacionesDB, $costo, $precio, $stock, $idCategoria, $id);
$stmt->execute();
$stmt->close();

respaldar_bd();

redireccionar('panel/productos#producto-' . $id);
