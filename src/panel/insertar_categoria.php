<?php
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../funciones.php';

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre !== '') {
    $sentencia = $conexion->prepare("INSERT IGNORE INTO categorias (nombre) VALUES (?)");
    $sentencia->bind_param('s', $nombre);
    $sentencia->execute();
    $sentencia->close();
}

redireccionar('panel.php');
