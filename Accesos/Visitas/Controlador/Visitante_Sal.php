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
      $Visitante = $row['Visitante'];
    $Empresa = $row['Empresa'];
    $Vis_Motivo = $row['Vis_Motivo'];
    $Visita = $row['Visita'];
    $Veh_Modelo = $row['Veh_Modelo'];
    $Veh_Color = $row['Veh_Color'];
    $Veh_Placas = $row['Veh_Placas'];
    $Ubicacion = $row['NomCorto'];
    $DispN = $row['DispSal'];
    $Fecha = $row['FechaSalida'];
    $TiempoMarcaje = $row['TiempoSal'];
    $Foto0 = $row['Foto0Sal'];
    $Foto1 = $row['Foto1Sal'];
    $Foto2 = $row['Foto2Sal'];
    $Foto3 = $row['Foto3Sal'];
    $Foto4 = $row['Foto4Sal'];
    $Observaciones = $row['ObsSal'];
    $Usuario = $row['UsuarioSal'];

?>
<div class="modal fade"  id="MovSalida<?php echo $IdMov;?>" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00; ">
                <h5 class="modal-title text-white" id="title" style="text-align: center;">Movimiento de Salida</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-auto" style="text-align: center;">
                    <h5 style="color: darkorange;">Nombre del Visitante: </h5>
                        <h5 style="align-self: center;"><?php echo $Visitante;?></h5>
                    <h5 style="color: darkorange;">Empresa: </h5>
                        <h5 style="align-self: center;"><?php echo $Empresa;?></h5>
                    <h5 style="color: darkorange;">Motivo de Visita: </h5>
                        <h5 style="align-self: center;"><?php echo $Vis_Motivo;?></h5>
                    <h5 style="color: darkorange;">Descripción del Vehiculo: </h5>
                        <h5 style="align-self: center;"><?php echo $Veh_Modelo." ".$Veh_Color." ".$Veh_Placas;?></h5>

                    <h5 style="color: darkorange;">Ubicación: </h5>
                        <h5 style="align-self: center;"><?php echo $Ubicacion;?></h5>
                    <h5 style="color: darkorange;">Dispositivo Utilizado: </h5>
                        <h5 style="align-self: center;"><?php echo $DispN;?></h5>
                    <h5 style="color: darkorange;">Fecha de Entrada: </h5>
                        <h5 style="align-self: center;"><?php echo $Fecha;?></h5>
                </div>
                <div class="col"style="text-align: center;">
                    <h5 style="color: darkorange;">Imagenes</h5>
                    <div class="row" style="text-align: center;">
                       <p style="align-self: center;"><?php echo '<img src="'.$Foto0.'" width="200" id="foto0">';?></p>
                       <p style="align-self: center;"><?php echo '<img src="'.$Foto1.'" width="200" id="foto1">';?></p>
                       <p style="align-self: center;"><?php echo '<img src="'.$Foto2.'" width="200" id="foto2">';?></p>
                       <p style="align-self: center;"><?php echo '<img src="'.$Foto3.'" width="200" id="foto3">';?></p>
                       <p style="align-self: center;"><?php echo '<img src="'.$Foto4.'" width="200" id="foto4">';?></p>
                    </div>
                </div>
                <div class="col-auto" style="text-align: center;">
                    <h5 style="color: darkorange;">Observaciones:  </h5>
                        <h5 style="align-self: center;"><?php echo $Observaciones; ?></h5>
                   <h5 style="color: darkorange;">Usuario que dio Salida: </h5>
                        <h5 style="align-self: center;"><?php echo $Usuario; ?></h5>
                    </div>
                </div>
            <div class="col-md-12" style="text-align: center;">
                <div class="form-group">
                    <button class="btn btn-success" id="cancelar" type="button" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</div>
