<?php
  include ('../Config/conexion.php');
  include "../Config/Header.php";

       $query = "SELECT max(IdPersonal) as IdPersonal FROM t_personal;";
              if ($ress = mysqli_query($mysqli, $query)) {

                while ($fila = mysqli_fetch_assoc($ress)) {
                    $IdPersonal = $fila["IdPersonal"];        
              }
            }
              $IdPersonal=$IdPersonal+1; 
?>
<div class="container-fluid">
    <button class="btn btn-primary btn-g" type="button" data-toggle="modal" data-target="#nuevoEmpleado" style="background-color:#d94f00; border-color:#d94f00;"><i class="fa fa-plus">Añadir Nuevo</i> </button>
    <section class="pt-2">
              <div class="table-responsive">
                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="auto" style="color:black; text-align: center;">IdBitacora</th>
                          <th width="auto" style="color:black; text-align: center;">Tabla</th>
                          <th width="auto" style="color:black; text-align: center;">Tipo del Movimiento</th>
                          <th width="auto" style="color:black; text-align: center;">Fecha</th>
                          <th width="3%" style="color:black; text-align: center;">Consulta</th>
                          <th width="auto" style="color:black; text-align: center;">Usuario</th>
                        </tr>
                      </thead>
                      <tbody>
                         <?php 
                            $sql = "SELECT IdBitacora, Tabla, FolMovimiento, Fecha, Consulta, Usuario FROM bitacora";
                            $result = mysqli_query($mysqli,$sql);
                            while($row = mysqli_fetch_object($result)){
                            ?>
                            <tr>
                                <td style="text-align: center"><?php echo $row->IdBitacora;?></td>
                                <td style="text-align: center"><?php echo $row->Tabla;?></td>
                                <td style="text-align: center"><?php echo $row->FolMovimiento;?></td>
                                <td style="text-align: center"><?php echo $row->Fecha;?></td>
                                <td style="text-align: center"><?php echo $row->Consulta;?></td>
                                <td style="text-align: center"><?php echo $row->Usuario;?></td>
                            </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                      </div>
                    </section>
                  </body>
                </div>

<div id="nuevoEmpleado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white" id="title">Registro Empleado</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="form2" id="form2" action="RegistrosPersonal" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo">Código</label>
                                <input type="hidden" id="id" name="id">
                                <input id="codigo" class="form-control" type="text" name="codigo" required placeholder="Codigo del estudiante">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dni">Dni</label>
                                <input id="dni" class="form-control" type="text" name="dni" required placeholder="Dni">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input id="nombre" class="form-control" type="text" name="nombre" required placeholder="Nombre completo">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="carrera">Carrera</label>
                                <input id="carrera" class="form-control" type="text" name="carrera" required placeholder="Carrera">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="telefono">Télefono</label>
                                <input id="telefono" class="form-control" type="text" name="telefono" required placeholder="Teléfono">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <input id="direccion" class="form-control" type="text" name="direccion" required placeholder="Dirección">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit" onclick="registrarEstudiante(event)" id="btnAccion">Registrar</button>
                                <button class="btn btn-danger" type="button" data-dismiss="modal">Atras</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modificarEmpleado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white" id="title">Modificar Empleado</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="form2" id="form2" action="RegistrosPersonal" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo">Código</label>
                                <input type="hidden" id="id" name="id">
                                <input id="codigo" class="form-control" type="text" name="codigo" required placeholder="Codigo del estudiante">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dni">Dni</label>
                                <input id="dni" class="form-control" type="text" name="dni" required placeholder="Dni">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input id="nombre" class="form-control" type="text" name="nombre" required placeholder="Nombre completo">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="carrera">Carrera</label>
                                <input id="carrera" class="form-control" type="text" name="carrera" required placeholder="Carrera">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="telefono">Télefono</label>
                                <input id="telefono" class="form-control" type="text" name="telefono" required placeholder="Teléfono">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <input id="direccion" class="form-control" type="text" name="direccion" required placeholder="Dirección">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit" onclick="registrarEstudiante(event)" id="btnAccion">Modificar</button>
                                <button class="btn btn-danger" type="button" data-dismiss="modal">Atras</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div  id="eliminarEmpleado" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Eliminar Empleado</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">¿Confirmo que quiero eliminar el empleado <?php echo $IdPersonal; ?> ?</div>
                  <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">No</button>
                    <a class="btn btn-primary" href="<?php echo base_url; ?>Control/Salir">Si</a>
                  </div>
            </div>
        </div>
</div>

<?php
include "../Config/Footer.php";
?>