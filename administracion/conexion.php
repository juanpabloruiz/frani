<?php
session_start();
$conexion = new mysqli('localhost', 'soledad', 'Rufus/80', 'soledad_db');
mysqli_set_charset($conexion, 'utf8');
//$donweb = new mysqli('200.58.126.3', 'soledad', 'Rufus/80', 'soledad_db');
//mysqli_set_charset($donweb, 'utf8');
?>