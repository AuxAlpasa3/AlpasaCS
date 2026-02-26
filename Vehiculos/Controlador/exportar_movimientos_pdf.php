<?php
require_once '../../api/db/conexion.php';
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$filtro_fecha = $_GET['filtro_fecha'] ?? 'hoy';
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$id_personal = $_GET['id_personal'] ?? '';
$id_ubicacion = $_GET['id_ubicacion'] ?? '';
$tipo_movimiento = $_GET['tipo_movimiento'] ?? '';
$id_personal_especifico = $_GET['id_personal_especifico'] ?? '';

function formatFechaPDF($fecha) {
    if (empty($fecha) || strpos($fecha, '1900-01-01') !== false) {
        return 'No registrado';
    }
    try {
        $date = new DateTime($fecha);
        return $date->format('d/m/Y H:i:s');
    } catch (Exception $e) {
        return 'Fecha inválida';
    }
}

try {
    $sql = "SELECT 
                t1.IdMovEntSal,
                CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as Personal,
                t2.IdPersonal as CodigoPersonal,
                CASE 
                    WHEN t1.IdUbicacion = 0 OR t1.IdUbicacion IS NULL THEN 'Sin Ubicación' 
                    ELSE t5.NomCorto 
                END as Ubicacion,
                t1.FolMovEnt,
                t1.FechaEntrada,
                t1.FolMovSal,
                t1.FechaSalida,
                t1.tiempo as Tiempo
            FROM regentsalper as t1 
            LEFT JOIN t_ubicacion_interna as t5 ON t5.IdUbicacion = t1.IdUbicacion 
            INNER JOIN t_personal as t2 ON t1.IdPer = t2.IdPersonal
            WHERE t2.status = 1";
    
    $params = [];
    
    switch($filtro_fecha) {
        case 'hoy':
            $sql .= " AND CONVERT(DATE, t1.FechaEntrada) = CONVERT(DATE, GETDATE())";
            break;
        case 'ayer':
            $sql .= " AND CONVERT(DATE, t1.FechaEntrada) = DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))";
            break;
        case 'semana':
            $sql .= " AND DATEPART(WEEK, t1.FechaEntrada) = DATEPART(WEEK, GETDATE()) 
                      AND DATEPART(YEAR, t1.FechaEntrada) = DATEPART(YEAR, GETDATE())";
            break;
        case 'mes':
            $sql .= " AND MONTH(t1.FechaEntrada) = MONTH(GETDATE()) 
                      AND YEAR(t1.FechaEntrada) = YEAR(GETDATE())";
            break;
        case 'personalizado':
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                $sql .= " AND CONVERT(DATE, t1.FechaEntrada) BETWEEN ? AND ?";
                $params[] = $fecha_inicio;
                $params[] = $fecha_fin;
            }
            break;
    }
    
    if (!empty($id_personal)) {
        $sql .= " AND t2.IdPersonal = ?";
        $params[] = $id_personal;
    }
    
    if (!empty($id_personal_especifico)) {
        $sql .= " AND t2.IdPersonal LIKE ?";
        $params[] = '%' . $id_personal_especifico . '%';
    }
    
    if (!empty($id_ubicacion)) {
        $sql .= " AND t1.IdUbicacion = ?";
        $params[] = $id_ubicacion;
    }
    
    if (!empty($tipo_movimiento)) {
        if ($tipo_movimiento == 'entrada') {
            $sql .= " AND t1.FechaEntrada IS NOT NULL 
                      AND CONVERT(VARCHAR, t1.FechaEntrada) NOT LIKE '%1900-01-01%'";
        } elseif ($tipo_movimiento == 'salida') {
            $sql .= " AND t1.FechaSalida IS NOT NULL 
                      AND CONVERT(VARCHAR, t1.FechaSalida) NOT LIKE '%1900-01-01%'";
        }
    }
    
    $sql .= " ORDER BY t1.IdMovEntSal DESC";
    
    $stmt = $Conexion->prepare($sql);
    
    if (!empty($params)) {
        foreach($params as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
    }
    
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $total_entradas = 0;
    $total_salidas = 0;
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Movimientos</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10pt; }
            h1 { color: #d94f00; text-align: center; font-size: 18pt; }
            .fecha { text-align: center; color: #666; margin-bottom: 20px; font-size: 9pt; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #d94f00; color: white; padding: 8px; text-align: center; font-weight: bold; font-size: 9pt; }
            td { padding: 6px; border: 1px solid #ddd; text-align: center; font-size: 8pt; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .resumen { margin-top: 20px; padding: 10px; background-color: #f5f5f5; border-radius: 5px; }
            .resumen h3 { color: #d94f00; margin-top: 0; font-size: 11pt; }
            .text-left { text-align: left; }
        </style>
    </head>
    <body>
        <h1>Reporte de Movimientos</h1>
        <div class="fecha">Generado: ' . date('d/m/Y H:i:s') . '</div>';
    
    $html .= '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Personal</th>';
    $html .= '<th>Código</th>';
    $html .= '<th>Ubicación</th>';
    $html .= '<th>Folio Ent</th>';
    $html .= '<th>Fecha Entrada</th>';
    $html .= '<th>Folio Sal</th>';
    $html .= '<th>Fecha Salida</th>';
    $html .= '<th>Tiempo</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    foreach ($rows as $row) {
        $fechaEntrada = formatFechaPDF($row->FechaEntrada);
        $fechaSalida = formatFechaPDF($row->FechaSalida);
        $tieneEntrada = ($row->FolMovEnt > 0 && $fechaEntrada != 'No registrado');
        $tieneSalida = ($row->FolMovSal > 0 && $fechaSalida != 'No registrado');
        
        if ($tieneEntrada) $total_entradas++;
        if ($tieneSalida) $total_salidas++;
        
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row->IdMovEntSal) . '</td>';
        $html .= '<td class="text-left">' . htmlspecialchars($row->Personal) . '</td>';
        $html .= '<td>' . htmlspecialchars($row->CodigoPersonal) . '</td>';
        $html .= '<td>' . htmlspecialchars($row->Ubicacion) . '</td>';
        $html .= '<td>' . ($row->FolMovEnt > 0 ? $row->FolMovEnt : '-') . '</td>';
        $html .= '<td>' . $fechaEntrada . '</td>';
        $html .= '<td>' . ($row->FolMovSal > 0 ? $row->FolMovSal : '-') . '</td>';
        $html .= '<td>' . $fechaSalida . '</td>';
        $html .= '<td>' . ($row->Tiempo != '00:00:00' ? $row->Tiempo : '-') . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    
    $html .= '<div class="resumen">';
    $html .= '<h3>Resumen:</h3>';
    $html .= '<p>Total de registros: ' . count($rows) . '</p>';
    $html .= '<p>Total de entradas: ' . $total_entradas . '</p>';
    $html .= '<p>Total de salidas: ' . $total_salidas . '</p>';
    $html .= '</div>';
    
    $html .= '</body></html>';
    
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Arial');
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $dompdf->stream("movimientos_" . date('Y-m-d_H-i-s') . ".pdf", array("Attachment" => true));
    
} catch (PDOException $e) {
    echo 'Error al generar el PDF: ' . $e->getMessage();
}
?>