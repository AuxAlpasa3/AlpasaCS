<?php 
    Include_once "../../templates/Sesion.php";
    $IdRevision = $_GET['IdRevision'] ?? 0;

                       $sentRevision = $Conexion->query("SELECT t1.IdRevision,t2.TipoRevision,t1.Descripcion,FORMAT(t1.FechaInicio,'dd/MM/yyyy') AS FechaInicio ,FORMAT(t1.FechaFinal,'dd/MM/yyyy') AS FechaFinal,t1.Estatus
                          FROM dbo.t_Revision AS t1
                          INNER JOIN dbo.t_tipoRevision AS t2 ON t2.IdTipoRevision = t1.TipoRevision
                          where IdRevision=$IdRevision");
                          $Revisiones = $sentRevision->fetchAll(PDO::FETCH_OBJ);
                    
                         foreach($Revisiones as $Revision){
                          $IdRevision=$Revision->IdRevision;
?>
    <div class="modal fade"  id="EliminarRevision" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-white" style="background-color: #d94f00;">
                            <h5 class="modal-title text-white" id="title">Eliminar Revision</h5>
                            <button class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="EliminarRevision" id="EliminarRevision"  method="POST" enctype="multipart/form-data">
                                <div class="row" style="align-content: center;">
                                    <div class="col-md-12" style="text-align: center;">
                                        <label for="Id">¿Estas seguro de eliminar la Revision <?php echo $IdRevision;?>?</label>
                                        <div class="form-group">

                                            <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">

                                            <input id="IdRevision" class="form-control" type="text" name="IdRevision" value="<?php echo $Revision->IdRevision; ?>"  ReadOnly>
                                             <input id="Descripcion" class="form-control" type="text" name="Descripcion" value="<?php echo $Revision->Descripcion; ?>"  ReadOnly>
                                        </div>
                                    </div>
                                <div class="col-md-12" style="text-align: center;">
                                    <div class="form-group">
                                        <button class="btn btn-success" type="submit" name="Mov" value="EliminarRevision" id="Mov">Si</button>
                                        <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">No</button>
                                    </div>
                                </div>
                              </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
<?php } ?>