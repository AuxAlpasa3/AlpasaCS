<?php
include '../../api/db/conexion.php';

header('Content-Type: text/html; charset=utf-8');

// Obtener parámetros de paginación
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$por_pagina = isset($_GET['por_pagina']) ? (int)$_GET['por_pagina'] : 10;
$inicio = ($pagina - 1) * $por_pagina;

// Obtener filtros
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
    // CONSULTA PARA CONTAR TOTAL DE REGISTROS
    $count_sql = "SELECT COUNT(*) as total FROM regentsalper as t1 
                  LEFT JOIN t_ubicacion_interna as t5 ON t5.IdUbicacion = t1.IdUbicacion 
                  INNER JOIN t_personal as t2 ON t1.IdPer = t2.IdPersonal
                  WHERE t2.status = 1";
    
    $count_params = [];
    
    // APLICAR FILTROS AL COUNT
    switch($filtro_fecha) {
        case 'hoy':
            $count_sql .= " AND CONVERT(DATE, t1.FechaEntrada) = CONVERT(DATE, GETDATE())";
            break;
        case 'ayer':
            $count_sql .= " AND CONVERT(DATE, t1.FechaEntrada) = DATEADD(DAY, -1, CONVERT(DATE, GETDATE()))";
            break;
        case 'semana':
            $count_sql .= " AND DATEPART(WEEK, t1.FechaEntrada) = DATEPART(WEEK, GETDATE()) 
                            AND DATEPART(YEAR, t1.FechaEntrada) = DATEPART(YEAR, GETDATE())";
            break;
        case 'mes':
            $count_sql .= " AND MONTH(t1.FechaEntrada) = MONTH(GETDATE()) 
                            AND YEAR(t1.FechaEntrada) = YEAR(GETDATE())";
            break;
        case 'personalizado':
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                $count_sql .= " AND CONVERT(DATE, t1.FechaEntrada) BETWEEN :fecha_inicio AND :fecha_fin";
                $count_params[':fecha_inicio'] = $fecha_inicio;
                $count_params[':fecha_fin'] = $fecha_fin;
            }
            break;
    }
    
    if (!empty($id_personal)) {
        $count_sql .= " AND t2.IdPersonal = :id_personal";
        $count_params[':id_personal'] = $id_personal;
    }
    
    if (!empty($id_personal_especifico)) {
        $count_sql .= " AND t2.IdPersonal LIKE :id_personal_especifico";
        $count_params[':id_personal_especifico'] = '%' . $id_personal_especifico . '%';
    }
    
    if (!empty($id_ubicacion)) {
        $count_sql .= " AND t1.IdUbicacion = :id_ubicacion";
        $count_params[':id_ubicacion'] = $id_ubicacion;
    }
    
    if (!empty($tipo_movimiento)) {
        if ($tipo_movimiento == 'entrada') {
            $count_sql .= " AND t1.FechaEntrada IS NOT NULL 
                            AND CONVERT(VARCHAR, t1.FechaEntrada) NOT LIKE '%1900-01-01%'";
        } elseif ($tipo_movimiento == 'salida') {
            $count_sql .= " AND t1.FechaSalida IS NOT NULL 
                            AND CONVERT(VARCHAR, t1.FechaSalida) NOT LIKE '%1900-01-01%'";
        }
    }
    
    // EJECUTAR CONSULTA DE CONTEO
    $count_stmt = $Conexion->prepare($count_sql);
    foreach($count_params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total_registros = $count_stmt->fetch(PDO::FETCH_OBJ)->total;
    $total_paginas = ceil($total_registros / $por_pagina);
    
    // CONSULTA PRINCIPAL CON PAGINACIÓN (SQL Server 2012+)
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
    
    // APLICAR FILTROS
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
                $params[':fecha_inicio'] = $fecha_inicio;
                $params[':fecha_fin'] = $fecha_fin;
            }
            break;
    }
    
    if (!empty($id_personal)) {
        $sql .= " AND t2.IdPersonal = :id_personal";
        $params[':id_personal'] = $id_personal;
    }
    
    if (!empty($id_personal_especifico)) {
        $sql .= " AND t2.IdPersonal LIKE :id_personal_especifico";
        $params[':id_personal_especifico'] = '%' . $id_personal_especifico . '%';
    }
    
    if (!empty($id_ubicacion)) {
        $sql .= " AND t1.IdUbicacion = :id_ubicacion";
        $params[':id_ubicacion'] = $id_ubicacion;
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
    
    // AGREGAR ORDER BY Y PAGINACIÓN
    $sql .= " ORDER BY t1.IdMovEntSal DESC
              OFFSET :inicio ROWS FETCH NEXT :por_pagina ROWS ONLY";
    
    $stmt = $Conexion->prepare($sql);
    
    // BIND DE PARÁMETROS PARA FILTROS
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    // BIND DE PARÁMETROS DE PAGINACIÓN
    $stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->bindValue(':por_pagina', $por_pagina, PDO::PARAM_INT);
    
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $count = count($rows);
    
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    exit;
}
?>

