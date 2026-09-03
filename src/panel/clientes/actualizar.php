<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/clientes');
}

verificar_CSRF();

$id = (int) ($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if ($id <= 0 || $nombre === '') {
    redireccionar('panel/clientes');
}

$db = conexion();
$telefonoDB = $telefono !== '' ? $telefono : null;

$fotoActual = null;
$stmtFoto = $db->prepare("SELECT foto FROM clientes WHERE id = ?");
$stmtFoto->bind_param('i', $id);
$stmtFoto->execute();
$stmtFoto->bind_result($fotoActual);
$stmtFoto->fetch();
$stmtFoto->close();

$fotoNueva = $fotoActual;

if (!empty($_FILES['foto']['name'])) {
    $directorio = __DIR__ . '/../../img/clientes';
    $nuevaFoto = subir_foto($_FILES['foto'], $directorio);
    if ($nuevaFoto !== null) {
        if ($fotoActual !== null) {
            eliminar_fotos($fotoActual, $directorio);
        }
        $fotoNueva = $nuevaFoto;
    }
}

$stmt = $db->prepare(
    "UPDATE clientes
    SET nombre = ?, telefono = ?, foto = ?
    WHERE id = ?"
);
$stmt->bind_param('sssi', $nombre, $telefonoDB, $fotoNueva, $id);
$stmt->execute();
$stmt->close();

respaldar_bd();

redireccionar('panel/clientes#cliente-' . $id);
