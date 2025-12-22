<?php 
    Include_once "../../templates/Sesion.php";

    $IdAlmacen = $_GET['IdAlmacen'] ?? 0;

    // Primero verificamos si el almacén existe
    $sentAlmacen = $Conexion->prepare("SELECT IdAlmacen, Almacen, Ubicacion FROM t_almacen WHERE IdAlmacen = :idAlmacen");
    $sentAlmacen->bindParam(':idAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $sentAlmacen->execute();
    
    $Almacenes = $sentAlmacen->fetchAll(PDO::FETCH_OBJ);
    
    if (count($Almacenes) === 0) {
        ?>
        <div class="modal fade" id="EliminarAlmacen" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #d94f00;">
                        <h5 class="modal-title text-white" id="title">Eliminar Almacén</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i> Error: El almacén no existe o ya ha sido eliminado.
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
        foreach($Almacenes as $Almacen) {
            $IdAlmacen = $Almacen->IdAlmacen;
            
            $sentIngreso = $Conexion->prepare("SELECT COUNT(*) as total FROM t_ingreso WHERE Almacen = :idAlmacen");
            $sentIngreso->bindParam(':idAlmacen', $IdAlmacen, PDO::PARAM_INT);
            $sentIngreso->execute();
            $ingresoCount = $sentIngreso->fetch(PDO::FETCH_OBJ);
            
            $sentSalida = $Conexion->prepare("SELECT COUNT(*) as total FROM t_Salida WHERE Almacen = :idAlmacen");
            $sentSalida->bindParam(':idAlmacen', $IdAlmacen, PDO::PARAM_INT);
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
    <div class="modal fade" id="EliminarAlmacen" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="title">Eliminar Almacén</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="EliminarAlmacen" id="EliminarAlmacen" method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-12" style="text-align: center;">
                                <?php if ($tieneRegistrosAsociados): ?>
                                    <div class="alert alert-warning" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> No se puede eliminar este almacén porque tiene registros asociados.
                                    </div>
                                    <div class="alert alert-info" style="background-color: #d94f00;">
                                        <strong>Registros encontrados:</strong><br>
                                        <?php echo $mensajeDetalle; ?>
                                    </div>
                                    <p>Debe eliminar o transferir estos registros antes de eliminar el almacén.</p>
                                <?php else: ?>
                                    <label for="Id">¿Estás seguro de eliminar el Almacén?</label>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdAlmacen" class="form-control" type="text" name="IdAlmacen" value="<?php echo htmlspecialchars($Almacen->IdAlmacen); ?>" hidden>

                                    <label for="Almacen">Nombre del Almacén:</label>
                                    <input id="Almacen" class="form-control" type="text" name="Almacen" value="<?php echo htmlspecialchars($Almacen->Almacen); ?>" required readonly>
                                    
                                    <label for="Ubicacion">Ubicación:</label>
                                    <input id="Ubicacion" class="form-control" type="text" name="Ubicacion" value="<?php echo htmlspecialchars($Almacen->Ubicacion); ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align: center;">
                                <div class="form-group">
                                    <?php if (!$tieneRegistrosAsociados): ?>
                                        <button class="btn btn-success" type="submit" name="Mov" value="EliminarAlmacen" id="Mov">Sí</button>
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