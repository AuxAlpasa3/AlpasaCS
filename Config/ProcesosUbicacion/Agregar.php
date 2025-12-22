<?php 
   
    Include_once "../../templates/Sesion.php";
    $MaximoUbicacion = $Conexion->query("SELECT case when isnull(max(IdUbicacion),0)=0 then 1 else Max(IdUbicacion)+1 end as IdUbicacion FROM t_ubicacion");
    $documentos = $MaximoUbicacion->fetchAll(PDO::FETCH_OBJ);
    foreach($documentos as $doc)
    {
        $CONT =$doc->IdUbicacion;
    }
?>
<div class="modal fade"  id="NuevaUbicacion" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
       <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="title">Nueva Ubicacion</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="AgregarUbicacion" id="AgregarUbicacion"  method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-2" style="text-align: center;">
                                <label for="Id">Id Ubicacion</label>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdUbicacion" class="form-control" type="text" name="IdUbicacion" value="<?php echo $CONT; ?>"readonly >
                                </div>
                            </div>
                            <div class="col-md-10" style="text-align: center;">
                                <label for="Ubicacion">Ubicacion</label>
                                <div class="form-group">
                                    <input id="Ubicacion" class="form-control" type="text" name="Ubicacion" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="Mov" value="AgregarUbicacion" id="Mov">Agregar</button>
                                <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
