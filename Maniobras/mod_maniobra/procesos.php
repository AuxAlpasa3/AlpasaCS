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
    $server='https://10.123.6.15:8080/regentsalper/imagenes/empleados/';
    $user = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
    $IdPersonal = (!empty($_POST['PersonalID']))   ?  $_POST['PersonalID']: NULL;
    $Nombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
    $ApPaterno = (!empty($_POST['apPaterno']))   ?  $_POST['apPaterno']: NULL;
    $ApMaterno = (!empty($_POST['apMaterno']))   ?  $_POST['apMaterno']: NULL;

    $Status = (!empty($_POST['s_estatus']))   ?  $_POST['s_estatus']: NULL;
    $Empresa = (!empty($_POST['s_Empresa']))   ?  $_POST['s_Empresa']: NULL;
    $Cargo = (!empty($_POST['s_cargo']))   ?  $_POST['s_cargo']: NULL;
    $Departamento = (!empty($_POST['s_depto']))   ?  $_POST['s_depto']: NULL;
    $IdUbicacion = (!empty($_POST['s_Ubicacion']))   ?  $_POST['s_Ubicacion']: NULL;
    $Rutafoto = (!empty($_POST['Foto']))   ?  $_POST['Foto']: NULL;   
     $Rutafoto2 = (!empty($_POST['Foto2']))   ?  $_POST['Foto2']: NULL;   
    $bytesArchivo=" ";

    if((!empty($Rutafoto2)))
      {
        $pathFoto = $IdPersonal."_".$ApPaterno."_".$Nombre.".jpg";
        $bytesArchivo= $server.$pathFoto;  
        file_put_contents($bytesArchivo, base64_decode($Rutafoto));
      }
      else
      {
        $bytesArchivo= $Rutafoto;
      }
      
                       
      $sql2 = "Update t_personal set Nombre='$Nombre', ApPaterno='$ApPaterno', ApMaterno='$ApMaterno', Cargo='$Cargo', Departamento='$Departamento',Empresa='$Empresa',Status=$Status,IdUbicacion='$IdUbicacion',Rutafoto ='$bytesArchivo' where IdPersonal=$IdPersonal";


        $data = "'Update t_personal set Nombre=$Nombre, ApPaterno=$ApPaterno, ApMaterno=$ApMaterno, Cargo=$Cargo, Departamento=$Departamento,Empresa=$Empresa,Status=$Status,IdUbicacion=$IdUbicacion,Rutafoto =$bytesArchivo where IdPersonal=$IdPersonal'";

        $validar=mysqli_query($mysqli,$sql2);
      if($validar)
      {   
    
        mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,usuario) VALUES ('t_personal','ModificarPersonal','$fecha',$data,'$user')");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha Modificado Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../personal.php';
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
                window.location = '../personal.php';
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

    $server='http://10.123.6.15:8080/regentsalper/imagenes/empleados/';
    $usuario = (!empty($_POST['usuario']))   ?  $_POST['usuario']: NULL;
    $IdPersonal = (!empty($_POST['Id']))   ?  $_POST['Id']: NULL;
    $Nombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
    $ApPaterno = (!empty($_POST['apPaterno']))   ?  $_POST['apPaterno']: NULL;
    $ApMaterno = (!empty($_POST['apMaterno']))   ?  $_POST['apMaterno']: NULL;

    $Empresa = (!empty($_POST['s_Empresa']))   ?  $_POST['s_Empresa']: NULL;
    $Cargo = (!empty($_POST['s_cargo']))   ?  $_POST['s_cargo']: NULL;
    $Departamento = (!empty($_POST['s_depto']))   ?  $_POST['s_depto']: NULL;
    $IdUbicacion = (!empty($_POST['s_Ubicacion']))   ?  $_POST['s_Ubicacion']: NULL;
    $Rutafoto = (!empty($_POST['Foto']))   ?  $_POST['Foto']: NULL;   
    $bytesArchivo=" ";

    if((!empty($Rutafoto)))
      {
        $pathFoto = $IdPersonal."_".$ApPaterno."_".$Nombre.".jpg";
        $bytesArchivo= $server.$pathFoto;  
        file_put_contents($bytesArchivo, base64_decode($Rutafoto));
         
      }
      else
      {
        $pathFoto = "Default.jpg";
        $bytesArchivo= $server.$pathFoto;
      }
                       
      $sql3 = "Insert into t_personal (IdPersonal,Nombre, ApPaterno, ApMaterno, Cargo, Departamento, Empresa, Status, IdUbicacion, Rutafoto) VALUES ($IdPersonal,'$Nombre','$ApPaterno','$ApMaterno','$Cargo','$Departamento','$Empresa',1,'$IdUbicacion','$bytesArchivo');";

        $data = "Insert into t_personal (IdPersonal,Nombre, ApPaterno, ApMaterno, Cargo, Departamento, Empresa, Status, IdUbicacion, Rutafoto) VALUES ($IdPersonal,$Nombre,$ApPaterno,$ApMaterno,$Cargo,$Departamento,$Empresa,1,$IdUbicacion,$bytesArchivo);";

        $validar=mysqli_query($mysqli,$sql3);

      if($validar)
      {   
          mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_personal','AltaPersonal','$fecha','$data','$usuario')") or die ("Error consulta");
    
        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../personal.php';
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
                window.location = '../personal.php';
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
      
    $EmpleadoID=$_POST['id'];
    $usuario=$_POST['usuario'];

      $sql4 = "DELETE FROM t_personal WHERE IdPersonal=$EmpleadoID;";

        $data = "'DELETE FROM t_personal WHERE IdPersonal=$EmpleadoID'";

       
      $validar=mysqli_query($mysqli,$sql4);

      if($validar)
      {  
           mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_personal','EliminarPersonal','$fecha',$data,'$usuario')") or die ("Error consulta");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../personal.php';
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
                window.location = '../personal.php';
            });
               });
            </script>";
      }
  }

?>