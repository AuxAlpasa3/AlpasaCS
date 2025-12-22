
    <div class="modal fade"  id="EliminarArticulo_<?php echo $Articulo->IdArticulo;?>" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header text-white" style="background-color: #d94f00;">
                            <h5 class="modal-title text-white" id="title">Eliminar Articulo</h5>
                            <button class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="EliminarArticulo" id="EliminarArticulo"  method="POST" enctype="multipart/form-data">
                                <div class="row" style="align-content: center;">
                                    <div class="col-md-12" style="text-align: center;">
                                        <label for="Id">¿Estas seguro de eliminar el Articulo?</label>
                                        <div class="form-group">

                                            <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">

                                            <input id="IdArticulo" class="form-control" type="text" name="IdArticulo" value="<?php echo $Articulo->IdArticulo; ?>" hidden>

                                            <input id="MaterialNo" class="form-control" type="text" name="MaterialNo" value="<?php echo $Articulo->MaterialNo; ?>" required readonly>

                                            <input id="MaterialShape" class="form-control" type="text" name="MaterialShape" value="<?php echo $Articulo->MaterialShape; ?>" required readonly>
                                        </div>
                                    </div>
                                <div class="col-md-12" style="text-align: center;">
                                    <div class="form-group">
                                        <button class="btn btn-success" type="submit" name="Mov" value="EliminarArticulo" id="Mov">Si</button>
                                        <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">No</button>
                                    </div>
                                </div>
                              </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </html>