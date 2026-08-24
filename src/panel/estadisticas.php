<?php
require_once __DIR__ . '/../conexion.php';
requerir_login();

$db = conexion();
$periodo = $_GET['periodo'] ?? 'dia';

header('Content-Type: application/json');

switch ($periodo) {
    case 'dia':
        $resultado = $db->query(
            "SELECT
                DAY(agregado) AS dia,
                CONCAT(
                    CASE DAYOFWEEK(agregado)
                        WHEN 1 THEN 'Dom'
                        WHEN 2 THEN 'Lun'
                        WHEN 3 THEN 'Mar'
                        WHEN 4 THEN 'Mié'
                        WHEN 5 THEN 'Jue'
                        WHEN 6 THEN 'Vie'
                        WHEN 7 THEN 'Sáb'
                    END,
                    ' ',
                    DATE_FORMAT(agregado, '%d')
                ) AS etiqueta,
                COALESCE(SUM(total), 0) AS total,
                COALESCE(SUM(efectivo), 0) AS efectivo,
                COALESCE(SUM(transferencia), 0) AS transferencia
             FROM facturas
             WHERE MONTH(agregado) = MONTH(CURDATE())
               AND YEAR(agregado) = YEAR(CURDATE())
             GROUP BY DAY(agregado), etiqueta
             ORDER BY dia"
        );
        break;

    case 'semana':
        $resultado = $db->query(
            "SELECT
                CASE
                    WHEN DAY(agregado) BETWEEN 1 AND 7 THEN '1 al 7'
                    WHEN DAY(agregado) BETWEEN 8 AND 14 THEN '8 al 14'
                    WHEN DAY(agregado) BETWEEN 15 AND 21 THEN '15 al 21'
                    WHEN DAY(agregado) BETWEEN 22 AND 28 THEN '22 al 28'
                    ELSE '29 al 31'
                END AS etiqueta,
                CASE
                    WHEN DAY(agregado) BETWEEN 1 AND 7 THEN 1
                    WHEN DAY(agregado) BETWEEN 8 AND 14 THEN 2
                    WHEN DAY(agregado) BETWEEN 15 AND 21 THEN 3
                    WHEN DAY(agregado) BETWEEN 22 AND 28 THEN 4
                    ELSE 5
                END AS orden,
                COALESCE(SUM(total), 0) AS total,
                COALESCE(SUM(efectivo), 0) AS efectivo,
                COALESCE(SUM(transferencia), 0) AS transferencia
             FROM facturas
             WHERE MONTH(agregado) = MONTH(CURDATE())
               AND YEAR(agregado) = YEAR(CURDATE())
             GROUP BY orden, etiqueta
             ORDER BY orden"
        );
        break;

    case 'mes':
    default:
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $mesActual = (int) date('n');
        $resultado = $db->query(
            "SELECT
                MONTH(agregado) AS mes,
                COALESCE(SUM(total), 0) AS total,
                COALESCE(SUM(efectivo), 0) AS efectivo,
                COALESCE(SUM(transferencia), 0) AS transferencia
             FROM facturas
             WHERE YEAR(agregado) = YEAR(CURDATE())
             GROUP BY MONTH(agregado)
             ORDER BY mes"
        );
        break;
}

$datos = [];
while ($fila = $resultado->fetch_assoc()) {
    if ($periodo === 'mes') {
        $mesNum = (int) ($fila['mes'] ?? 0);
        $etiqueta = $meses[$mesNum] ?? "Mes $mesNum";
    } else {
        $etiqueta = $fila['etiqueta'] ?? '';
    }

    $datos[] = [
        'etiqueta' => $etiqueta,
        'total' => (float) ($fila['total'] ?? 0),
        'efectivo' => (float) ($fila['efectivo'] ?? 0),
        'transferencia' => (float) ($fila['transferencia'] ?? 0),
    ];
}

echo json_encode($datos);
