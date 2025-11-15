<?php
include('conexion.php');

$codigo = $_POST['codigo'];
$producto = $_POST['producto'];
$precio = $_POST['precio'];

$stmt = $pdo->prepare(
    "INSERT INTO productos (codigo, producto, precio) VALUES (?, ?, ?)"
);

$stmt->execute([$codigo, $producto, $precio]);
