<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '');

function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function base_path(string $ruta = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($ruta, '/');

    if ($base === '') {
        return '/' . $path;
    }

    return $path === '' ? $base . '/' : $base . '/' . $path;
}

function redireccionar(string $ruta = ''): void
{
    header('Location: ' . base_path($ruta));
    exit;
}

function numero($valor) {
    $valor = trim($valor);
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return is_numeric($valor) ? $valor : 0;
}
