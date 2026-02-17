<?php 
    Include_once "../../templates/Sesion.php";

    $IdUbicacion = $_GET['IdUbicacionInterna'] ?? 0;

    $sentUbicacionInterna = $Conexion->prepare("SELECT IdUbicacion, NomCorto, NomLargo,
Concat(Ciudad,',',Estado,'.',Pais) as Ubicacion FROM t_ubicacion_interna WHERE IdUbicacion = :idUbicacion");
    $sentUbicacionInterna->bindParam(':idUbicacion', $IdUbicacion, PDO::PARAM_INT);
    $sentUbicacionInterna->execute();
    
    $UbicacionesInternas = $sentUbicacionInterna->fetchAll(PDO::FETCH_OBJ);
    
    if (count($UbicacionesInternas) === 0) {
        ?>
        <div class="modal fade" id="EliminarUbicacionInterna" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #d94f00;">
                        <h5 class="modal-title text-white" id="title">Eliminar Ubicación Interna</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i> Error: La ubicación interna no existe o ya ha sido eliminada.
                        </div>
                        <div class="text-center">
                            <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        foreach($UbicacionesInternas as $UbicacionInterna) {
            $IdUbicacion = $UbicacionInterna->IdUbicacion;
            
            $sentIngreso = $Conexion->prepare("SELECT COUNT(*) as total FROM regentper WHERE Ubicacion   = :idUbicacion");
            $sentIngreso->bindParam(':idUbicacion', $IdUbicacion, PDO::PARAM_INT);
            $sentIngreso->execute();
            $ingresoCount = $sentIngreso->fetch(PDO::FETCH_OBJ);
            
            $sentSalida = $Conexion->prepare("SELECT COUNT(*) as total FROM regsalper WHERE Ubicacion = :idUbicacion");
            $sentSalida->bindParam(':idUbicacion', $IdUbicacion, PDO::PARAM_INT);
            $sentSalida->execute();
            $salidaCount = $sentSalida->fetch(PDO::FETCH_OBJ);
            
            $tieneRegistrosAsociados = ($ingresoCount->total > 0) || ($salidaCount->total > 0);
            
            $mensajeDetalle = "";
            if ($ingresoCount->total > 0) {
                $mensajeDetalle .= "Ingresos: " . $ingresoCount->total . " registro(s)<br>";
            }
            if ($salidaCount->total > 0) {
                $mensajeDetalle .= "Salidas: " . $salidaCount->total . " registro(s)<br>";
            }
?>
    <div class="modal fade" id="EliminarUbicacionInterna" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="title">Eliminar Ubicación Interna</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="EliminarUbicacionInterna" id="EliminarUbicacionInterna" method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-12" style="text-align: center;">
                                <?php if ($tieneRegistrosAsociados): ?>
                                    <div class="alert alert-warning" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> No se puede eliminar esta ubicación interna porque tiene registros asociados.
                                    </div>
                                    <div class="alert alert-info" style="background-color: #d94f00;">
                                        <strong>Registros encontrados:</strong><br>
                                        <?php echo $mensajeDetalle; ?>
                                    </div>
                                    <p>Debe eliminar o transferir estos registros antes de eliminar la ubicación interna.</p>
                                <?php else: ?>
                                    <label for="Id">¿Estás seguro de eliminar la Ubicación Interna?</label>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdUbicacionInterna" class="form-control" type="text" name="IdUbicacionInterna" value="<?php echo htmlspecialchars($UbicacionInterna->IdUbicacion); ?>" hidden>

                                    <label for="NomCorto">Nombre Corto:</label>
                                    <input id="NomCorto" class="form-control" type="text" name="NomCorto" value="<?php echo htmlspecialchars($UbicacionInterna->NomCorto); ?>" required readonly>
                                    
                                    <label for="Ubicacion">Ubicación:</label>
                                    <input id="Ubicacion" class="form-control" type="text" name="Ubicacion" value="<?php echo htmlspecialchars($UbicacionInterna->Ubicacion); ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align: center;">
                                <div class="form-group">
                                    <?php if (!$tieneRegistrosAsociados): ?>
                                        <button class="btn btn-success" type="submit" name="Mov" value="EliminarUbicacionInterna" id="Mov">Sí</button>
                                    <?php endif; ?>
                                    <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal"><?php echo $tieneRegistrosAsociados ? 'Cerrar' : 'No'; ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php  
        }
    }
?>