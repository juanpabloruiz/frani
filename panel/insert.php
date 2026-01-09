<?php
include('pdo.php');

$stmt = $pdo->prepare("INSERT INTO posts (code, title, price, created) VALUES (?, ?, ?, NOW())");
$stmt->execute([
    $_POST['code'],
    $_POST['title'],
    $_POST['price']
]);

header("Location: ./");
exit;
