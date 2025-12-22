<?php 

    Include_once "../../templates/Sesion.php";

    $IdTipoUsuario = $_GET['IdTipoUsuario'] ?? 0;

    $sentTUsuario = $Conexion->query("SELECT IdTipoUsuario, TipoUsuario FROM t_tipoUsuario where IdTipoUsuario=$IdTipoUsuario");
        $tiposusuarios = $sentTUsuario->fetchAll(PDO::FETCH_OBJ);
         foreach($tiposusuarios as $Tipos){
           $IdTipoUsuario=$Tipos->IdTipoUsuario;
                                    
?>
<div class="modal fade"  id="ModificarTipoUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Modificar Tipo de Usuarios</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="ModificarTipoUsuario" id="ModificarTipoUsuario"  method="POST" enctype="multipart/form-data">
                    <div class="row" style="align-content: center;">
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Id">IdTipoUsuario</label>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdTipoUsuario" class="form-control" type="text" name="IdTipoUsuario" value="<?php echo $Tipos->IdTipoUsuario; ?>" readonly >
                                </div>
                            </div>
                            <div class="col-md-8" style="text-align: center;">
                                <label for="TipoUsuario">Tipo de Usuario</label>
                                <div class="form-group">
                                    <input id="TipoUsuario" value="<?php echo $Tipos->TipoUsuario;?>" class="form-control" type="text" name="TipoUsuario" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                           <div class="col-md-12" style="text-align: center;">
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit" name="Mov" value="ModificarTipoUsuario" id="Mov">Modificar</button>
                                    <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
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