<!-- CAMPOS OCULTOS CON INFORMACIÓN DE PAGINACIÓN -->
<input type="hidden" id="pagination-total" value="<?php echo $total_registros; ?>">
<input type="hidden" id="pagination-current" value="<?php echo $pagina; ?>">
<input type="hidden" id="pagination-per-page" value="<?php echo $por_pagina; ?>">
<input type="hidden" id="pagination-total-pages" value="<?php echo $total_paginas; ?>">

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="dataTableMovimientos" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th style="text-align: center; width: 5%;">#</th>
                <th style="text-align: center; width: 8%;">Id Mov</th>
                <th style="text-align: center; width: 15%;">Personal</th>
                <th style="text-align: center; width: 10%;">Ubicación</th>
                <th style="text-align: center; width: 10%;">Mov Entrada</th>
                <th style="text-align: center; width: 12%;">Fecha Entrada</th>
                <th style="text-align: center; width: 10%;">Mov Salida</th>
                <th style="text-align: center; width: 12%;">Fecha Salida</th>
                <th style="text-align: center; width: 8%;">Tiempo</th>
            </tr>
        </thead>
        <tbody>
            <?php if($count == 0): ?>
            <tr>
                <td colspan="9" class="text-center">
                    <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle"></i> No se encontraron movimientos con los filtros aplicados
                    </div>
                </td>
            </tr>
            <?php else: ?>
                <?php 
                $contador = $inicio + 1;
                foreach($rows as $row): 
                    $fechaEntrada = formatSqlServerDate($row->FechaEntrada);
                    $fechaSalida = formatSqlServerDate($row->FechaSalida);
                ?>
                <tr>
                    <td style="text-align: center; font-weight: bold;"><?php echo $contador; ?></td>
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
                <?php 
                $contador++;
                endforeach; 
            ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($count > 0): ?>
<div class="row mt-3">
    <div class="col-md-6">
        <p class="text-muted">
            <small>
                <i class="fas fa-info-circle"></i> 
                Mostrando <?php echo $inicio + 1; ?> a <?php echo min($inicio + $por_pagina, $total_registros); ?> 
                de <?php echo $total_registros; ?> movimiento(s) | 
                Página <?php echo $pagina; ?> de <?php echo $total_paginas; ?>
            </small>
        </p>
    </div>
    <div class="col-md-6">
        <p class="text-muted text-right">
            <small>
                <i class="fas fa-chart-bar"></i> 
                Entradas: <?php echo count(array_filter($rows, function($r) { return $r->FolMovEnt > 0; })); ?> | 
                Salidas: <?php echo count(array_filter($rows, function($r) { return $r->FolMovSal > 0; })); ?>
            </small>
        </p>
    </div>
</div>
<?php endif; ?>