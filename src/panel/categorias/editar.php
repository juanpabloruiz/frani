<?php
require_once __DIR__ . '/../../conexion.php';
requerir_login();

$id = (int) ($_GET['id'] ?? 0);
redireccionar('panel/categorias' . ($id > 0 ? '?id=' . $id : ''));
