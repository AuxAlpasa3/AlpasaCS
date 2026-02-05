<?php
include '../../api/db/conexion.php';

$idMov = $_GET['idMov'] ?? 0;
$tipo = $_GET['tipo'] ?? 'entrada';

if ($idMov <= 0) {
    echo '<div class="alert alert-danger">ID de movimiento no válido</div>';
    exit;
}

try {
    $sql = "SELECT 
                t1.*,
                CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as Personal,
                t2.IdPersonal,
                t2.RutaFoto as Foto,
                t4.NomCargo as Cargo,
                t5.NomDepto as Departamento,
                t3.NomCorto as Ubicacion
            FROM regentsalper as t1 
            INNER JOIN t_personal as t2 ON t1.IdPer = t2.IdPersonal
            LEFT JOIN t_ubicacion as t3 ON t1.IdUbicacion = t3.IdUbicacion
            INNER JOIN t_cargo as t4 on t2.Cargo=t4.IdCargo
            INNER JOIN t_departamento as t5 on t2.Departamento=t5.IdDepartamento
            WHERE t1.IdMovEntSal= ?";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([$idMov]);
    $movimiento = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$movimiento) {
        echo '<div class="alert alert-danger">Movimiento no encontrado</div>';
        exit;
    }
    
    if ($tipo === 'entrada') {
        $tabla = 'regentper';
        $campoFolio = 'FolMovEnt';
        $titulo = 'Entrada';
        $icono = 'fa-sign-in-alt';
        $color = 'success';
        $TipoMov = 1;
    } else {
        $tabla = 'regsalper';
        $campoFolio = 'FolMovSal';
        $titulo = 'Salida';
        $icono = 'fa-sign-out-alt';
        $color = 'danger';
        $TipoMov = 2;
    }
    
    $folio = $movimiento->{$campoFolio};
    
    $sqlDetalle= "SELECT t2.NoEmpleado, t3.NomCorto, t1.DispN, CONVERT(varchar, t1.Fecha, 103) as Fecha, 
    CONVERT(varchar, t1.TiempoMarcaje, 108) as TiempoMarcaje, (CASE WHEN t1.TipoVehiculo=0 then 'Sin Vehiculo'
    WHEN t1.TipoVehiculo=1 THEN 'Vehiculo Empresa' WHEN t1.TipoVehiculo=2 then 'Vehiculo Propio' 
    WHEN t1.tipovehiculo=3 THEN 'Vehiculo Registrado' END) AS TipoVehiculo, 
    t4.Marca, t4.Modelo, t4.Placas,t1.Observaciones, t5.Usuario, 
    (case when t1.Notificar =0 then 'Sin Notificar a Supervisor' else 'Se Notifico a Supervisor' End) as Notificar
    FROM $tabla AS t1 
    INNER JOIN t_personal AS t2 on t1.IdPer=t2.IdPersonal
    INNER JOIN t_ubicacion AS t3 on t1.Ubicacion=t3.IdUbicacion
    INNER JOIN t_vehiculos AS t4 on t1.IdVeh=t4.IdVehiculo
    INNER JOIN t_usuario as t5 on t1.Usuario=t5.IdUsuario
    WHERE FolMov = ?";

    $stmtDetalle = $Conexion->prepare($sqlDetalle);
    $stmtDetalle->execute([$folio]);
    $detalle = $stmtDetalle->fetch(PDO::FETCH_OBJ);

    $sqlFotografias= "SELECT RutaFoto,NombreFoto,NextIdFoto
    from t_fotografias_Encabezado as t1 
    INNER JOIN t_fotografias_Detalle as t2 on t1.IdFotografias=t2.IdFotografiaRef
    WHERE  IdEntSal= ? and TipoMov= ?";

    $stmtFotografias = $Conexion->prepare($sqlFotografias);
    $stmtFotografias->execute([$folio,$TipoMov]);
    $fotografias = $stmtFotografias->fetchALL(PDO::FETCH_OBJ);
    
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    exit;
}
?>

