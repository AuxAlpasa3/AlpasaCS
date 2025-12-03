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
    $IdEmpresa = (!empty($_POST['EmpresaID']))   ?  $_POST['EmpresaID']: NULL;
    $Nombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
      
                       
      $sql2 = "Update t_Empresa set NomEmpresa='$Nombre' where IdEmpresa=$IdEmpresa";


        $data = "'Update t_Empresa set NomEmpresa=$Nombre where IdEmpresa=$IdEmpresa'";

        $validar=mysqli_query($mysqli,$sql2);
      if($validar)
      {   
    
        mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,usuario) VALUES ('t_Empresa','ModificarEmpresa','$fecha',$data,'$user')");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha Modificado Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Empresas.php';
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
                window.location = '../Empresas.php';
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
    $IdEmpresa = (!empty($_POST['Id']))   ?  $_POST['Id']: NULL;
    $Nombre = (!empty($_POST['Nombre']))   ?  $_POST['Nombre']: NULL;
                       
      $sql3 = "INSERT INTO t_empresa(IdEmpresa,NomEmpresa)  VALUES ($IdEmpresa,'$Nombre');";

        $data = "INSERT INTO t_empresa(IdEmpresa,NomEmpresa)  VALUES ($IdEmpresa,$Nombre);";

        $validar=mysqli_query($mysqli,$sql3);

      if($validar)
      {   
          mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_empresa','AltaEmpresa','$fecha','$data','$usuario')") or die ("Error consulta");
    
        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Empresas.php';
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
                window.location = '../Empresas.php';
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
      
    $EmpresaID=$_POST['id'];
    $usuario=$_POST['usuario'];

      $sql4 = "DELETE FROM t_empresa WHERE IdEmpresa=$EmpresaID;";

        $data = "'DELETE FROM t_empresa WHERE IdEmpresa=$EmpresaID'";

       
      $validar=mysqli_query($mysqli,$sql4);

      if($validar)
      {  
           mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_empresa','EliminarEmpresa','$fecha',$data,'$usuario')") or die ("Error consulta");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Empresas.php';
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
                window.location = '../Empresas.php';
            });
               });
            </script>";
      }
  }

?>