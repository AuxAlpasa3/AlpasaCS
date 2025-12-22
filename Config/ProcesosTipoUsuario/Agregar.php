<?php 
   
    Include_once "../../templates/Sesion.php";
    $MaximotUsuarios = $Conexion->query("SELECT case when isnull(max(IdTipoUsuario),0)=0 then 1 else Max(IdTipoUsuario)+1 end as IdTipoUsuario FROM t_tipoUsuario");
    $documentos = $MaximotUsuarios->fetchAll(PDO::FETCH_OBJ);
    foreach($documentos as $doc)
    {
        $CONT =$doc->IdTipoUsuario;
    }
?>
<div class="modal fade"  id="NuevoTipoUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
       <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="title">Nuevo Tipo de Usuarios</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="AgregarTipoUsuario" id="AgregarTipoUsuario"  method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Id">Id Tipo de Embalaje</label>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdTipoUsuario" class="form-control" type="text" name="IdTipoUsuario" value="<?php echo $CONT; ?>"readonly >
                                </div>
                            </div>
                            <div class="col-md-8" style="text-align: center;">
                                <label for="TipoUsuario">Tipo de Usuario</label>
                                <div class="form-group">
                                    <input id="TipoUsuario" class="form-control" type="text" name="TipoUsuario" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="Mov" value="AgregarTipoUsuario" id="Mov">Agregar</button>
                                <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
