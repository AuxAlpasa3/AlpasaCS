<?php 
Include_once "../../templates/Sesion.php";

$CodBarras = $_GET['CodBarras'] ?? 0;
             $sentSalidas = $Conexion->query("SELECT t1.IdTarja AS IdTarja,t1.IdTarja AS IdTarjaNum, t1.CodBarras AS CodBarras, t1.CodBarras AS CodBarrasNum, 
                          CONVERT(DATE,t1.FechaSalida) as FechaSalida, t1.FechaProduccion,t1.IdArticulo,t2.MaterialNo, trim(Concat(t2.Material,t2.Shape)) as MaterialShape, 
                          t1.Piezas,t1.NumPedido,t1.NetWeight,t1.GrossWeight,t1.Cliente,t4.NombreCliente,t1.IdRemision,t1.IdLinea,t1.Transportista,t1.Placas,t1.Chofer,
                          t1.Checador,t1.Supervisor,t6.NumRecinto,t1.Almacen
                          FROM t_salida as t1 
                          INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
                          INNER JOIN t_cliente as t4 on t1.Cliente=t4.IdCliente 
                          INNER JOIN t_usuario_almacen as t5 on t1.Almacen=t5.IdAlmacen
                          INNER JOIN t_almacen as t6 on t1.Almacen=t6.IdAlmacen
              INNER JOIN t_remision_encabezado as t7 on t1.IdRemision=t7.IdRemisionEncabezado
                          WHERE t1.ESTATUS IN (0,1,2,3) and t5.IdUsuario=$IdUsuario and CodBarras=$CodBarras order by t1.IdRemision, t1.IdLinea ");
                $Salidas = $sentSalidas->fetchAll(PDO::FETCH_OBJ);
                  foreach($Salidas as $Salida){
                            $Almacen=$Salida->Almacen;
                              ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="modal fade"  id="EditarSalida" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Modificar CodBarras: <?php echo $Salida->CodBarras;?></h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="ModificarSalida" id="ModificarSalida"  method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-2" style="text-align: center;">
                                <label for="CodBarras">CodBarras</label>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input type="hidden" id="IdTarja" name="IdTarja" value="<?php echo $Salida->IdTarja; ?>">
                                    <input type="hidden" id="IdRemision" name="IdRemision" value="<?php echo $Salida->IdRemision; ?>">
                                    <input type="hidden" id="Almacen" name="Almacen" value="<?php echo $Salida->Almacen; ?>">
                                    <input type="hidden" id="IdArticulo" name="IdArticulo" value="<?php echo $Salida->IdArticulo; ?>">
                                    <input type="hidden" id="CodBarrasNum" name="CodBarrasNum" value="<?php echo $Salida->CodBarras; ?>">
                                    
                                    <input id="CodBarras" class="form-control" type="text" name="CodBarras" value="<?php  echo $Salida->NumRecinto."-". sprintf("%06d", $CodBarras=$Salida->CodBarras);?>"readonly >

                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="MaterialNo">MaterialNo: </label>
                                <div class="form-group">
                                    <input id="MaterialNo" value="<?php echo $Salida->MaterialNo;?>"  class="form-control"  type="text" name="MaterialNo" readonly>
                                </div>
                            </div>
                            <div class="col-md-6" style="text-align: center;">
                                <label for="MaterialShape">MaterialShape: </label>
                                <div class="form-group">
                                    <input id="MaterialShape" value="<?php echo $Salida->MaterialShape;?>"  class="form-control"  type="text" name="MaterialShape" readonly>
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="FechaProduccion">FechaProduccion: </label>
                                <div class="form-group">
                                    <input id="FechaProduccion" value="<?php echo $Salida->FechaProduccion;?>" class="form-control" type="date" name="FechaProduccion">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="FechaSalida">FechaSalida: </label>
                                <div class="form-group">
                                    <input id="FechaSalida" value="<?php echo $Salida->FechaSalida;?>" class="form-control" type="date" name="FechaSalida">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Piezas">Piezas: </label>
                                <div class="form-group">
                                    <input id="Piezas" value="<?php echo $Salida->Piezas;?>"  class="form-control"  type="number" name="Piezas">
                                </div>
                            </div>
                             <div class="col-md-4" style="text-align: center;">
                                <label for="NumPedido">NumPedido: </label>
                                <div class="form-group">
                                    <input id="NumPedido" value="<?php echo $Salida->NumPedido;?>"  class="form-control"  type="text" name="NumPedido">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="NetWeight">NetWeight: </label>
                                <div class="form-group">
                                    <input id="NetWeight" value="<?php echo $Salida->NetWeight;?>"  class="form-control"  type="text" name="NetWeight">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="GrossWeight">GrossWeight: </label>
                                <div class="form-group">
                                    <input id="GrossWeight" value="<?php echo $Salida->GrossWeight;?>"  class="form-control"  type="text" name="GrossWeight">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Transportista">Transportista: </label>
                                <div class="form-group">
                                    <input id="Transportista" value="<?php echo $Salida->Transportista;?>"  class="form-control"  type="text" name="Transportista">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Placas">Placas: </label>
                                <div class="form-group">
                                    <input id="Placas" value="<?php echo $Salida->Placas;?>"  class="form-control"  type="text" name="Placas">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Chofer">Chofer: </label>
                                <div class="form-group">
                                    <input id="Chofer" value="<?php echo $Salida->Chofer;?>"  class="form-control"  type="text" name="Chofer">
                                </div>
                            </div>
                             <div class="col-md-6" style="text-align: center;">
                                <label for="Checador">Checador</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT t1.idUsuario, t1.Nombrecolaborador from t_usuario as t1 
                                            INNER JOIN t_usuario_almacen as t2 on t1.IdUsuario=t2.IdUsuario 
                                            where TipoUsuario=5  and t2.IdAlmacen=$Almacen;"); 
                                          $Cli = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control" name="Checador" id="Ubicacion">
                                             <?php foreach($Cli as $Clientes){ ?>
                                          <option value="<?php echo $Clientes->idUsuario; ?>" 
                                                <?php if($Clientes->idUsuario==$Salida->Checador) 
                                                echo 'selected="selected"'; ?>>  
                                            <?php echo $Clientes->Nombrecolaborador ; ?></option>
                                                <?php } ?>
                                        </select>
                                </div>
                            </div> 
                            <div class="col-md-6" style="text-align: center;">
                                <label for="Supervisor">Supervisor</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT t1.idUsuario, t1.Nombrecolaborador from t_usuario as t1 
                                            INNER JOIN t_usuario_almacen as t2 on t1.IdUsuario=t2.IdUsuario 
                                            where TipoUsuario in(2,3,4) and t2.IdAlmacen=$Almacen;"); 
                                          $Cli = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control" name="Supervisor" id="Ubicacion">
                                             <?php foreach($Cli as $Clientes){ ?>
                                          <option value="<?php echo $Clientes->idUsuario; ?>" 
                                                <?php if($Clientes->idUsuario==$Salida->Supervisor) 
                                                echo 'selected="selected"'; ?>>  
                                            <?php echo $Clientes->Nombrecolaborador ; ?></option>
                                                <?php } ?>
                                        </select>
                                </div>
                            </div>
                             
                       <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="Mov" value="ModificarSalida" id="Mov">Modificar</button>
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