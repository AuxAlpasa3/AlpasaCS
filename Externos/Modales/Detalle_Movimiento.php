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
                CONCAT(t2.Nombre, ' ', t2.ApPaterno, ' ', t2.ApMaterno) as PersonalExterno,
                t2.IdPersonalExterno,
                t2.RutaFoto as Foto,
                t4.NomCargo as Cargo,
                t3.NomCorto as Ubicacion,
                t2.EmpresaProcedencia as Empresa
            FROM regentsalext as t1 
            INNER JOIN t_personal_externo as t2 ON t1.IdExt = t2.IdPersonalExterno
            LEFT JOIN t_ubicacion as t3 ON t1.IdUbicacion = t3.IdUbicacion
            INNER JOIN t_cargoExterno as t4 on t2.Cargo=t4.IdCargo
            WHERE t1.IdMovEntSal = ?";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([$idMov]);
    $movimiento = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$movimiento) {
        echo '<div class="alert alert-danger">Movimiento no encontrado</div>';
        exit;
    }
    
    if ($tipo === 'entrada') {
        $tabla = 'regentExt';
        $campoFolio = 'FolMovEnt';
        $titulo = 'Entrada';
        $icono = 'fa-sign-in-alt';
        $color = 'success';
        $TipoMov = 1;
    } else {
        $tabla = 'regsalExt';
        $campoFolio = 'FolMovSal';
        $titulo = 'Salida';
        $icono = 'fa-sign-out-alt';
        $color = 'danger';
        $TipoMov = 2;
    }
    
    $folio = $movimiento->{$campoFolio};
    
    $sqlDetalle= " SELECT t2.NumeroIdentificacion, t3.NomCorto, t1.DispN, CONVERT(varchar, t1.Fecha, 103) as Fecha, 
    CONVERT(varchar, t1.TiempoMarcaje, 108) as TiempoMarcaje, (CASE WHEN t1.TipoVehiculo=0 then 'Sin Vehiculo'
    WHEN t1.TipoVehiculo=1 THEN 'Vehiculo Empresa' WHEN t1.TipoVehiculo=2 then 'Vehiculo Propio' 
    WHEN t1.tipovehiculo=3 THEN 'Vehiculo Registrado' END) AS TipoVehiculo, 
    t4.Marca, t4.Modelo, t4.Placas,t1.Observaciones, t5.Usuario, 
    (case when t1.Notificar =0 then 'Sin Notificar a Responsable' else 'Se Notifico a Responsable' End) as Notificar
    FROM regentext AS t1 
    INNER JOIN t_personal_externo AS t2 on t1.IdExt=t2.IdPersonalExterno
    INNER JOIN t_ubicacion AS t3 on t1.Ubicacion=t3.IdUbicacion
    INNER JOIN t_vehiculos AS t4 on t1.IdVeh=t4.IdVehiculo
    INNER JOIN t_usuario as t5 on t1.Usuario=t5.IdUsuario
    WHERE t1.FolMov= ? ";

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
                                <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Información PersonalExterno</h6>
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
                                        $nameParts = explode(' ', $movimiento->PersonalExterno);
                                        if(isset($nameParts[0][0])) $initials .= $nameParts[0][0];
                                        if(isset($nameParts[1][0])) $initials .= $nameParts[1][0];
                                        echo strtoupper($initials);
                                        ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h5 class="mb-1"><?php echo htmlspecialchars($movimiento->PersonalExterno); ?></h5>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-id-card"></i> Externo: <?php echo htmlspecialchars($movimiento->PersonalExterno); ?>
                                </p>
                                <?php if(!empty($movimiento->Cargo)): ?>
                                    <p class="mb-1">
                                        <i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($movimiento->Cargo); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if(!empty($movimiento->Empresa)): ?>
                                    <p class="mb-0">
                                        <i class="fas fa-building"></i> <?php echo htmlspecialchars($movimiento->Empresa); ?>
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
        <div id="fotografiasCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach($fotografias as $index => $foto): ?>
                    <button type="button" data-bs-target="#fotografiasCarousel" 
                            data-bs-slide-to="<?php echo $index; ?>" 
                            class="<?php echo $index === 0 ? 'active' : ''; ?>"
                            aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                            aria-label="Slide <?php echo $index + 1; ?>">
                    </button>
                <?php endforeach; ?>
            </div>
            
            <div class="carousel-inner">
                <?php foreach($fotografias as $index => $foto): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($foto->RutaFoto); ?>" 
                             class="d-block w-100" 
                             alt="<?php echo htmlspecialchars($foto->NombreFoto); ?>"
                             style="height: 400px; object-fit: cover;">
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#fotografiasCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#fotografiasCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
        
        <div class="row mt-3">
            <?php foreach($fotografias as $index => $foto): ?>
                <div class="col-3 col-md-2">
                    <a href="#fotografiasCarousel" data-bs-slide-to="<?php echo $index; ?>" 
                       class="d-block <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($foto->RutaFoto); ?>" 
                             class="img-thumbnail" 
                             alt="Miniatura <?php echo htmlspecialchars($foto->NombreFoto); ?>"
                             style="height: 80px; width: 100%; object-fit: cover;">
                    </a>
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
                                    $camposExcluir = ['FolMov', 'IdPersonalExterno', 'FechaRegistro','IdVehiculo'];
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