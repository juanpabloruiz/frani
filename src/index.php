<?php

include('conexion.php');

$stmt = $pdo->prepare("SELECT codigo, producto, precio FROM productos WHERE precio > ?");
$stmt->execute([0]);

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($productos as $p) {
    echo "{$p['codigo']} - {$p['producto']} - $ {$p['precio']}<br>";
}
