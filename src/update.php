<?php
include('pdo.php');

$stmt = $pdo->prepare("UPDATE posts 
                       SET code = ?, title = ?, price = ? 
                       WHERE id = ?");
$stmt->execute([
    $_POST['code'],
    $_POST['title'],
    $_POST['price'],
    $_POST['id']
]);

header("Location: ./");
exit;
