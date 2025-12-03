<?php

	if(isset($_POST['Mov']))
	{
		switch($_POST['Mov']){
      case 'alta':
        alta();
      break;
      case 'editar':
        editar();
      break;
      case 'borrar':
        borrar();
      break;
		}
	}

	function editar()
  {

    include ('../../Config/conexion.php');
    date_default_timezone_set('America/Guatemala');
    $fecha = date('Y-m-d H:i:s');
    extract($_POST);
        
   //C:\xampp\htdocs\regentsalper\imagenes\empleados
    $server='https://10.123.6.15:8080/regentsalper/imagenes/vehiculos/';
    $user = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
    $VehiculoID = (!empty($_POST['VehiculoID']))   ?  $_POST['VehiculoID']: NULL;
    $Empleado = (!empty($_POST['s_Empleado']))   ?  $_POST['s_Empleado']: NULL;
    $Marca = (!empty($_POST['Marca']))   ?  $_POST['Marca']: NULL;
    $Modelo = (!empty($_POST['Modelo']))   ?  $_POST['Modelo']: NULL;

    $Num_Serie = (!empty($_POST['Num_Serie']))   ?  $_POST['Num_Serie']: NULL;
    $Placas = (!empty($_POST['Placas']))   ?  $_POST['Placas']: NULL;
    $Anio = (!empty($_POST['Anio']))   ?  $_POST['Anio']: NULL;
    $Color = (!empty($_POST['Color']))   ?  $_POST['Color']: NULL;
    $Activo = (!empty($_POST['s_estatus']))   ?  $_POST['s_estatus']: NULL;
    $Rutafoto = (!empty($_POST['Foto']))   ?  $_POST['Foto']: NULL;   
     $Rutafoto2 = (!empty($_POST['Foto2']))   ?  $_POST['Foto2']: NULL;   
    $bytesArchivo=" ";

    if((!empty($Rutafoto2)))
      {
        $pathFoto = $VehiculoID."_".$Empleado."_".$Modelo."_".$Placas.".jpg";
        $bytesArchivo= $server.$pathFoto;  
        file_put_contents($bytesArchivo, base64_decode($Rutafoto));
      }
      else
      {
        $bytesArchivo= $Rutafoto;
      }
      
                       
      $sql2 = "UPDATE t_vehiculos SET IdVehiculo='$VehiculoID',IdPersonal='$Empleado',Marca='$Marca',Modelo='$Modelo',Num_Serie='$Num_Serie',Placas='$Placas',Anio='$Anio',Color='$Color',Activo='$Activo',
Rutafoto ='$bytesArchivo' where IdVehiculo=$VehiculoID;";


        $data = "'UPDATE t_vehiculos SET IdVehiculo=$VehiculoID,IdPersonal=$Empleado,Marca=$Marca,Modelo=$Modelo,Num_Serie=$Num_Serie,Placas=$Placas,Anio=$Anio,Color=$Color,Activo=$Activo,Rutafoto =$bytesArchivo where IdVehiculo=$VehiculoID;'";

        $validar=mysqli_query($mysqli,$sql2);
      if($validar)
      {   
    
        mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,usuario) VALUES ('t_vehiculos','ModificarVehiculo','$fecha',$data,'$user')");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha Modificado Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Vehiculos.php';
            });
               });
            </script>";

      }
      else
      {
         echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'error',
                  title: 'Algo ha salido mal, intenta de nuevo',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Vehiculos.php';
            });
               });
            </script>";
      }
  }

  function alta()
  {

    include ('../../Config/conexion.php');
    date_default_timezone_set('America/Guatemala');
      $fecha = date('Y-m-d H:i:s');

     $server='https://10.123.6.15:8080/regentsalper/imagenes/vehiculos/';
    $user = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
    $VehiculoID = (!empty($_POST['Id']))   ?  $_POST['Id']: NULL;
    $Empleado = (!empty($_POST['s_Empleado']))   ?  $_POST['s_Empleado']: NULL;
    $Marca = (!empty($_POST['Marca']))   ?  $_POST['Marca']: NULL;
    $Modelo = (!empty($_POST['Modelo']))   ?  $_POST['Modelo']: NULL;

    $Num_Serie = (!empty($_POST['Num_Serie']))   ?  $_POST['Num_Serie']: NULL;
    $Placas = (!empty($_POST['Placas']))   ?  $_POST['Placas']: NULL;
    $Anio = (!empty($_POST['Anio']))   ?  $_POST['Anio']: NULL;
    $Color = (!empty($_POST['Color']))   ?  $_POST['Color']: NULL;
    $Activo = (!empty($_POST['s_estatus']))   ?  $_POST['s_estatus']: NULL;
     $Rutafoto = (!empty($_POST['Foto']))   ?  $_POST['Foto']: NULL;   
    $bytesArchivo=" ";

    if((!empty($Rutafoto)))
      {
        $pathFoto = $VehiculoID."_".$Empleado."_".$Modelo."_".$Placas.".jpg";
        $bytesArchivo= $server.$pathFoto;  
        file_put_contents($bytesArchivo, base64_decode($Rutafoto));
         
      }
      else
      {
        $pathFoto = "Default.jpg";
        $bytesArchivo= $server.$pathFoto;
      }
                       
      $sql3 = "INSERT INTO t_vehiculos(IdVehiculo, IdPersonal, Marca, Modelo, Num_Serie, Placas, Anio, Color, Activo, RutaFoto) VALUES ('$VehiculoID','$Empleado','$Marca','$Modelo','$Num_Serie','$Placas','$Anio','$Color','$Activo','$bytesArchivo');";

        $data = "INSERT INTO t_vehiculos(IdVehiculo, IdPersonal, Marca, Modelo, Num_Serie, Placas, Anio, Color, Activo, RutaFoto) VALUES ($VehiculoID,$Empleado,$Marca,$Modelo,$Num_Serie,$Placas,$Anio,$Color,$Activo,$bytesArchivo)";

        $validar=mysqli_query($mysqli,$sql3);

      if($validar)
      {   
          mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_vehiculos','AltaVehiculo','$fecha','$data','$user')") or die ("Error consulta");
    
        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Vehiculos.php';
            });
               });
            </script>";

      }
      else
      {
         echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'error',
                  title: 'Algo ha salido mal, intenta de nuevo',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Vehiculos.php';
            });
               });
            </script>";
      }
  }

  function borrar()
  {  
   include ('../../Config/conexion.php');
    date_default_timezone_set('America/Guatemala');
      $fecha = date('Y-m-d H:i:s');
      
    $VehiculoID=$_POST['id'];
    $usuario=$_POST['usuario'];

      $sql4 = "DELETE FROM t_vehiculos WHERE IdVehiculo=$VehiculoID;";

        $data = "'DELETE FROM t_vehiculos WHERE IdVehiculo=$VehiculoID'";

       
      $validar=mysqli_query($mysqli,$sql4);

      if($validar)
      {  
           mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_vehiculos','EliminarVehiculo','$fecha',$data,'$usuario')") or die ("Error consulta");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Vehiculos.php';
            });
               });
            </script>";

      }
      else
      {
         echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'error',
                  title: 'Algo ha salido mal, intenta de nuevo',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Vehiculos.php';
            });
               });
            </script>";
      }
  }

?>