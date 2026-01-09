<?php
session_start();

// Define raíz
define('BASE_URL', '/');

// Mostrar errores PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Renombrá este archivo a "conexion.php"
// y completá tus datos reales. Este archivo NO debe subirse al repositorio.
$conexion = new mysqli('localhost', 'usuario', 'contraseña', 'base_de_datos');
$conexion->set_charset('utf8mb4');
