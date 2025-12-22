<?php 
    Include_once "../../templates/Sesion.php";

$CodBarras = $_GET['CodBarras'] ?? 0;
             $sentIngresos = $Conexion->query(" SELECT t1.IdTarja AS IdTarja, t1.IdTarja as IdTarjaNum ,t1.CodBarras AS CodBarras,t1.CodBarras as CodBarrasNum, CONVERT(DATE,t1.FechaIngreso) as FechaIngreso, t1.FechaProduccion,t1.IdArticulo,t2.MaterialNo,  trim(Concat(t2.Material,' ',t2.Shape)) as MaterialShape, t1.Piezas,t1.NumPedido,t1.NetWeight,t1.GrossWeight,t1.IdUbicacion, t3.Ubicacion,t5.EstadoMaterial as EstadoMercancia,t1.Origen,t1.Cliente,t4.NombreCliente,t1.IdRemision,t1.IdLinea,t1.Transportista,trim(t1.Placas) as Placas,t1.Chofer,t1.Checador,t1.Supervisor, (case when t1.Comentarios is null then 'SIN COMENTARIOS' ELSE  t1.Comentarios end) as Comentarios, PaisOrigen,NoTarima,t8.NumRecinto,t1.Almacen
                FROM t_ingreso as t1 
                INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
                LEFT JOIN t_ubicacion as t3 on t1.IdUbicacion=t3.IdUbicacion
                INNER JOIN t_cliente as t4 on t1.Cliente=t4.IdCliente 
                INNER JOIN t_estadoMaterial as t5 on t1.EstadoMercancia=t5.IdEstadoMaterial
                INNER JOIN t_usuario_almacen as t6 on t1.Almacen=t6.IdAlmacen 
                INNER JOIN t_remision_encabezado as t7 on t1.IdRemision=t7.IdRemisionEncabezado
                INNER JOIN t_almacen as t8 on t1.Almacen=t8.IdAlmacen
                WHERE t1.ESTATUS IN (0,1,2,3)  and t6.IdUsuario=$IdUsuario and CodBarras=$CodBarras");
                $Ingresos = $sentIngresos->fetchAll(PDO::FETCH_OBJ);
                  foreach($Ingresos as $Ingreso){
                              ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="modal fade" id="EditarIngreso" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Modificar CodBarras: <?php echo $Ingreso->NumRecinto."-". sprintf("%06d", $CodBarras=$Ingreso->CodBarras);?></h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="ModificarIngreso" id="ModificarIngreso"  method="POST" enctype="multipart/form-data">
                        <div class="row" style="align-content: center;">
                            <div class="col-md-2" style="text-align: center;">
                                <label for="CodBarras">CodBarras</label>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                    <input type="hidden" id="IdTarja" name="IdTarja" value="<?php echo $Ingreso->IdTarjaNum; ?>">
                                    <input type="hidden" id="IdRemision" name="IdRemision" value="<?php echo $Ingreso->IdRemision; ?>">
                                    <input type="hidden" id="IdArticulo" name="IdArticulo" value="<?php echo $Ingreso->IdArticulo; ?>">
                                    <input type="hidden" id="CodBarrasNum" name="CodBarrasNum" value="<?php echo $Ingreso->CodBarrasNum; ?>">
                                    
                                    <input id="CodBarras" class="form-control" type="text" name="CodBarras" value="<?php echo $Ingreso->NumRecinto."-". sprintf("%06d", $CodBarras=$Ingreso->CodBarras);?>"readonly >
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="MaterialNo">MaterialNo: </label>
                                <div class="form-group">
                                    <input id="MaterialNo" value="<?php echo $Ingreso->MaterialNo;?>"  class="form-control"  type="text" name="MaterialNo" readonly>
                                </div>
                            </div>
                            <div class="col-md-6" style="text-align: center;">
                                <label for="MaterialShape">MaterialShape: </label>
                                <div class="form-group">
                                    <input id="MaterialShape" value="<?php echo $Ingreso->MaterialShape;?>"  class="form-control"  type="text" name="MaterialShape" readonly>
                                </div>
                            </div>
                            <div class="col-md-3" style="text-align: center;">
                                <label for="FechaProduccion">FechaProduccion: </label>
                                <div class="form-group">
                                    <input id="FechaProduccion" value="<?php echo $Ingreso->FechaProduccion;?>" class="form-control" type="date" name="FechaProduccion">
                                </div>
                            </div>
                            <div class="col-md-3" style="text-align: center;">
                                <label for="FechaIngreso">FechaIngreso: </label>
                                <div class="form-group">
                                    <input id="FechaIngreso" value="<?php echo $Ingreso->FechaIngreso;?>" class="form-control" type="date" name="FechaIngreso">
                                </div>
                            </div>
                            <div class="col-md-3" style="text-align: center;">
                                <label for="Piezas">Piezas: </label>
                                <div class="form-group">
                                    <input id="Piezas" value="<?php echo $Ingreso->Piezas;?>"  class="form-control"  type="number" name="Piezas" required-field>
                                </div>
                            </div>
                             <div class="col-md-3" style="text-align: center;">
                                <label for="Ubicacion">Ubicacion</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT t1.IdUbicacion,t1.Ubicacion from t_ubicacion as t1 
                                                inner join t_usuario_almacen as t2 on t1.Almacen=t2.IdAlmacen
                                                where t2.IdAlmacen=$Ingreso->Almacen;"); 
                                          $Cli = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control select2" name="Ubicacion" id="Ubicacion">
                                             <?php foreach($Cli as $Clientes){ ?>
                                          <option value="<?php echo $Clientes->IdUbicacion; ?>" 
                                                <?php if($Clientes->IdUbicacion==$Ingreso->IdUbicacion) 
                                                echo 'selected="selected"'; ?>>  
                                            <?php echo $Clientes->Ubicacion ; ?></option>
                                                <?php } ?>
                                        </select>
                                </div>
                            </div>
                             <div class="col-md-4" style="text-align: center;">
                                <label for="NumPedido">NumPedido: </label>
                                <div class="form-group">
                                    <input id="NumPedido" value="<?php echo $Ingreso->NumPedido;?>"  class="form-control"  type="text" name="NumPedido">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="NetWeight">NetWeight: </label>
                                <div class="form-group">
                                    <input id="NetWeight" value="<?php echo $Ingreso->NetWeight;?>"  class="form-control"  type="text" name="NetWeight">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="GrossWeight">GrossWeight: </label>
                                <div class="form-group">
                                    <input id="GrossWeight" value="<?php echo $Ingreso->GrossWeight;?>"  class="form-control"  type="text" name="GrossWeight">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="PaisOrigen">Destino: </label>
                                <div class="form-group">
                                    <input id="PaisOrigen" value="<?php echo $Ingreso->PaisOrigen;?>"  class="form-control"  type="text" name="PaisOrigen" onkeyup="this.value = this.value.toUpperCase()" required-field>
                                </div>
                            </div>
                             <div class="col-md-4" style="text-align: center;">
                                <label for="Origen">Origen: </label>
                                <div class="form-group">
                                    <input id="Origen" value="<?php echo $Ingreso->Origen;?>"  class="form-control"  type="text" name="Origen" 
                                   onkeyup="this.value = this.value.toUpperCase()" required-field>
                                </div>
                            </div> 
                            <div class="col-md-4" style="text-align: center;">
                                <label for="NoTarima">NoTarima: </label>
                                <div class="form-group">
                                    <input id="NoTarima" value="<?php echo $Ingreso->NoTarima;?>"  class="form-control"  type="text" name="NoTarima" >
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Transportista">Transportista: </label>
                                <div class="form-group">
                                    <input id="Transportista" value="<?php echo $Ingreso->Transportista;?>"  class="form-control"  type="text" name="Transportista" 
                                   onkeyup="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Placas">Placas: </label>
                                <div class="form-group">
                                    <input id="Placas" value="<?php echo $Ingreso->Placas;?>"  class="form-control"  type="text" name="Placas" 
                                   onkeyup="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="Chofer">Chofer: </label>
                                <div class="form-group">
                                    <input id="Chofer" value="<?php echo $Ingreso->Chofer;?>"  class="form-control"  type="text" name="Chofer" 
                                   onkeyup="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                             <div class="col-md-6" style="text-align: center;">
                                <label for="Checador">Checador</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT idUsuario, Nombrecolaborador from t_usuario where TipoUsuario=5;"); 
                                          $Cli = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control select2" name="Checador" id="Checador">
                                             <?php foreach($Cli as $Clientes){ ?>
                                          <option value="<?php echo $Clientes->idUsuario; ?>" 
                                                <?php if($Clientes->idUsuario==$Ingreso->Checador) 
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
                                          $sentencia = $Conexion->query("SELECT idUsuario, Nombrecolaborador from t_usuario where TipoUsuario in(2,3,4);"); 
                                          $Cli = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                        <select class="form-control select2" name="Supervisor" id="Supervisor">
                                             <?php foreach($Cli as $Clientes){ ?>
                                          <option value="<?php echo $Clientes->idUsuario; ?>" 
                                                <?php if($Clientes->idUsuario==$Ingreso->Supervisor) 
                                                echo 'selected="selected"'; ?>>  
                                            <?php echo $Clientes->Nombrecolaborador ; ?></option>
                                                <?php } ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                <label for="EstadoMaterial" class="form-label required-field">Estado del Material</label>
                                <div class="form-group">
                                        <?php  
                                          $sentencia = $Conexion->query("SELECT IdEstadoMaterial, EstadoMaterial from  t_estadoMaterial;"); 
                                          $Cli = $sentencia->fetchAll(PDO::FETCH_OBJ);
                                        ?>
                                       <select class="form-control select2-multiple" name="EstadoMaterial[]" id="EstadoMaterial" multiple="multiple">
                                             <?php foreach($Cli as $Clientes){ ?>
                                          <option value="<?php echo $Clientes->IdEstadoMaterial; ?>" 
                                                <?php if($Clientes->IdEstadoMaterial==$Ingreso->EstadoMercancia) 
                                                echo 'selected="selected"'; ?>>  
                                            <?php echo $Clientes->EstadoMaterial ; ?></option>
                                                <?php } ?>
                                        </select>
                                        <div class="form-text">Seleccione uno o varios estados para el material. Use Ctrl+Click para seleccionar múltiples opciones.</div>
                                </div>
                            </div>
                             <div class="col-md-8" style="text-align: center;">
                                <label for="Comentarios">Comentarios: </label>
                                <div class="form-group">
                                    <input id="Comentarios" value="<?php echo $Ingreso->Comentarios;?>"  class="form-control"  type="text" name="Comentarios"  onkeyup="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                             
                       <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <button class="btn btn-success" type="submit" name="Mov" value="ModificarIngreso" id="Mov">Modificar</button>
                                <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .select2-container.select2-multiple {
            z-index: 9999 !important;
        }

        .select2-dropdown.select2-multiple {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
        }

        .select2-container .select2-selection--single {
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
</style>
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%', 
            dropdownParent: $('#EditarIngreso') 
        });
        
        $('.select2-multiple').select2({
            width: '100%',
            theme: 'classic',
            placeholder: "Seleccione uno o varios estados",
            allowClear: true,
            dropdownParent: $('#EditarIngreso')
        });
        
        $('#Ubicacion').select2({
            width: '100%',
            dropdownParent: $('#EditarIngreso'),
            placeholder: "Selecciona una ubicación"
        });
        
        $('#Checador').select2({
            width: '100%',
            dropdownParent: $('#EditarIngreso'),
            placeholder: "Selecciona un checador"
        });
        
        $('#Supervisor').select2({
            width: '100%',
            dropdownParent: $('#EditarIngreso'),
            placeholder: "Selecciona un supervisor"
        });
        
        $('#EstadoMaterial').select2({
            width: '100%',
            dropdownParent: $('#EditarIngreso'),
            placeholder: "Selecciona estado del material"
        });

    });
    </script>
</div>
<?php
}