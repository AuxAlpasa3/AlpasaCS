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

?>
<div class="modal fade" id="modificarEmpresa_<?php echo $ID; ?>_<?php echo $usuario; ?>" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #808080;">
                <h5 class="modal-title text-white" id="title">Modificar Empleado</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
              <div class="modal-body">
                      <form name="editar" id="editar" action="mod_Empresa/procesos.php" method="POST">
                        <div class="row">
                          <div class="col-md-6">
                              <div class="form-group" >
                                   <input id="EmpresaID" value="<?php echo $ID ?>" name="EmpresaID" hidden="true">
                                   <input id="user" value="<?php echo $usuario ?>" name="user" hidden="true">
                              </div>
                          </div>
                        </div>
                        <div class="row">
                              <div class="col-md-12">
                                <div class="form-group">
                                      <label for="nombre">Nombre</label>
                                      <input id="nombre" class="form-control" type="text" name="nombre" required placeholder="Nombre de la Empresa" value="<?php echo  $Empresa;?>">
                                </div>
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

