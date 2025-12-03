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
<div class="modal fade"  id="nuevoAlmacen" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #808080;">
                <h5 class="modal-title text-white" id="title">Registrar Empleado</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="nuevo" id="nuevo" action="mod_Almacen/procesos.php" method="POST">
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
                        <label for="NombreC">Abreviatura del Almacen</label>
                        <input id="NombreC" class="form-control" type="text" name="NombreC" required placeholder="Abreviatura del Almacen">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="NombreL">Nombre del Almacen:</label>
                        <input id="NombreL" class="form-control" type="text" name="NombreL" required placeholder="Nombre del Almacen">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Ciudad">Ciudad:</label>
                        <input id="Ciudad" class="form-control" type="text" name="Ciudad" required placeholder="Ciudad">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Estado">Estado:</label>
                        <input id="Estado" class="form-control" type="text" name="Estado" required placeholder="Estado">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="Pais">Pais:</label>
                        <input id="Pais" class="form-control" type="text" name="Pais" required placeholder="Pais">
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