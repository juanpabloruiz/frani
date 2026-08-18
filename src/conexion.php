<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar variables de entorno desde .env
$archivoEnv = __DIR__ . '/../.env';
if (file_exists($archivoEnv)) {
    $lineas = file($archivoEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        $linea = trim($linea);
        if ($linea === '' || $linea[0] === '#') {
            continue;
        }
        if (strpos($linea, '=') !== false) {
            [$clave, $valor] = explode('=', $linea, 2);
            $clave = trim($clave);
            $valor = trim($valor);
            putenv("$clave=$valor");
        }
    }
}

define('BASE_URL', '');

require_once __DIR__ . '/funciones.php';

function conexion(): mysqli
{
    static $db = null;

    if ($db === null) {
        $host = getenv('DB_HOST');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASSWORD');
        $name = getenv('DB_NAME');
        $port = (int) getenv('DB_PORT');

        $db = new mysqli($host, $user, $pass, $name, $port);
        $db->set_charset('utf8mb4');

        if ($db->connect_error) {
            die('Error de conexión: ' . $db->connect_error);
        }
    }

    return $db;
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
