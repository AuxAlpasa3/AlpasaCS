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
    $IdCargo = (!empty($_POST['CargoID']))   ?  $_POST['CargoID']: NULL;
    $Nombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
      
                       
      $sql2 = "Update t_cargo set NomCargo='$Nombre' where IdCargo=$IdCargo";


        $data = "'Update t_cargo set NomCargo=$Nombre where IdCargo=$IdCargo'";

        $validar=mysqli_query($mysqli,$sql2);
      if($validar)
      {   
    
        mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,usuario) VALUES ('t_cargo','ModificarCargo','$fecha',$data,'$user')");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha Modificado Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Cargo.php';
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
                window.location = '../Cargo.php';
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
    $IdCargo = (!empty($_POST['Id']))   ?  $_POST['Id']: NULL;
    $Nombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
                       
      $sql3 = "INSERT INTO t_cargo(IdCargo,NomCargo)  VALUES ($IdCargo,'$Nombre');";

        $data = "INSERT INTO t_cargo(IdCargo,NomCargo)  VALUES ($IdCargo,$Nombre);";

        $validar=mysqli_query($mysqli,$sql3);

      if($validar)
      {   
          mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_cargo','AltaCargo','$fecha','$data','$usuario')") or die ("Error consulta");
    
        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Cargo.php';
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
                window.location = '../Cargo.php';
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
      
    $CargoId=$_POST['id'];
    $usuario=$_POST['usuario'];

      $sql4 = "DELETE FROM t_cargo WHERE IdCargo=$CargoId;";

        $data = "'DELETE FROM t_cargo WHERE IdCargo=$CargoId'";

       
      $validar=mysqli_query($mysqli,$sql4);

      if($validar)
      {  
           mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_cargo','EliminaCargo','$fecha',$data,'$usuario')") or die ("Error consulta");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Cargo.php';
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
                window.location = '../Cargo.php';
            });
               });
            </script>";
      }
  }

?>