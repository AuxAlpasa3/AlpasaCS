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
 
 $User= $row['Usuario'];
 $Contrasenia= $row['Contrasenia'];
 $Descripcion= $row['Descripcion']; 
 $ClaveEmpleado= $row['ClaveEmpleado'];
 $Status= $row['Estatus'];

?>
<div class="modal fade" id="modificarUsuario_<?php echo $ID;?>_<?php echo $usuario; ?>" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #808080;">
                <h5 class="modal-title text-white" id="title">Modificar Usuario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body">
              <form name="editar" id="editar" action="mod_Usuario/procesos.php" method="POST">
                <div class="row">
                  <div class="form-group" >
                    <input id="IdUsuario" value="<?php echo $ID?>" name="IdUsuario" hidden="true">
                    <input id="Usuario" value="<?php echo $usuario?>" name="Usuario" hidden="true">
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="User">Usuario:</label>
                      <input id="User" class="form-control" type="text" name="User" required placeholder="Usuario" value="<?php echo $User;?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="s_estatus">Estatus</label>
                      <select class="form-control" name="s_estatus" id="s_estatus" required>
                        <option value="1" <?php if($Status==1) echo 'selected="selected"';?>>Activo</option>
                        <option value="0" <?php if($Status==0) echo 'selected="selected"';?>>Inactivo</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="Descripcion">Descripción del usuario: </label>
                      <input id="Descripcion" class="form-control" type="textarea" name="Descripcion" required placeholder="Descripcion del Usuario"value="<?php echo $Descripcion; ?>">
                    </div>
                  </div>
                  <div class="col-sm-6" >
                    <div class="form-group">
                      <label for="s_Empleado">Empleado: </label>
                        <?php 
                              $query = "SELECT IdPersonal, Concat(Nombre,' ',ApPaterno,' ',ApMaterno) as Empleado FROM t_personal;"; 
                              $resultado=$mysqli->query($query); ?>
                            <select class="form-control" name="s_Empleado" id="s_Empleado" required>
                              <option value="0">Seleccionar Empleado</option> 
                                  <?php while($row = $resultado->fetch_assoc()) { ?>
                              <option value="<?php echo $row['IdPersonal']; ?>" 
                                  <?php if($row['IdPersonal']==$ClaveEmpleado) echo 'selected="selected"'; ?>> 
                                  <?php echo $row['Empleado']; ?></option>
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
</html>

