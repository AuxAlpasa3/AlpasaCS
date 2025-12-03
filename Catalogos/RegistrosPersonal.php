<?php 
  include ('../Config/conexion.php');
date_default_timezone_set('America/Guatemala');
  $fecha = date('Y-m-d H:i:s');

$mov=$_POST['mov'];

if($mov=='Alta')
{

    $server='http://10.123.6.15:8080/regentsalper/imagenes/';
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
        $pathFoto = "empleados/$IdPersonal_$ApPaterno_$Nombre.jpg";
        //file_put_contents($pathFoto, base64_decode($Rutafoto));
        $bytesArchivo= $server.$pathFoto;   
      }
      else
      {
        $pathFoto = "empleados/Default.jpg";
        $bytesArchivo= $server.$pathFoto;
      }
      
                       
      $sql2 = "Insert into t_personal (IdPersonal,Nombre, ApPaterno, ApMaterno, Cargo, Departamento, Empresa, Status, IdUbicacion, Rutafoto) VALUES ($IdPersonal,'$Nombre','$ApPaterno','$ApMaterno','$Cargo','$Departamento','$Empresa',1,'$IdUbicacion','$bytesArchivo');";

        $data = "Insert into t_personal (IdPersonal,Nombre, ApPaterno, ApMaterno, Cargo, Departamento, Empresa, Status, IdUbicacion, Rutafoto) VALUES ($IdPersonal,$Nombre,$ApPaterno,$ApMaterno,$Cargo,$Departamento,$Empresa,1,$IdUbicacion,$bytesArchivo);";

	  if(mysqli_query($mysqli,$sql2))
	  {   
		
		$Mensaje='Ha sido dado de Alta correctamente';	
	    mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_personal','AltaPersonal','$fecha','$data','$usuario')") or die ("Error consulta");
	    
       		header("Location:" .base_url.'Catalogos/Personal');
	  }
}

if($mov=='Eliminar')
{
    
}

if($mov=='Modificar')

{

    $server='http://192.168.10.243:8080/regentsalper/imagenes/';
    $usuario = (!empty($_POST['usuario']))   ?  $_POST['usuario']: NULL;
    $IdPersonal = (!empty($_POST['Id']))   ?  $_POST['Id']: NULL;
    $Nombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
    $ApPaterno = (!empty($_POST['apPaterno']))   ?  $_POST['apPaterno']: NULL;
    $ApMaterno = (!empty($_POST['apMaterno']))   ?  $_POST['apMaterno']: NULL;

    $Status = (!empty($_POST['s_estatus']))   ?  $_POST['s_estatus']: NULL;
    $Empresa = (!empty($_POST['s_Empresa']))   ?  $_POST['s_Empresa']: NULL;
    $Cargo = (!empty($_POST['s_cargo']))   ?  $_POST['s_cargo']: NULL;
    $Departamento = (!empty($_POST['s_depto']))   ?  $_POST['s_depto']: NULL;
    $IdUbicacion = (!empty($_POST['s_Ubicacion']))   ?  $_POST['s_Ubicacion']: NULL;
    $Rutafoto = (!empty($_POST['Foto']))   ?  $_POST['Foto']: NULL;   
    $bytesArchivo=" ";
    
    if((!empty($Rutafoto)))
      {
        $pathFoto = "empleados/$IdPersonal_$ApPaterno_$Nombre.jpg";
        //file_put_contents($pathFoto, base64_decode($Rutafoto));
        $bytesArchivo= $server.$pathFoto;   
      }
      else
      {
        $pathFoto = "empleados/Default.jpg";
        $bytesArchivo= $server.$pathFoto;
      }
      
                       
      $sql2 = "Update t_personal set Nombre='$Nombre', ApPaterno='$ApPaterno', ApMaterno='$ApMaterno', Cargo='$Cargo', Departamento='$Departamento',Empresa='$Empresa',Status=$Status,IdUbicacion='$IdUbicacion',Rutafoto ='$bytesArchivo' where IdPersonal=$IdPersonal";


        $data = "'Update t_personal set Nombre=$Nombre, ApPaterno=$ApPaterno, ApMaterno=$ApMaterno, Cargo=$Cargo, Departamento=$Departamento,Empresa=$Empresa,Status=$Status,IdUbicacion=$IdUbicacion,Rutafoto =$bytesArchivo where IdPersonal=$IdPersonal'";

      if(mysqli_query($mysqli,$sql2))
      {   
	
        mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_personal','ModificarPersonal','$fecha',$data,'$usuario')") or die ("Error consulta");

      }
}

?>