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
<div class="modal fade"  id="nuevoUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #808080;">
                <h5 class="modal-title text-white" id="title">Registrar Usuario Web</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="nuevo" id="nuevo" action="mod_Usuario_Web/procesos.php" method="POST">
        <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Id">Id</label>
                        <input type="hidden" id="usuario" name="usuario" value="<?php echo $usuario; ?>">
                        <input id="Id" class="form-control" type="text" name="Id" value="<?php echo $CONT; ?>" required readonly>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="User">Usuario:</label>
                        <input id="User" class="form-control" type="text" name="User" required placeholder="Usuario">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Contrasenia">Contraseña: </label>
                        <input id="Contrasenia" class="form-control" type="password" name="Contrasenia" required placeholder="Contraseña">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Descripcion">Descripción del usuario: </label>
                        <input id="Descripcion" class="form-control" type="text" name="Descripcion" required placeholder="Descripcion del Usuario">
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
                              <option value="<?php echo $row['IdPersonal']; ?>"> 
                                <?php echo $row['Empleado']; ?></option>
                                    <?php } ?>
                            </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="FechaCreacion">Fecha de Creación: </label>
                        <input id="FechaCreacion" class="form-control" type="date" name="FechaCreacion" required>
                    </div>
                </div>
                
                <div class="col-md-12" style="text-align: center;">
                    <div class="form-group">
                         <button class="btn btn-success" type="submit" name="Mov" value="alta" id="Mov">Dar de Alta</button>
                         <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
            </form>
            </div>
        </div>
    </div>
</div>