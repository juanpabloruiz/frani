<?php
declare(strict_types=1);

function numero($valor) {
    // Quitar espacios
    $valor = trim($valor);

    // Quitar separador de miles
    $valor = str_replace('.', '', $valor);

    // Cambiar coma decimal a punto
    $valor = str_replace(',', '.', $valor);

    // Validar
    return is_numeric($valor) ? $valor : 0;
}

function mostrar_numero($valor) {
    return number_format($valor, 0, '', '.');
}

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
