<?php

include '../../api/db/conexion.php';

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
    $sql = "SELECT 
                t1.IdMov,
                CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as Personal,
                CASE 
                    WHEN t1.IdUbicacion = 0 THEN 'SinUbicacion' 
                    ELSE t5.NomCorto 
                END as NomCorto,
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
            WHERE t2.tipoPersonal = 2
            ORDER BY t1.IdMov DESC";
    
    $stmt = $Conexion->prepare($sql);
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
                <th width="auto" style="color:black; text-align: center;">Ubicación</th>
                <th width="auto" style="color:black; text-align: center;">Movimiento Entrada</th>
                <th width="auto" style="color:black; text-align: center;">Fecha Entrada</th>
                <th width="auto" style="color:black; text-align: center;">Movimiento Salida</th>
                <th width="auto" style="color:black; text-align: center;">Fecha Salida</th>
                <th width="auto" style="color:black; text-align: center;">Tiempo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $row): ?>
            <tr>
                <td style="text-align: center"><?php echo htmlspecialchars($row->IdMov); ?></td>
                <td style="text-align: center"><?php echo htmlspecialchars($row->Personal); ?></td>
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
        </tbody>
    </table>
</div>