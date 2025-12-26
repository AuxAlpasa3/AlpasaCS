 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Control de Accesos - Alpasa</title>
    <!-- Custom fonts for this template-->
    <link href="<?php echo base_url; ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="<?php echo base_url; ?>css/sb-admin-2.min.css" rel="stylesheet">
</head>

<?php
 $Empresa= $row['NomEmpresa'];
 $Ubicacion= $row['NomCorto'];
 $Cargo= $row['NomCargo']; 
 $Depto= $row['NomDepto'];


?>
<div class="modal fade" id="modificarEmpleado_<?php echo $ID; ?>_<?php echo $usuario; ?>" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Modificar Empleado</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
              <div class="modal-body">
                      <form name="editar" id="editar" action="mod_personal/procesos.php" method="POST">
                        <div class="row">
                          <div class="col-md-6">
                              <div class="form-group" >
                                   <input id="PersonalID" value="<?php echo $ID ?>" name="PersonalID" hidden="true">
                                   <input id="user" value="<?php echo $usuario ?>" name="user" hidden="true">

                                  <div class="form-group">
                                    <img src="<?php  echo $row['RutaFoto']?>" width="90" height="100" name="Foto2"/>

                                      <input id="Foto2" class="form-control" type="file" name="Foto2"  accept="image/*">

                                       <input id="Foto" class="form-control" type="text" name="Foto" value="<?php  echo $row['RutaFoto'];?>" hidden="true">
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label for="Status">Estatus</label>
                                  <select class="form-control" name="s_estatus" id="s_estatus" required>
                                      <option value="1" <?php if($row['Status']==1) echo 'selected="selected"';?>>Activo</option>
                                      <option value="0" <?php if($row['Status']==0) echo 'selected="selected"';?>>Inactivo</option>
                                  </select>
                              </div>
                          </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                      <label for="nombre">Nombre</label>
                                      <input id="nombre" class="form-control" type="text" name="nombre" required placeholder="Nombre del Empleado" value="<?php echo $row['Nombre'];?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                          <label for="apPaterno">Apellido Paterno</label>
                                          <input id="apPaterno" class="form-control" type="text" name="apPaterno" required placeholder="Apellido Paterno" value="<?php  echo $row['ApPaterno'];?> ">
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                          <label for="apMaterno">Apellido Materno</label>
                                          <input id="apMaterno" class="form-control" type="text" name="apMaterno" required placeholder="Apellido Materno" value="<?php  echo $row['ApMaterno'];?> ">
                                  </div>
                              </div>
                              <div class="col-sm-6" >
                                  <div class="form-group">
                                      <label for="s_Empresa">Empresa: </label>
                                        <?php  
                                        $query = "SELECT *FROM t_Empresa;"; 
                                        $resultado=$mysqli->query($query); ?>
                                        <select class="form-control" name="s_Empresa" id="s_Empresa" required>
                                            <option value="0">Seleccionar Empresa</option>
                                                <?php 
                                        while($row = $resultado->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['IdEmpresa']; ?>" 
                                                <?php if($row['NomEmpresa']==$Empresa) echo 'selected="selected"'; ?>> 
                                                <?php echo $row['NomEmpresa']; ?> 
                                            </option>
                                          <?php } ?>
                                        </select>
                                  </div>
                              </div>
                              <div class="col-sm-6" >
                                  <div class="form-group">
                                      <label for="s_Ubicacion">Ubicación: </label>
                                          <?php  
                                                  $query = "SELECT *FROM t_ubicacion;"; 
                                                  $resultado=$mysqli->query($query); ?>
                                              <select class="form-control" name="s_Ubicacion" id="s_Ubicacion" required>
                                                  <option value="0">Seleccionar Ubicacion</option>
                                  <?php while($row = $resultado->fetch_assoc()) { ?>
                                  <option value="<?php echo $row['IdUbicacion']; ?>"<?php if($row['NomCorto']==$Ubicacion) echo 'selected="selected"'; ?>> <?php echo $row['NomCorto']; ?>
                                                  </option>
                                                  <?php } ?>
                                                  </select>
                                  </div>
                              </div>
                              <div class="col-sm-6" >
                                  <div class="form-group">
                                      <label for="s_depto">Departamento: </label>
                                          <?php  
                                                  $query = "SELECT *FROM t_departamento;"; 
                                                  $resultado=$mysqli->query($query); ?>
                                              <select class="form-control" name="s_depto" id="s_depto" required>
                                                  <option value="0">Seleccionar Departamento</option>
                                                      <?php while($row = $resultado->fetch_assoc()) { ?>
                                                  <option value="<?php echo $row['IdDepartamento']; ?>"
                                                    <?php if($row['NomDepto']==$Depto) echo 'selected="selected"'; ?>><?php echo $row['NomDepto']; ?>
                                                  </option>
                                                  <?php } ?>
                                                  </select>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="s_cargo">Cargo: </label>
                                          <?php 
                                              $query = "SELECT *FROM t_cargo;"; 
                                              $resultado=$mysqli->query($query); ?>
                                              <select class="form-control" name="s_cargo" id="s_cargo"  required>
                                                  <option value="0">Seleccionar Cargo</option>
                                                      <?php while($row = $resultado->fetch_assoc()) { ?>
                                                  <option value="<?php echo $row['IdCargo']; ?>"
                                                      <?php if($row['NomCargo']==$Cargo) echo 'selected="selected"'; ?>> 
                                                      <?php echo $row['NomCargo']; ?></option>
                                              <?php } ?>            
                                              </select>
                                  </div>
                              </div> 
                               <div class="col-md-12" style="text-align: center;">
                                  <div class="form-group">
                                      <button class="btn btn-success" type="submit" name="Mov" value="editar" id="Mov">Editar</button>
                                      <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
                                  </div>
                              </div>
                        </div>
                    </form>
              </div>
        </div>
    </div>
</div>