<div class="modal fade" id="Detalle<?php echo ucfirst($tipo); ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title">
                    <i class="fas <?php echo $icono; ?> mr-2"></i>
                    Detalle de <?php echo $titulo; ?> - Movimiento #<?php echo $idMov; ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Información Personal</h6>
                            </div>
                            <div class="card-body text-center">
                                <?php if(!empty($movimiento->Foto) && filter_var($movimiento->Foto, FILTER_VALIDATE_URL)): ?>
                                    <img src="<?php echo htmlspecialchars($movimiento->Foto); ?>" 
                                        alt="Foto" 
                                        class="rounded-circle mb-3" 
                                        style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #d94f00;">
                                <?php else: ?>
                                    <div class="employee-initials rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" 
                                        style="width: 100px; height: 100px; background-color: #d94f00; color: white; font-weight: bold; font-size: 2rem; border: 3px solid #d94f00;">
                                        <?php 
                                        $initials = '';
                                        $nameParts = explode(' ', $movimiento->Personal);
                                        if(isset($nameParts[0][0])) $initials .= $nameParts[0][0];
                                        if(isset($nameParts[1][0])) $initials .= $nameParts[1][0];
                                        echo strtoupper($initials);
                                        ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h5 class="mb-1"><?php echo htmlspecialchars($movimiento->Personal); ?></h5>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-id-card"></i> No Empleado: <?php echo htmlspecialchars($movimiento->IdPersonal); ?>
                                </p>
                                <?php if(!empty($movimiento->Cargo)): ?>
                                    <p class="mb-1">
                                        <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($movimiento->Cargo); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if(!empty($movimiento->Departamento)): ?>
                                    <p class="mb-0">
                                        <i class="fas fa-building"></i> <?php echo htmlspecialchars($movimiento->Departamento); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Fotografías (<?php echo count($fotografias); ?>)</h6>
                        </div>
                        <div class="card-body">
                            <?php if($fotografias && count($fotografias) > 0): ?>
                                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                                    <?php foreach($fotografias as $index => $foto): ?>
                                        <div class="col">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <img src="<?php echo htmlspecialchars($foto->RutaFoto); ?>" 
                                                    class="card-img-top" 
                                                    alt="<?php echo htmlspecialchars($foto->NombreFoto); ?>"
                                                    style="height: 120px; object-fit: cover;">
                                                <div class="card-body p-2">
                                                    <p class="card-text small text-center mb-0">
                                                        <?php echo htmlspecialchars($foto->NombreFoto); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mb-0 text-center">
                                    No se encontraron fotografías
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    </div>
                    
                    
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header bg-<?php echo $color; ?> text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Detalles del Movimiento</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Folio Movimiento:</strong> 
                                            <span class="badge badge-<?php echo $color; ?>"><?php echo $folio; ?></span>
                                        </p>
                                        <p><strong>Tipo:</strong> 
                                            <span class="badge badge-<?php echo $color; ?>">
                                                <?php echo strtoupper($titulo); ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Ubicación:</strong><br>
                                            <?php if(!empty($movimiento->Ubicacion)): ?>
                                                <span class="badge badge-info"><?php echo htmlspecialchars($movimiento->Ubicacion); ?></span>
                                                <?php if(!empty($movimiento->DescUbicacion)): ?>
                                                    <small class="text-muted d-block"><?php echo htmlspecialchars($movimiento->DescUbicacion); ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Sin ubicación asignada</span>
                                            <?php endif; ?>
                                        </p>
                                        
                                        <p><strong>Fecha <?php echo $titulo; ?>:</strong><br>
                                            <?php 
                                            $fechaCampo = ($tipo === 'entrada') ? 'FechaEntrada' : 'FechaSalida';
                                            $fecha = $movimiento->{$fechaCampo};
                                            if ($fecha && strpos($fecha, '1900-01-01') === false) {
                                                $date = new DateTime($fecha);
                                                echo $date->format('d/m/Y H:i:s');
                                            } else {
                                                echo '<span class="badge badge-warning">No registrada</span>';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php if($detalle): ?>
                                <hr>
                                <h6><i class="fas fa-clipboard-list mr-2"></i>Información Adicional</h6>
                                <div class="row mt-3">
                                    <?php 
                                    $camposExcluir = ['FolMov', 'IdPersonal', 'FechaRegistro','IdVehiculo'];
                                    foreach($detalle as $key => $value):
                                        if(in_array($key, $camposExcluir)) continue;
                                        if(empty($value) || strpos($value, '1900-01-01') !== false) continue;
                                    ?>
                                    <div class="col-md-6 mb-2">
                                        <strong><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</strong><br>
                                        <?php 
                                        if(strpos($key, 'Fecha') !== false || strpos($key, 'fecha') !== false) {
                                            try {
                                                $date = new DateTime($value);
                                                echo $date->format('d/m/Y');
                                            } catch (Exception $e) {
                                                echo htmlspecialchars($value);
                                            }
                                        } else {
                                            echo htmlspecialchars($value);
                                        }
                                        ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#Detalle<?php echo ucfirst($tipo); ?>').modal('show');
    
    $('#Detalle<?php echo ucfirst($tipo); ?>').on('hidden.bs.modal', function () {
        $('#modal-container').empty();
    });
});
</script>

<style>
.modal-body .card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.modal-body .card-header {
    border-radius: 0.375rem 0.375rem 0 0 !important;
    font-weight: 600;
}

.employee-initials {
    cursor: default;
}

.badge {
    font-size: 0.85em;
    padding: 0.35em 0.65em;
}

@media print {
    .modal-footer {
        display: none !important;
    }
    
    .modal-content {
        border: none !important;
        box-shadow: none !important;
    }
    
    .modal-body .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
}
</style>