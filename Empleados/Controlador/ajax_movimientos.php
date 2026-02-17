<?php
include '../../api/db/conexion.php';

header('Content-Type: text/html; charset=utf-8');

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
        strpos($dateStr, '0000-00-00') !== false ||
        strpos($dateStr, '1753-01-01') !== false) {
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
    $sql = "SELECT 
                t1.IdMovEntSal,
                CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as Personal,
                t2.IdPersonal as CodigoPersonal,
                CASE 
                    WHEN t1.IdUbicacion = 0 OR t1.IdUbicacion IS NULL THEN 'SinUbicacion' 
                    ELSE t5.NomCorto 
                END as NomCorto,
                t1.IdUbicacion,
                t1.FolMovEnt,
                t1.FechaEntrada,
                t1.FolMovSal,
                t1.FechaSalida,
                t1.tiempo as Tiempo,
                t2.RutaFoto
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
    
    $count = count($rows);
    
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    exit;
}
?>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableMovimientos" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th style="text-align: center;">Id Movimiento</th>
                <th style="text-align: center;">Personal</th>
                <th style="text-align: center;">Ubicación</th>
                <th style="text-align: center;">Movimiento Entrada</th>
                <th style="text-align: center;">Fecha Entrada</th>
                <th style="text-align: center;">Movimiento Salida</th>
                <th style="text-align: center;">Fecha Salida</th>
                <th style="text-align: center;">Tiempo</th>
            </tr>
        </thead>
        <tbody>
            <?php if($count == 0): ?>
            <tr>
                <td colspan="8" class="text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No se encontraron movimientos con los filtros aplicados
                    </div>
                </td>
            </tr>
            <?php else: ?>
                <?php foreach($rows as $row): 
                    $fechaEntrada = formatSqlServerDate($row->FechaEntrada);
                    $fechaSalida = formatSqlServerDate($row->FechaSalida);
                ?>
                <tr>
                    <td style="text-align: center"><?php echo htmlspecialchars($row->IdMovEntSal); ?></td>
                    <td style="text-align: center">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="text-left">
                                <div><?php echo htmlspecialchars($row->Personal); ?></div>
                                <small class="text-muted">
                                    <span class="badge badge-primary" style="font-size: 0.8em;">
                                        ID: <?php echo htmlspecialchars($row->CodigoPersonal); ?>
                                    </span>
                                </small>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center">
                        <?php 
                        $badge_class = ($row->NomCorto == 'SinUbicacion') ? 'badge-secondary' : 'badge-info';
                        ?>
                        <span class="badge <?php echo $badge_class; ?> p-2">
                            <?php echo htmlspecialchars($row->NomCorto); ?>
                        </span>
                    </td>
                    <td style="text-align: center">
                        <?php if ($row->FolMovEnt == 0): ?>
                            <span class="badge badge-warning p-2">
                                <i class="fas fa-times"></i> Sin movimiento
                            </span>
                        <?php else: ?>
                            <button type="button" class="btn btn-info btn-sm btn-ver-entrada" 
                                    data-id="<?php echo htmlspecialchars($row->IdMovEntSal); ?>"
                                    title="Ver detalles de entrada">
                                <i class="fas fa-eye"></i> Ver <?php echo htmlspecialchars($row->FolMovEnt); ?>
                            </button>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center">
                        <?php 
                        if($fechaEntrada == 'No existe un movimiento' || $fechaEntrada == 'Fecha inválida') {
                            echo '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> ' . $fechaEntrada . '</span>';
                        } else {
                            $date = new DateTime($fechaEntrada);
                            echo '<span class="badge badge-success">' . $date->format('d/m/Y H:i:s') . '</span>';
                        }
                        ?>
                    </td>
                    <td style="text-align: center">
                        <?php if ($row->FolMovSal == 0): ?>
                            <span class="badge badge-warning p-2">
                                <i class="fas fa-times"></i> Sin movimiento
                            </span>
                        <?php else: ?>
                            <button type="button" class="btn btn-info btn-sm btn-ver-salida" 
                                    data-id="<?php echo htmlspecialchars($row->IdMovEntSal); ?>"
                                    title="Ver detalles de salida">
                                <i class="fas fa-eye"></i> Ver <?php echo htmlspecialchars($row->FolMovSal); ?>
                            </button>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center">
                        <?php 
                        if($fechaSalida == 'No existe un movimiento' || $fechaSalida == 'Fecha inválida') {
                            echo '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> ' . $fechaSalida . '</span>';
                        } else {
                            $date = new DateTime($fechaSalida);
                            echo '<span class="badge badge-danger">' . $date->format('d/m/Y H:i:s') . '</span>';
                        }
                        ?>
                    </td>
                    <td style="text-align: center">
                        <?php 
                        if(!empty($row->Tiempo) && $row->Tiempo != '00:00:00' && $row->Tiempo != '1900-01-01 00:00:00.000') {
                            echo '<span class="badge badge-primary p-2"><i class="fas fa-clock"></i> ' . htmlspecialchars($row->Tiempo) . '</span>';
                        } else {
                            echo '<span class="badge badge-secondary p-2"><i class="fas fa-clock"></i> N/A</span>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($count > 0): ?>
<div class="row mt-2">
    <div class="col-md-12">
        <p class="text-muted text-center">
            <small>
                <i class="fas fa-info-circle"></i> 
                Mostrando <?php echo $count; ?> movimiento(s) | 
                Total entradas: <?php echo count(array_filter($rows, function($r) { return $r->FolMovEnt > 0; })); ?> | 
                Total salidas: <?php echo count(array_filter($rows, function($r) { return $r->FolMovSal > 0; })); ?>
            </small>
        </p>
    </div>
</div>
<?php endif; ?>