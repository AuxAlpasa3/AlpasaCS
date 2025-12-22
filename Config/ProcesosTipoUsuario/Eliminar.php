<?php 
    Include_once "../../templates/Sesion.php";

    $IdTipoUsuario = $_GET['IdTipoUsuario'] ?? 0;

    $sentTUsuario = $Conexion->prepare("SELECT IdTipoUsuario, TipoUsuario FROM t_tipoUsuario WHERE IdTipoUsuario = :idTipoUsuario");
    $sentTUsuario->bindParam(':idTipoUsuario', $IdTipoUsuario, PDO::PARAM_INT);
    $sentTUsuario->execute();
    
    $tiposusuarios = $sentTUsuario->fetchAll(PDO::FETCH_OBJ);
    
    if (count($tiposusuarios) === 0) {
        ?>
        <div class="modal fade" id="EliminarTipoUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #d94f00;">
                        <h5 class="modal-title text-white" id="title">Eliminar Tipo de Usuario</h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle"></i> Error: El tipo de usuario no existe o ya ha sido eliminado.
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
        foreach($tiposusuarios as $Tipos) {
            $IdTipoUsuario = $Tipos->IdTipoUsuario;
            
            $sentUsuariosAsociados = $Conexion->prepare("SELECT COUNT(*) as total FROM t_usuario WHERE TipoUsuario = :idTipoUsuario");
            $sentUsuariosAsociados->bindParam(':idTipoUsuario', $IdTipoUsuario, PDO::PARAM_INT);
            $sentUsuariosAsociados->execute();
            $usuariosAsociados = $sentUsuariosAsociados->fetch(PDO::FETCH_OBJ);
            
            $tieneUsuariosAsociados = ($usuariosAsociados->total > 0);
?>
    <div class="modal fade" id="EliminarTipoUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="title">Eliminar Tipo de Usuario</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="EliminarTipoUsuario" id="EliminarTipoUsuario" method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-12" style="text-align: center;">
                                <?php if ($tieneUsuariosAsociados): ?>
                                    <div class="alert alert-warning" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> No se puede eliminar este tipo de usuario porque tiene usuarios asociados.
                                    </div>
                                    <p>Debe cambiar el tipo de usuario de los usuarios asociados antes de eliminarlo.</p>
                                <?php else: ?>
                                    <label for="Id">¿Estás seguro de eliminar el tipo de Usuario?</label>
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdTipoUsuario" class="form-control" type="text" name="IdTipoUsuario" value="<?php echo htmlspecialchars($Tipos->IdTipoUsuario); ?>" hidden>

                                    <label for="TipoUsuario">Tipo de Usuario:</label>
                                    <input id="TipoUsuario" class="form-control" type="text" name="TipoUsuario" value="<?php echo htmlspecialchars($Tipos->TipoUsuario); ?>" required readonly>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align: center;">
                                <div class="form-group">
                                    <?php if (!$tieneUsuariosAsociados): ?>
                                        <button class="btn btn-success" type="submit" name="Mov" value="EliminarTipoUsuario" id="Mov">Sí</button>
                                    <?php endif; ?>
                                    <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal"><?php echo $tieneUsuariosAsociados ? 'Cerrar' : 'No'; ?></button>
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