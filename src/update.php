<?php
include('pdo.php');

$stmt = $pdo->prepare("UPDATE productos 
                       SET codigo = ?, producto = ?, precio = ? 
                       WHERE id = ?");
$stmt->execute([
    $_POST['codigo'],
    $_POST['producto'],
    $_POST['precio'],
    $_POST['id']
]);

header("Location: ./");
exit;
