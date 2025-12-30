<?php

include '../../api/db/conexion.php';

$filtro_fecha = $_GET['filtro_fecha'] ?? 'hoy';
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$id_personal = $_GET['id_personal'] ?? '';
$id_ubicacion = $_GET['id_ubicacion'] ?? '';
$tipo_movimiento = $_GET['tipo_movimiento'] ?? '';
$id_personal_especifico = $_GET['id_personal_especifico'] ?? '';

function formatSqlServerDate($dateValue) {
    if ($dateValue === null || $dateValue === '') {
        return 'No existe un movimiento';
    }
    
    $dateStr = (string)$dateValue;
    if (strpos($dateStr, '1900-01-01') !== false || 
        strpos($dateStr, '0000-00-00') !== false) {
        return 'No existe un movimiento';
    }
    
    try {
        $date = new DateTime($dateStr);
        return $date->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        return 'Fecha inválida';
    }
}

try {
    // Construir la consulta SQL base
    $sql = "SELECT 
                t1.IdMov,
                CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as Personal,
                t2.IdPersonal as CodigoPersonal,
                CASE 
                    WHEN t1.IdUbicacion = 0 THEN 'SinUbicacion' 
                    ELSE t5.NomCorto 
                END as NomCorto,
                t1.IdUbicacion,
                t1.FolMovEnt,
                t1.FolMovEnt as MovEnt,
                t1.FechaEntrada,
                t1.FolMovSal,
                t1.FolMovSal as MovSal,
                t1.FechaSalida,
                t1.tiempo as Tiempo
            FROM regentsalper as t1 
            LEFT JOIN t_ubicacion as t5 ON t5.IdUbicacion = t1.IdUbicacion 
            INNER JOIN t_personal as t2 ON t1.IdPer = t2.IdPersonal
            LEFT JOIN regentper as t4 ON t1.FolMovEnt = t4.FolMov
            LEFT JOIN regsalper as t6 ON t1.FolMovSal = t6.FolMov
            WHERE t2.tipoPersonal = 1";
    
    // Aplicar filtros de fecha (basados en FechaEntrada)
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
                $sql .= " AND CONVERT(DATE, t1.FechaEntrada) BETWEEN :fecha_inicio AND :fecha_fin";
            }
            break;
    }
    
    // Filtro por ID de personal (t2.IdPersonal)
    if (!empty($id_personal)) {
        $sql .= " AND t2.IdPersonal = :id_personal";
    }
    
    // Filtro por ID personal específico (búsqueda en código)
    if (!empty($id_personal_especifico)) {
        $sql .= " AND t2.IdPersonal LIKE :id_personal_especifico";
    }
    
    // Filtro por ubicación
    if (!empty($id_ubicacion)) {
        $sql .= " AND t1.IdUbicacion = :id_ubicacion";
    }
    
    // Filtro por tipo de movimiento (entrada/salida)
    if (!empty($tipo_movimiento)) {
        if ($tipo_movimiento == 'entrada') {
            $sql .= " AND t1.FechaEntrada IS NOT NULL 
                      AND t1.FechaEntrada NOT LIKE '%1900-01-01%'
                      AND t1.FechaEntrada NOT LIKE '%0000-00-00%'";
        } elseif ($tipo_movimiento == 'salida') {
            $sql .= " AND t1.FechaSalida IS NOT NULL 
                      AND t1.FechaSalida NOT LIKE '%1900-01-01%'
                      AND t1.FechaSalida NOT LIKE '%0000-00-00%'";
        }
    }
    
    // Ordenar por fecha más reciente
    $sql .= " ORDER BY t1.IdMov DESC";
    
    $stmt = $Conexion->prepare($sql);
    
    // Bind de parámetros
    if ($filtro_fecha == 'personalizado' && !empty($fecha_inicio) && !empty($fecha_fin)) {
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
    }
    
    if (!empty($id_personal)) {
        $stmt->bindParam(':id_personal', $id_personal, PDO::PARAM_INT);
    }
    
    if (!empty($id_ubicacion)) {
        $stmt->bindParam(':id_ubicacion', $id_ubicacion, PDO::PARAM_INT);
    }
    
    if (!empty($id_personal_especifico)) {
        $search_term = '%' . $id_personal_especifico . '%';
        $stmt->bindParam(':id_personal_especifico', $search_term);
    }
    
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
    
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]));
}
?>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableMovimientos" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th width="auto" style="color:black; text-align: center;">Id Movimiento</th>
                <th width="auto" style="color:black; text-align: center;">Personal</th>
                <th width="auto" style="color:black; text-align: center;">ID Personal</th>
                <th width="auto" style="color:black; text-align: center;">Ubicación</th>
                <th width="auto" style="color:black; text-align: center;">Movimiento Entrada</th>
                <th width="auto" style="color:black; text-align: center;">Fecha Entrada</th>
                <th width="auto" style="color:black; text-align: center;">Movimiento Salida</th>
                <th width="auto" style="color:black; text-align: center;">Fecha Salida</th>
                <th width="auto" style="color:black; text-align: center;">Tiempo</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($rows)): ?>
            <tr>
                <td colspan="9" class="text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No se encontraron movimientos con los filtros aplicados
                    </div>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach($rows as $row): ?>
                <tr>
                    <td style="text-align: center"><?php echo htmlspecialchars($row->IdMov); ?></td>
                    <td style="text-align: center"><?php echo htmlspecialchars($row->Personal); ?></td>
                    <td style="text-align: center">
                        <span class="badge badge-primary p-2" style="font-size: 0.9em;">
                            <?php echo htmlspecialchars($row->CodigoPersonal); ?>
                        </span>
                    </td>
                    <td style="text-align: center">
                        <?php 
                        $badge_class = ($row->NomCorto == 'SinUbicacion') ? 'badge-secondary' : 'badge-info';
                        ?>
                        <span class="badge <?php echo $badge_class; ?> p-2" style="font-size: 0.9em; min-width: 100px;">
                            <?php echo htmlspecialchars($row->NomCorto); ?>
                        </span>
                    </td>
                    <td style="text-align: center">
                        <?php if ($row->MovEnt == 0): ?>
                            <span class="badge badge-warning p-2" style="font-size: 0.9em;">
                                No existe un movimiento
                            </span>
                        <?php else: ?>
                            <button type="button" class="btn btn-info btn-sm btn-ver-entrada" data-id="<?php echo htmlspecialchars($row->IdMov); ?>">
                                <i class="fa fa-eye"></i> Ver Detalle <?php echo htmlspecialchars($row->FolMovEnt); ?>
                            </button>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center">
                        <?php 
                        $fechaEntrada = formatSqlServerDate($row->FechaEntrada);
                        if($fechaEntrada == 'No existe un movimiento' || $fechaEntrada == 'Fecha inválida') {
                            echo '<span class="badge badge-warning">' . $fechaEntrada . '</span>';
                        } else {
                            echo htmlspecialchars($fechaEntrada);
                        }
                        ?>
                    </td>
                    <td style="text-align: center">
                        <?php if ($row->MovSal == 0): ?>
                            <span class="badge badge-warning p-2" style="font-size: 0.9em;">
                                No existe un movimiento
                            </span>
                        <?php else: ?>
                            <button type="button" class="btn btn-info btn-sm btn-ver-salida" data-id="<?php echo htmlspecialchars($row->IdMov); ?>">
                                <i class="fa fa-eye"></i> Ver Detalle <?php echo htmlspecialchars($row->FolMovSal); ?>
                            </button>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center">
                        <?php 
                        $fechaSalida = formatSqlServerDate($row->FechaSalida);
                        if($fechaSalida == 'No existe un movimiento' || $fechaSalida == 'Fecha inválida') {
                            echo '<span class="badge badge-warning">' . $fechaSalida . '</span>';
                        } else {
                            echo htmlspecialchars($fechaSalida);
                        }
                        ?>
                    </td>
                    <td style="text-align: center">
                        <?php 
                        if(!empty($row->Tiempo)) {
                            echo '<span class="badge badge-success p-2" style="font-size: 0.9em;">' . htmlspecialchars($row->Tiempo) . '</span>';
                        } else {
                            echo '<span class="badge badge-secondary p-2" style="font-size: 0.9em;">N/A</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>