<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '');

function conexion(): mysqli
{
    static $db = null;

    if ($db === null) {
        $db = new mysqli('db', 'frani', 'frani123', 'frani', 3306);
        $db->set_charset('utf8mb4');

        if ($db->connect_error) {
            die('Error de conexión: ' . $db->connect_error);
        }
    }

    return $db;
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

function numero($valor) {
    $valor = trim($valor);
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return is_numeric($valor) ? $valor : 0;
}

function requerir_login(): void
{
    if (empty($_SESSION['usuario_id'])) {
        redireccionar('panel');
    }
}

function usuario_actual(): ?array
{
    if (empty($_SESSION['usuario_id'])) {
        return null;
    }

    $db = conexion();
    $stmt = $db->prepare("SELECT id, nombre, correo, foto FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['usuario_id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    $stmt->close();

    return $usuario;
}

function CSRF_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function CSRF_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(CSRF_token()) . '">';
}

function verificar_CSRF(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            die('Token CSRF inválido.');
        }
    }
}
