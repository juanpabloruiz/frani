<?php
include('pdo.php');

$stmt = $pdo->prepare("INSERT INTO productos (codigo, producto, precio, creado) VALUES (?, ?, ?, NOW())");
$stmt->execute([
    $_POST['codigo'],
    $_POST['producto'],
    $_POST['precio']
]);

header("Location: ./");
exit;
