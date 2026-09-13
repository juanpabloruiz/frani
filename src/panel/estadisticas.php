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
                WEEKDAY(agregado) AS indice,
                COALESCE(SUM(efectivo), 0) + COALESCE(SUM(transferencia), 0) AS total,
                COALESCE(SUM(efectivo), 0) AS efectivo,
                COALESCE(SUM(transferencia), 0) AS transferencia,
                COALESCE(SUM(deuda), 0) AS deuda
             FROM facturas
             WHERE agregado >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
               AND agregado < DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY)
             GROUP BY indice
             ORDER BY indice"
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
                COALESCE(SUM(efectivo), 0) + COALESCE(SUM(transferencia), 0) AS total,
                COALESCE(SUM(efectivo), 0) AS efectivo,
                COALESCE(SUM(transferencia), 0) AS transferencia,
                COALESCE(SUM(deuda), 0) AS deuda
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
                COALESCE(SUM(efectivo), 0) + COALESCE(SUM(transferencia), 0) AS total,
                COALESCE(SUM(efectivo), 0) AS efectivo,
                COALESCE(SUM(transferencia), 0) AS transferencia,
                COALESCE(SUM(deuda), 0) AS deuda
             FROM facturas
             WHERE YEAR(agregado) = YEAR(CURDATE())
             GROUP BY MONTH(agregado)
             ORDER BY mes"
        );
        break;
}

$datos = [];

if ($periodo === 'dia') {
    $fechaLunes = new DateTimeImmutable('monday this week');
    $nombres = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

    foreach (range(0, 6) as $i) {
        $datos[$i] = [
            'etiqueta' => $nombres[$i] . ' ' . $fechaLunes->modify("+$i days")->format('d/m'),
            'total' => 0.0,
            'efectivo' => 0.0,
            'transferencia' => 0.0,
            'deuda' => 0.0,
        ];
    }

    while ($fila = $resultado->fetch_assoc()) {
        $i = (int) ($fila['indice'] ?? 0);
        if (isset($datos[$i])) {
            $datos[$i]['total'] = (float) ($fila['total'] ?? 0);
            $datos[$i]['efectivo'] = (float) ($fila['efectivo'] ?? 0);
            $datos[$i]['transferencia'] = (float) ($fila['transferencia'] ?? 0);
            $datos[$i]['deuda'] = (float) ($fila['deuda'] ?? 0);
        }
    }
} else {
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
            'deuda' => (float) ($fila['deuda'] ?? 0),
        ];
    }
}

echo json_encode($datos);
