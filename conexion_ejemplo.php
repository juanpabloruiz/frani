<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cambiar los datos para la conexión
$conexion = mysqli_connect('localhost', 'usuario', 'clave.', 'basededatos');

mysqli_set_charset($conexion, 'utf8');
