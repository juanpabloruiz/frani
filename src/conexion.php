<?php
require_once __DIR__ . '/funciones.php';

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
