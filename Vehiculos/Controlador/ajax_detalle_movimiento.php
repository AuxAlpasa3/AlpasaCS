<?php
include '../../api/db/conexion.php';

if (!isset($_GET['idMov']) || !isset($_GET['tipo'])) {
    die('Parámetros inválidos');
}

$idMov = $_GET['idMov'];
$tipo = $_GET['tipo'];

try {
    if ($tipo == 'entrada') {
        $sql = "SELECT 
                    t1.*,
                    t4.DispN as DispEnt, 
                    t4.Foto0 as Foto0Ent,
                    t4.Foto1 as Foto1Ent,
                    t4.Foto2 as Foto2Ent,
                    t4.Foto3 as Foto3Ent,
                    t4.Foto4 as Foto4Ent,
                    t4.Observaciones as ObsEnt,
                    t4.Usuario as UsuarioEnt,
                    t4.TiempoMarcaje as TiempoEnt,
                    CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as Personal,
                    CASE 
                        WHEN t1.IdUbicacion = 0 THEN 'SinUbicacion' 
                        ELSE t5.NomCorto 
                    END as NomCorto
                FROM regentsalper as t1 
                LEFT JOIN t_ubicacion as t5 ON t5.IdUbicacion = t1.IdUbicacion 
                INNER JOIN t_personal as t2 ON t1.IdPer = t2.IdPersonal
                LEFT JOIN regentper as t4 ON t1.FolMovEnt = t4.FolMov
                WHERE t1.IdMov = :idMov AND t2.tipoPersonal = 1";
    } else {
        $sql = "SELECT 
                    t1.*,
                    t6.DispN as DispSal, 
                    t6.Foto0 as Foto0Sal,
                    t6.Foto1 as Foto1Sal,
                    t6.Foto2 as Foto2Sal,
                    t6.Foto3 as Foto3Sal,
                    t6.Foto4 as Foto4Sal,
                    t6.Observaciones as ObsSal,
                    t6.Usuario as UsuarioSal,
                    t6.TiempoMarcaje as TiempoSal,
                    CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as Personal,
                    CASE 
                        WHEN t1.IdUbicacion = 0 THEN 'SinUbicacion' 
                        ELSE t5.NomCorto 
                    END as NomCorto
                FROM regentsalper as t1 
                LEFT JOIN t_ubicacion as t5 ON t5.IdUbicacion = t1.IdUbicacion 
                INNER JOIN t_personal as t2 ON t1.IdPer = t2.IdPersonal
                LEFT JOIN regsalper as t6 ON t1.FolMovSal = t6.FolMov
                WHERE t1.IdMov = :idMov AND t2.tipoPersonal = 1";
    }
    
    $stmt = $Conexion->prepare($sql);
    $stmt->bindParam(':idMov', $idMov, PDO::PARAM_INT);
    $stmt->execute();
    $movimiento = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$movimiento) {
        die('Movimiento no encontrado');
    }
    
} catch (PDOException $e) {
    die('Error en la consulta: ' . $e->getMessage());
}
?>

<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalle de Movimiento <?php echo $tipo == 'entrada' ? 'Entrada' : 'Salida'; ?></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>ID Movimiento:</th>
                                <td><?php echo htmlspecialchars($movimiento->IdMov); ?></td>
                            </tr>
                            <tr>
                                <th>Personal:</th>
                                <td><?php echo htmlspecialchars($movimiento->Personal); ?></td>
                            </tr>
                            <tr>
                                <th>Ubicación:</th>
                                <td>
                                    <?php if ($movimiento->NomCorto == 'SinUbicacion'): ?>
                                        <span class="badge badge-secondary">Sin Ubicación</span>
                                    <?php else: ?>
                                        <span class="badge badge-info"><?php echo htmlspecialchars($movimiento->NomCorto); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if ($tipo == 'entrada'): ?>
                            <tr>
                                <th>Folio Entrada:</th>
                                <td><?php echo htmlspecialchars($movimiento->FolMovEnt); ?></td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <th>Folio Salida:</th>
                                <td><?php echo htmlspecialchars($movimiento->FolMovSal); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Detalles <?php echo $tipo == 'entrada' ? 'Entrada' : 'Salida'; ?></h6>
                        <table class="table table-sm">
                            <?php if ($tipo == 'entrada'): ?>
                            <tr>
                                <th>Dispositivo:</th>
                                <td><?php echo htmlspecialchars($movimiento->DispEnt ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Usuario:</th>
                                <td><?php echo htmlspecialchars($movimiento->UsuarioEnt ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Observaciones:</th>
                                <td><?php echo htmlspecialchars($movimiento->ObsEnt ?? 'Sin observaciones'); ?></td>
                            </tr>
                            <tr>
                                <th>Tiempo Marcaje:</th>
                                <td><?php echo htmlspecialchars($movimiento->TiempoEnt ?? 'N/A'); ?></td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <th>Dispositivo:</th>
                                <td><?php echo htmlspecialchars($movimiento->DispSal ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Usuario:</th>
                                <td><?php echo htmlspecialchars($movimiento->UsuarioSal ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Observaciones:</th>
                                <td><?php echo htmlspecialchars($movimiento->ObsSal ?? 'Sin observaciones'); ?></td>
                            </tr>
                            <tr>
                                <th>Tiempo Marcaje:</th>
                                <td><?php echo htmlspecialchars($movimiento->TiempoSal ?? 'N/A'); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                
                <?php if ($tipo == 'entrada'): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Fotos de Entrada</h6>
                        <div class="row">
                            <?php for ($i = 0; $i <= 4; $i++): ?>
                                <?php $fotoField = "Foto{$i}Ent"; ?>
                                <?php if (!empty($movimiento->$fotoField)): ?>
                                <div class="col-md-2 col-4 mb-2">
                                    <a href="<?php echo htmlspecialchars($movimiento->$fotoField); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($movimiento->$fotoField); ?>" 
                                             class="img-thumbnail" 
                                             style="width: 100px; height: 100px; object-fit: cover;"
                                             alt="Foto <?php echo $i; ?>">
                                    </a>
                                </div>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Fotos de Salida</h6>
                        <div class="row">
                            <?php for ($i = 0; $i <= 4; $i++): ?>
                                <?php $fotoField = "Foto{$i}Sal"; ?>
                                <?php if (!empty($movimiento->$fotoField)): ?>
                                <div class="col-md-2 col-4 mb-2">
                                    <a href="<?php echo htmlspecialchars($movimiento->$fotoField); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($movimiento->$fotoField); ?>" 
                                             class="img-thumbnail" 
                                             style="width: 100px; height: 100px; object-fit: cover;"
                                             alt="Foto <?php echo $i; ?>">
                                    </a>
                                </div>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#modalDetalle').modal('show');
});
</script>