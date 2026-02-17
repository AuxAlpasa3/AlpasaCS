<?php 

    Include_once "../../templates/Sesion.php";

    $IdUbicacion = $_GET['IdUbicacionInterna'] ?? 0;

    $sentUbicacion = $Conexion->query("SELECT IdUbicacion,NomCorto,NomLargo,Ciudad,Estado,Pais FROM t_ubicacion_interna where IdUbicacion=$IdUbicacion");
    $UbicacionesInternas = $sentUbicacion->fetchAll(PDO::FETCH_OBJ);
     foreach($UbicacionesInternas as $UbicacionInterna){
        $IdUbicacion=$UbicacionInterna->IdUbicacion;
?>
<div class="modal fade"  id="ModificarUbicacionInterna" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Modificar Ubicacion Interna</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="ModificarUbicacionInterna" id="ModificarUbicacionInterna"  method="POST" enctype="multipart/form-data">
                    <div class="row" style="align-content: center;">
                            <div class="col-md-2" style="text-align: center;">
                                <label for="Id">Id Ubicacion</label>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdUbicacion" class="form-control" type="text" name="IdUbicacion" value="<?php echo $UbicacionInterna->IdUbicacion; ?>" readonly >
                                </div>
                            </div>
                            <div class="col-md-5" style="text-align: center;">
                                <label for="NomCorto">NomCorto</label>
                                <div class="form-group">
                                    <input id="NomCorto" value="<?php echo $UbicacionInterna->NomCorto;?>" class="form-control" type="text" name="NomCorto" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="col-md-5" style="text-align: center;">
                                <label for="NomLargo">NomLargo</label>
                                <div class="form-group">
                                    <input id="NomLargo" value="<?php echo $UbicacionInterna->NomLargo;?>" class="form-control" type="text" name="NomLargo" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Ciudad">Ciudad</label>
                                <div class="form-group">
                                    <input id="Ciudad" value="<?php echo $UbicacionInterna->Ciudad;?>" class="form-control" type="text" name="Ciudad" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Estado">Estado</label>
                                <div class="form-group">
                                    <input id="Estado" value="<?php echo $UbicacionInterna->Estado;?>" class="form-control" type="text" name="Estado" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Pais">Pais</label>
                                <div class="form-group">
                                    <input id="Pais" value="<?php echo $UbicacionInterna->Pais;?>" class="form-control" type="text" name="Pais" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                           <div class="col-md-12" style="text-align: center;">
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit" name="Mov" value="ModificarUbicacionInterna" id="Mov">Modificar</button>
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