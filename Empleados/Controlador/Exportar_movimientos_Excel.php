<?php
require_once '../../api/db/conexion.php';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="movimientos_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Obtener filtros
$filtro_fecha = $_GET['filtro_fecha'] ?? 'hoy';
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$id_personal = $_GET['id_personal'] ?? '';
$id_ubicacion = $_GET['id_ubicacion'] ?? '';
$tipo_movimiento = $_GET['tipo_movimiento'] ?? '';
$id_personal_especifico = $_GET['id_personal_especifico'] ?? '';

function formatFechaExcel($fecha) {
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
    // Construir consulta
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
                t1.tiempo as Tiempo,
                CASE 
                    WHEN t1.FechaEntrada IS NOT NULL AND CONVERT(VARCHAR, t1.FechaEntrada) NOT LIKE '%1900-01-01%' 
                    THEN 'Con Entrada' 
                    ELSE 'Sin Entrada' 
                END as EstadoEntrada,
                CASE 
                    WHEN t1.FechaSalida IS NOT NULL AND CONVERT(VARCHAR, t1.FechaSalida) NOT LIKE '%1900-01-01%' 
                    THEN 'Con Salida' 
                    ELSE 'Sin Salida' 
                END as EstadoSalida
            FROM regentsalper as t1 
            LEFT JOIN t_ubicacion_interna as t5 ON t5.IdUbicacion = t1.IdUbicacion 
            INNER JOIN t_personal as t2 ON t1.IdPer = t2.IdPersonal
            WHERE t2.status = 1";
    
    $params = [];
    
    // Aplicar filtros
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
    
    // Generar archivo Excel (HTML)
    echo '<html>';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<title>Movimientos</title>';
    echo '<style>';
    echo 'th { background-color: #d94f00; color: white; font-weight: bold; text-align: center; padding: 8px; }';
    echo 'td { padding: 6px; border: 1px solid #ddd; }';
    echo '.text-center { text-align: center; }';
    echo '.badge-entrada { background-color: #28a745; color: white; padding: 3px 6px; border-radius: 3px; }';
    echo '.badge-salida { background-color: #dc3545; color: white; padding: 3px 6px; border-radius: 3px; }';
    echo '.badge-pendiente { background-color: #ffc107; color: black; padding: 3px 6px; border-radius: 3px; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    echo '<h2 style="color: #d94f00; text-align: center;">Reporte de Movimientos</h2>';
    echo '<p style="text-align: center; font-size: 12px;">Generado: ' . date('d/m/Y H:i:s') . '</p>';
    
    // Mostrar filtros aplicados
    echo '<div style="margin-bottom: 20px; padding: 10px; background-color: #f5f5f5; border-radius: 5px;">';
    echo '<h4>Filtros Aplicados:</h4>';
    echo '<ul>';
    echo '<li>Período: ' . ucfirst($filtro_fecha) . '</li>';
    if ($filtro_fecha == 'personalizado') {
        echo '<li>Desde: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' Hasta: ' . date('d/m/Y', strtotime($fecha_fin)) . '</li>';
    }
    if (!empty($id_personal_especifico)) {
        echo '<li>ID Personal: ' . htmlspecialchars($id_personal_especifico) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    
    // Tabla de datos
    echo '<table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 100%; font-size: 12px;">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Personal</th>';
    echo '<th>Código</th>';
    echo '<th>Ubicación</th>';
    echo '<th>Folio Entrada</th>';
    echo '<th>Fecha Entrada</th>';
    echo '<th>Estado Entrada</th>';
    echo '<th>Folio Salida</th>';
    echo '<th>Fecha Salida</th>';
    echo '<th>Estado Salida</th>';
    echo '<th>Tiempo</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $total_entradas = 0;
    $total_salidas = 0;
    
    foreach ($rows as $row) {
        $fechaEntrada = formatFechaExcel($row->FechaEntrada);
        $fechaSalida = formatFechaExcel($row->FechaSalida);
        $tieneEntrada = ($row->FolMovEnt > 0 && $fechaEntrada != 'No registrado');
        $tieneSalida = ($row->FolMovSal > 0 && $fechaSalida != 'No registrado');
        
        if ($tieneEntrada) $total_entradas++;
        if ($tieneSalida) $total_salidas++;
        
        echo '<tr>';
        echo '<td class="text-center">' . htmlspecialchars($row->IdMovEntSal) . '</td>';
        echo '<td>' . htmlspecialchars($row->Personal) . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row->CodigoPersonal) . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($row->Ubicacion) . '</td>';
        echo '<td class="text-center">' . ($row->FolMovEnt > 0 ? $row->FolMovEnt : '-') . '</td>';
        echo '<td class="text-center">' . $fechaEntrada . '</td>';
        echo '<td class="text-center">' . ($tieneEntrada ? 'Con Entrada' : 'Sin Entrada') . '</td>';
        echo '<td class="text-center">' . ($row->FolMovSal > 0 ? $row->FolMovSal : '-') . '</td>';
        echo '<td class="text-center">' . $fechaSalida . '</td>';
        echo '<td class="text-center">' . ($tieneSalida ? 'Con Salida' : 'Sin Salida') . '</td>';
        echo '<td class="text-center">' . ($row->Tiempo != '00:00:00' ? $row->Tiempo : '-') . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    
    // Resumen
    echo '<div style="margin-top: 20px; padding: 10px; background-color: #f5f5f5; border-radius: 5px;">';
    echo '<h4>Resumen:</h4>';
    echo '<p>Total de registros: ' . count($rows) . '</p>';
    echo '<p>Total de entradas: ' . $total_entradas . '</p>';
    echo '<p>Total de salidas: ' . $total_salidas . '</p>';
    echo '</div>';
    
    echo '</body>';
    echo '</html>';
    
} catch (PDOException $e) {
    echo 'Error al generar el reporte: ' . $e->getMessage();
}
?>