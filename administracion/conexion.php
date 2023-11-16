<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$conexion = new mysqli('localhost', 'soledad', 'Rufus/80', 'soledad_db');
mysqli_set_charset($conexion, 'utf8');
?>