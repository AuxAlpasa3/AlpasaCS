<?php 

    Include_once "../../templates/Sesion.php";

    $IdAlmacen = $_GET['IdAlmacen'] ?? 0;

    $sentAlmacen = $Conexion->query("SELECT IdAlmacen,Almacen,Ubicacion FROM t_almacen where IdAlmacen=$IdAlmacen");
    $Almacenes = $sentAlmacen->fetchAll(PDO::FETCH_OBJ);
     foreach($Almacenes as $Almacen){
        $IdAlmacen=$Almacen->IdAlmacen;
?>
<div class="modal fade"  id="ModificarAlmacen" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Modificar Almacen</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="ModificarAlmacen" id="ModificarAlmacen"  method="POST" enctype="multipart/form-data">
                    <div class="row" style="align-content: center;">
                            <div class="col-md-2" style="text-align: center;">
                                <label for="Id">Id Almacen</label>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdAlmacen" class="form-control" type="text" name="IdAlmacen" value="<?php echo $Almacen->IdAlmacen; ?>" readonly >
                                </div>
                            </div>
                            <div class="col-md-5" style="text-align: center;">
                                <label for="Almacen">Almacen</label>
                                <div class="form-group">
                                    <input id="Almacen" value="<?php echo $Almacen->Almacen;?>" class="form-control" type="text" name="Almacen" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="col-md-5" style="text-align: center;">
                                <label for="Ubicacion">Ubicacion</label>
                                <div class="form-group">
                                    <input id="Ubicacion" value="<?php echo $Almacen->Ubicacion;?>" class="form-control" type="text" name="Ubicacion" onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                           <div class="col-md-12" style="text-align: center;">
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit" name="Mov" value="ModificarAlmacen" id="Mov">Modificar</button>
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