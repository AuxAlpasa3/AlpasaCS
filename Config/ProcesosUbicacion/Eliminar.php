<?php
    Include_once "../../templates/Sesion.php";

    $IdUbicacion = $_GET['IdUbicacion'] ?? 0;

    // Primero verificamos si la ubicación existe
    $sentUbicacion = $Conexion->prepare("SELECT IdUbicacion, Ubicacion FROM t_ubicacion WHERE IdUbicacion = :idUbicacion");
    $sentUbicacion->bindParam(':idUbicacion', $IdUbicacion, PDO::PARAM_INT);
    $sentUbicacion->execute();
    
    $Ubicaciones = $sentUbicacion->fetchAll(PDO::FETCH_OBJ);
    
    if (count($Ubicaciones) === 0) {
        ?>
        <div class="modal fade" id="EliminarUbicacion" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #d94f00;">
                        <h5 class="modal-title text-white" id="title">Eliminar Ubicación</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i> Error: La ubicación no existe o ya ha sido eliminada.
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
        foreach($Ubicaciones as $Ubicacion) {
            $IdUbicacion = $Ubicacion->IdUbicacion;
            
            $sentIngreso = $Conexion->prepare("SELECT COUNT(*) as total FROM t_ingreso WHERE IdUbicacion = :idUbicacion");
            $sentIngreso->bindParam(':idUbicacion', $IdUbicacion, PDO::PARAM_INT);
            $sentIngreso->execute();
            $ingresoCount = $sentIngreso->fetch(PDO::FETCH_OBJ);
            
            $sentArmado = $Conexion->prepare("SELECT COUNT(*) as total FROM t_armado WHERE IdUbicacion = :idUbicacion");
            $sentArmado->bindParam(':idUbicacion', $IdUbicacion, PDO::PARAM_INT);
            $sentArmado->execute();
            $armadoCount = $sentArmado->fetch(PDO::FETCH_OBJ);
            
            $tieneRegistrosAsociados = ($ingresoCount->total > 0) ||  ($armadoCount->total > 0);
            
            $mensajeDetalle = "";
            if ($ingresoCount->total > 0) {
                $mensajeDetalle .= "Ingresos: " . $ingresoCount->total . " registro(s)<br>";
            }
            if ($armadoCount->total > 0) {
                $mensajeDetalle .= "Armados: " . $armadoCount->total . " registro(s)<br>";
            }
?>
    <div class="modal fade" id="EliminarUbicacion" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="title">Eliminar Ubicación</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="EliminarUbicacion" id="EliminarUbicacion" method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-12" style="text-align: center;">
                                <?php if ($tieneRegistrosAsociados): ?>
                                    <div class="alert alert-warning" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> No se puede eliminar esta ubicación porque tiene registros asociados.
                                    </div>
                                    <div class="alert alert-info" style="background-color: #d94f00;">
                                        <strong>Registros encontrados:</strong><br>
                                        <?php echo $mensajeDetalle; ?>
                                    </div>
                                    <p>Debe eliminar o transferir estos registros antes de eliminar la ubicación.</p>
                                <?php else: ?>
                                    <label for="Id">¿Estás seguro de eliminar la Ubicación?</label>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdUbicacion" class="form-control" type="text" name="IdUbicacion" value="<?php echo htmlspecialchars($Ubicacion->IdUbicacion); ?>" hidden>

                                    <label for="Ubicacion">Nombre de la Ubicación:</label>
                                    <input id="Ubicacion" class="form-control" type="text" name="Ubicacion" value="<?php echo htmlspecialchars($Ubicacion->Ubicacion); ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align: center;">
                                <div class="form-group">
                                    <?php if (!$tieneRegistrosAsociados): ?>
                                        <button class="btn btn-success" type="submit" name="Mov" value="EliminarUbicacion" id="Mov">Sí</button>
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