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
<div class="modal fade" id="PasswordUsuario_<?php echo $ID;?>_<?php echo $usuario; ?>" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #808080;">
                <h5 class="modal-title text-white" id="title">Modificar Contraseña del Usuario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <div class="modal-body">
              <form name="cambiar" id="cambiar" action="mod_Usuario_seg/procesos.php" method="POST">
                  <div class="form-group" >
                    <input id="IdUsuario" value="<?php echo $ID?>" name="IdUsuario" hidden="true">
                    <input id="user" value="<?php echo $acceso?>" name="user" hidden="true">
                    <input id="user" value="<?php echo $acceso?>" name="user" hidden="true">
                    <input id="Usuario" value="<?php echo $usuario?>" name="Usuario" hidden="true">
                  </div>
                  <div class="row">
                    <div class="col-md-12" style="text-align: center;">
                      <div class="form-group">
                        <label for="pass1">Nueva Contraseña:</label>
                        <input id="pass1" class="form-control" type="Password" name="pass1" required placeholder="Nueva Contraseña">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12" style="text-align: center;">
                      <div class="form-group">
                        <label for="pass2">Vuelve a Escribir Nueva Contraseña:</label>
                        <input id="pass2" class="form-control" type="Password" name="pass2" required placeholder="Vuelve a escribir Nueva Contraseña">
                      </div>
                    </div>
                  </div>
                <div class="row">
                  <div class="col-md-12" style="text-align: center;">
                    <div class="form-group">
                      <button class="btn btn-success" type="submit" name="Mov" value="cambiar" id="Mov">Cambiar Contraseña</button>
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
<script>
$('#pass1').on("mousedown",function(event) {
  $(this).attr("type","text");
});

$('#pass1').on("mouseup",function(event) {
  $('#pass1').attr("type","password");
});

$('#pass2').on("mousedown",function(event) {
  $(this).attr("type","text");
});

$('#pass2').on("mouseup",function(event) {
  $('#pass2').attr("type","password");
});

</script>