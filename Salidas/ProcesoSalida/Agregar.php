
                    
    <div class="modal fade"  id="nuevoArticulo_<?php echo $CONT;?>" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
       <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="title">Nueva Articulo</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="AgregarArticulo" id="AgregarArticulo"  method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-1" style="text-align: center;">
                                <label for="Id">Id</label>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input id="IdArticulo" class="form-control" type="text" name="IdArticulo" value="<?php echo $CONT; ?>"readonly >
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="MaterialNo">Material No.:</label>
                                <div class="form-group">
                                    <input id="MaterialNo" class="form-control" type="number" name="MaterialNo"  onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="col-md-5" style="text-align: center;">
                                <label for="MaterialShape">Material/Shape:</label>
                                <div class="form-group">
                                    <input id="MaterialShape" class="form-control" type="text" name="MaterialShape"  onKeyUp="this.value = this.value.toUpperCase();">
                                </div>
                            </div>
                            <div class="col-md-2" style="text-align: center;">
                                  <label for="Piezas">Piezas</label>
                                  <div class="form-group">
                                    <input type="number" id="Piezas" class="form-control" name="Piezas"/>
                                </div>
                            </div>
                            <div class="col-md-3" style="text-align: center;">
                                <label for="TMaterial">Tipo Material</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT *FROM t_tipoMaterial;"); 
                                          $tmaterial = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control" name="TMaterial" id="TMaterial">
                                            <option value="0">No Aplica</option>
                                             <?php foreach($tmaterial as $material){ ?>
                                          <option value="<?php echo $material->IdTipoMaterial; ?>">  
                                            <?php echo $material->TipoMaterial ; ?></option>
                                                <?php } ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-3" style="text-align: center;">
                                <label for="TEmbalaje">Tipo Embalaje</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT *FROM t_tipoEmbalaje;"); 
                                          $tembalaje = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control" name="TEmbalaje" id="TEmbalaje">
                                            <option value="0">No Aplica</option>
                                             <?php foreach($tembalaje as $embalaje){ ?>
                                          <option value="<?php echo $embalaje->IdTipoEmbalaje; ?>">  
                                            <?php echo $embalaje->TipoEmbalaje; ?></option>
                                                <?php } ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-3" style="text-align: center;">
                                <label for="rEstiba">Regla Estiba</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT *FROM t_reglaEstiba;"); 
                                          $rEstiba = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control" name="rEstiba" id="rEstiba">
                                            <option value="0">No Aplica</option>
                                             <?php foreach($rEstiba as $Estiba){ ?>
                                          <option value="<?php echo $Estiba->IdReglaEstiba; ?>">  
                                            <?php echo $Estiba->ReglaEstiba; ?></option>
                                                <?php } ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-3" style="text-align: center;">
                                <label for="Periodicidad">Periodicidad</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT *FROM t_periodicidad order by IdPeriodicidad asc;"); 
                                          $periodicidad = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control" name="Periodicidad" id="Periodicidad">
                                             <?php foreach($periodicidad as $per){ ?>
                                          <option value="<?php echo $per->IdPeriodicidad; ?>">  
                                            <?php   
                                                if($per->PeriodicidadMes==1)
                                                {
                                                  echo $per->PeriodicidadMes." Mes"; 
                                                }
                                                elseif($per->PeriodicidadMes==0)
                                                {
                                                  echo "Sin Periodicidad"; 
                                                }
                                                else 
                                                {
                                                  echo $per->PeriodicidadMes." Meses";                 
                                                }
                                                ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                </div>
                            </div>

                            
                            <div class="col-md-6" style="text-align: center;">
                                <label for="GrossWeightU">Peso Bruto Unitario</label>
                                <div class="form-group">
                                    <input  type="number" id="GrossWeightU" class="form-control" name="GrossWeightU"/>
                                </div>
                            </div>
                            <div class="col-md-6" style="text-align: center;">
                                <label for="NetWeightU">Peso Neto Unitario</label>
                                <div class="form-group">
                                    <input type="number" id="NetWeightU" class="form-control" name="NetWeightU"/>
                                </div>
                            </div>
                            <div class="col-md-6" style="text-align: center;">
                                <label for="GrossWeight">Peso Bruto</label>
                                <div class="form-group">
                                    <input type="number" id="GrossWeight" class="form-control" name="GrossWeight"/>
                                </div>
                            </div>
                            <div class="col-md-6" style="text-align: center;">
                                <label for="NetWeight">Peso Neto</label>
                                <div class="form-group">
                                    <input type="number" id="NetWeight" class="form-control" name="NetWeight"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="Mov" value="AgregarArticulo" id="Mov">Agregar</button>
                                <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
