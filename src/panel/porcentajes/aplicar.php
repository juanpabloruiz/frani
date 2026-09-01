<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redireccionar('panel/porcentajes');
}

verificar_CSRF();

$categoriaId = (int) ($_POST['categoria_id'] ?? 0);
$porcentaje = (float) ($_POST['porcentaje'] ?? 0);

if ($categoriaId <= 0) {
    redireccionar('panel/porcentajes');
}

$db = conexion();

$stmt = $db->prepare(
    "UPDATE productos
     SET precio = ROUND(precio * (1 + ? / 100), 2),
         modificado = NOW()
     WHERE id_categoria = ?"
);
$stmt->bind_param('di', $porcentaje, $categoriaId);
$stmt->execute();
$cantAffected = $stmt->affected_rows;
$stmt->close();

respaldar_bd();

$_SESSION['toast_exito'] = "Se actualizaron $cantAffected productos con un {$porcentaje}%.";

redireccionar('panel/porcentajes');
