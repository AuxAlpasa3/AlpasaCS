<?php
    Include_once "../../templates/Sesion.php";

    $IdUsuarioNum = $_GET['IdUsuario'] ?? 0;

    $sentUsuarios = $Conexion->prepare("SELECT t1.IdUsuario AS IdUsuarioNum, t1.Correo, t1.Usuario, t1.NombreColaborador, t1.TipoUsuario as TipoUsuarioNum, t2.TipoUsuario, t1.Contrasenia, t1.Estatus 
                                       FROM t_usuario as t1 
                                       INNER JOIN t_tipoUsuario as t2 ON t1.TipoUsuario = t2.IdTipoUsuario 
                                       WHERE t1.IdUsuario = :idUsuario");
    $sentUsuarios->bindParam(':idUsuario', $IdUsuarioNum, PDO::PARAM_INT);
    $sentUsuarios->execute();
    
    $Usuarios = $sentUsuarios->fetchAll(PDO::FETCH_OBJ);
    
    foreach($Usuarios as $Usuario) {
?>
<div class="modal fade" id="ModificarUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Modificar Usuario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="ModificarUsuario" id="ModificarUsuario" method="POST">
                    <div class="row" style="align-content: center;">
                        <div class="col-md-2" style="text-align: center;">
                            <label for="Id">IdUsuario</label>
                            <div class="form-group">
                                <input type="hidden" id="user" name="user" value="<?php echo htmlspecialchars($IdUsuario); ?>">
                                <input id="IdUsuario" class="form-control" type="text" name="IdUsuario" value="<?php echo htmlspecialchars($IdUsuarioNum); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-5" style="text-align: center;">
                            <label for="Usuario">Usuario</label>
                            <div class="form-group">
                                <input id="Usuario" class="form-control" type="text" name="Usuario" onKeyUp="this.value = this.value.toUpperCase();" value="<?php echo htmlspecialchars($Usuario->Usuario); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-5" style="text-align: center;">
                            <label for="Correo">Correo</label>
                            <div class="form-group">
                                <input id="Correo" class="form-control" type="email" name="Correo" onKeyUp="this.value = this.value.toUpperCase();" value="<?php echo htmlspecialchars($Usuario->Correo); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6" style="text-align: center;">
                            <label for="NColaborador">Nombre del Colaborador</label>
                            <div class="form-group">
                                <input id="NColaborador" class="form-control" type="text" name="NColaborador" onKeyUp="this.value = this.value.toUpperCase();" value="<?php echo htmlspecialchars($Usuario->NombreColaborador); ?>" required>
                            </div>
                        </div>

                        <div class="col-md-3" style="text-align: center;">
                            <label for="TUsuario">Tipo Usuario</label>
                            <div class="form-group">
                                <?php  
                                    $sentUsuario = $Conexion->query("SELECT * FROM t_tipoUsuario"); 
                                    $tusuario = $sentUsuario->fetchAll(PDO::FETCH_OBJ);
                                ?>
                                <select class="form-control" name="TUsuario" id="TUsuario" required>
                                    <?php foreach($tusuario as $tusuarios){ ?>
                                        <option value="<?php echo htmlspecialchars($tusuarios->IdTipoUsuario); ?>"
                                            <?php if($Usuario->TipoUsuarioNum == $tusuarios->IdTipoUsuario) 
                                                echo 'selected="selected"'; ?>>  
                                            <?php echo htmlspecialchars($tusuarios->TipoUsuario); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="text-align: center;">
                            <label for="Estatus">Estatus</label>
                            <div class="form-group">
                                <select class="form-control" name="Estatus" id="Estatus" required>
                                    <option value="1" <?php echo ($Usuario->Estatus == 1) ? 'selected="selected"' : ''; ?>>  
                                        ACTIVO
                                    </option>
                                    <option value="0" <?php echo ($Usuario->Estatus == 0) ? 'selected="selected"' : ''; ?>>  
                                        INACTIVO
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="Mov" value="ModificarUsuario" id="Mov">Actualizar</button>
                                <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
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
?>