<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/');

function env_value(string $key, ?string $default = null): string
{
    $value = getenv($key);

    if ($value !== false && $value !== '') {
        return $value;
    }

    if ($default !== null) {
        return $default;
    }

    throw new RuntimeException("Falta definir la variable de entorno {$key}.");
}

if (env_value('APP_DEBUG', '0') === '1') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conexion = new mysqli(
    env_value('DB_HOST', 'db'),
    env_value('DB_USER', 'frani'),
    env_value('DB_PASSWORD', 'frani123'),
    env_value('DB_NAME', 'frani'),
    (int) env_value('DB_PORT', '3306')
);

$conexion->set_charset('utf8mb4');
