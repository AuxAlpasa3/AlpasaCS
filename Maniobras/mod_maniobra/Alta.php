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
<div class="modal fade"  id="nuevoEmpleado" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Registrar Empleado</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="nuevo" id="nuevo" action="mod_personal/procesos.php" method="POST">
        <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Id">Id</label>
                        <input type="hidden" id="usuario" name="usuario" value="<?php echo $usuario; ?>">
                        
                        <input id="Id" class="form-control" type="text" name="Id" value="<?php echo $CONT; ?>" required readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Foto">Foto</label>
                        <input id="Foto" class="form-control" type="file" name="Foto" accept="image/*">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input id="nombre" class="form-control" type="text" name="nombre" required placeholder="Nombre del Empleado">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="apPaterno">Apellido Paterno</label>
                        <input id="apPaterno" class="form-control" type="text" name="apPaterno" required placeholder="Apellido Paterno">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="apMaterno">Apellido Materno</label>
                        <input id="apMaterno" class="form-control" type="text" name="apMaterno" required placeholder="Apellido Materno">
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
                                <?php while($row = $resultado->fetch_assoc()) { ?>
                              <option value="<?php echo $row['IdEmpresa']; ?>"> 
                                <?php echo $row['NomEmpresa']; ?></option>
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
                                <option value="<?php echo $row['IdUbicacion']; ?>">
                                  <?php echo $row['NomLargo']; ?></option>
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
                                <option value="<?php echo $row['IdDepartamento']; ?>">
                                  <?php echo $row['NomDepto']; ?></option>
                            <?php } ?>
                            </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="s_cargo">Cargo: </label>
                            <?php $query = "SELECT *FROM t_cargo;"; 
                            $resultado=$mysqli->query($query); ?>
                            <select class="form-control" name="s_cargo" id="s_cargo"  required>
                                <option value="0">Seleccionar Cargo</option>
                                    <?php while($row = $resultado->fetch_assoc()) { ?>
                                <option value="<?php echo $row['IdCargo']; ?>">
                                  <?php echo $row['NomCargo']; ?></option>
                            <?php } ?>
                            </select>
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