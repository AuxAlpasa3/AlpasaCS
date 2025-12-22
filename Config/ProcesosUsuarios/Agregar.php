<?php 
   
    Include_once "../../templates/Sesion.php";
    $MaximoUsuario = $Conexion->query("SELECT case when isnull(max(IdUsuario),0)=0 then 1 else Max(IdUsuario)+1 end as IdUsuario FROM t_usuario");
    $documentos = $MaximoUsuario->fetchAll(PDO::FETCH_OBJ);
    foreach($documentos as $doc)
    {
        $CONT =$doc->IdUsuario;
    }
?>
<div class="modal fade"  id="NuevoUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Nuevo Usuario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="AgregarUsuario" id="AgregarUsuario" method="POST">
                    <div class="row" style="align-content: center;">
                        <div class="col-md-2" style="text-align: center;">
                            <label for="Id">IdUsuario</label>
                            <div class="form-group">
                                <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                <input id="IdUsuario" class="form-control" type="text" name="IdUsuario" value="<?php echo $CONT; ?>"readonly >
                            </div>
                        </div>
                            <div class="col-md-5" style="text-align: center;">
                                <label for="Usuario">Usuario</label>
                                <div class="form-group">
                                    <input id="Usuario" class="form-control" type="text" name="Usuario" onKeyUp="this.value = this.value.toUpperCase();" required>
                                </div>
                            </div>
                            <div class="col-md-5" style="text-align: center;">
                                <label for="Correo">Correo</label>
                                <div class="form-group">
                                    <input id="Correo" class="form-control" type="text" name="Correo" onKeyUp="this.value = this.value.toUpperCase();" required>
                                </div>
                            </div>
                            <div class="col-md-8" style="text-align: center;">
                                <label for="NColaborador">Nombre del Colaborador</label>
                                <div class="form-group">
                                    <input id="NColaborador" class="form-control" type="text" name="NColaborador" onKeyUp="this.value = this.value.toUpperCase();" required>
                                </div>
                            </div>

                            <div class="col-md-4" style="text-align: center;">
                                <label for="TUsuario">Tipo Usuario</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT *FROM t_tipoUsuario;"); 
                                          $tusuario = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control" name="TUsuario" id="TUsuario" required>
                                            <option value="0">No Aplica</option>
                                             <?php foreach($tusuario as $usuarios){ ?>
                                          <option value="<?php echo $usuarios->IdTipoUsuario; ?>">  
                                            <?php echo $usuarios->TipoUsuario ; ?></option>
                                                <?php } ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-6" style="text-align: center;">
                              <div class="form-group">
                                <label for="pass1">Contraseña:</label>
                                <input id="pass1" class="form-control" type="Password" name="pass1" required>
                              </div>
                            </div>
                            <div class="col-md-6" style="text-align: center;">
                              <div class="form-group">
                                <label for="pass2">Vuelve a Escribir Contraseña:</label>
                                <input id="pass2" class="form-control" type="Password" name="pass2" required>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="Mov" value="AgregarUsuario" id="Mov">Agregar</button>
                                <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>    
