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
        
    $user = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
    $IdUbicacion = (!empty($_POST['IdUbi']))   ?  $_POST['IdUbi']: NULL;
    $NombreC = (!empty($_POST['NombreC']))   ?  $_POST['NombreC']: NULL;
    $NombreL = (!empty($_POST['NombreL']))   ?  $_POST['NombreL']: NULL;
    $Ciudad = (!empty($_POST['Ciudad']))   ?  $_POST['Ciudad']: NULL;
    $Estado = (!empty($_POST['Estado']))   ?  $_POST['Estado']: NULL;
    $Pais = (!empty($_POST['Pais']))   ?  $_POST['Pais']: NULL;
                       
      $sql2 = "Update t_ubicacion set NomCorto='$NombreC', NomLargo='$NombreL', Ciudad='$Ciudad', Estado='$Estado',Pais='$Pais' where IdUbicacion=$IdUbicacion;";

      $data = "'Update t_ubicacion set NomCorto=$NombreC , NomLargo=$NombreL,Ciudad=$Ciudad,Estado=$Estado,Pais=$Pais where IdUbicacion=$IdUbicacion'";

        $validar=mysqli_query($mysqli,$sql2);
      if($validar)
      {   
        mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,usuario) VALUES ('t_ubicacion','ModificarAlmacen','$fecha',$data,'$user')");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha Modificado Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Almacenes.php';
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
                window.location = '../Almacenes.php';
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

    $usuario = (!empty($_POST['usuario']))   ?  $_POST['usuario']: NULL;
    $IdUbicacion = (!empty($_POST['Id']))   ?  $_POST['Id']: NULL;
    $NombreC = (!empty($_POST['NombreC']))   ?  $_POST['NombreC']: NULL;
    $NombreL = (!empty($_POST['NombreL']))   ?  $_POST['NombreL']: NULL;
    $Ciudad = (!empty($_POST['Ciudad']))   ?  $_POST['Ciudad']: NULL;
    $Estado = (!empty($_POST['Estado']))   ?  $_POST['Estado']: NULL;
    $Pais = (!empty($_POST['Pais']))   ?  $_POST['Pais']: NULL;
  
                       
      $sql3 = "INSERT INTO t_ubicacion(IdUbicacion, NomCorto, NomLargo, Ciudad, Estado, Pais) VALUES ($IdUbicacion,'$NombreC','$NombreL','$Ciudad','$Estado','$Pais');";

        $data = "INSERT INTO t_ubicacion(IdUbicacion, NomCorto, NomLargo, Ciudad, Estado, Pais) VALUES ($IdUbicacion,$NombreC,$NombreL,$Ciudad,$Estado,$Pais);";

        $validar=mysqli_query($mysqli,$sql3);

      if($validar)
      {   
          mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_ubicacion','AltaAlmacen','$fecha','$data','$usuario')") or die ("Error consulta");
    
        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Almacenes.php';
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
                window.location = '../Almacenes.php';
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
      
    $UbicacionID=$_POST['id'];
    $usuario=$_POST['usuario'];

      $sql4 = "DELETE FROM t_ubicacion WHERE IdUbicacion=$UbicacionID;";

        $data = "'DELETE FROM t_ubicacion WHERE IdUbicacion=$UbicacionID'";

       
      $validar=mysqli_query($mysqli,$sql4);

      if($validar)
      {  
           mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_ubicacion','EliminarAlmacen','$fecha',$data,'$usuario')") or die ("Error consulta");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Almacenes.php';
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
                window.location = '../Almacenes.php';
            });
               });
            </script>";
      }
  }

?>