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
    $IdDepartamento = (!empty($_POST['EmpresaID']))   ?  $_POST['EmpresaID']: NULL;
    $Nombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
      
                       
      $sql2 = "Update t_departamento set NomDepto='$Nombre' where IdDepartamento=$IdDepartamento";


        $data = "'Update t_departamento set NomDepto=$Nombre where IdDepartamento=$IdDepartamento'";

        $validar=mysqli_query($mysqli,$sql2);
      if($validar)
      {   
    
        mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,usuario) VALUES ('t_departamento','ModificarDepartamento','$fecha',$data,'$user')");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha Modificado Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Departamentos.php';
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
                window.location = '../Departamentos.php';
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
    $IdDepartamento = (!empty($_POST['Id']))   ?  $_POST['Id']: NULL;
    $Nombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
                       
      $sql3 = "INSERT INTO t_departamento(IdDepartamento,NomDepto)  VALUES ($IdDepartamento,'$Nombre');";

        $data = "INSERT INTO t_departamento(IdDepartamento,NomDepto)  VALUES ($IdDepartamento,$Nombre);";

        $validar=mysqli_query($mysqli,$sql3);

      if($validar)
      {   
          mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_departamento','AltaDepartamento','$fecha','$data','$usuario')") or die ("Error consulta");
    
        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Departamentos.php';
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
                window.location = '../Departamentos.php';
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
      
    $DeptoId=$_POST['id'];
    $usuario=$_POST['usuario'];

      $sql4 = "DELETE FROM t_departamento WHERE IdDepartamento=$DeptoId;";

        $data = "'DELETE FROM t_departamento WHERE IdDepartamento=$DeptoId'";

       
      $validar=mysqli_query($mysqli,$sql4);

      if($validar)
      {  
           mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_departamento','EliminaDepartamento','$fecha',$data,'$usuario')") or die ("Error consulta");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../Departamentos.php';
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
                window.location = '../Departamentos.php';
            });
               });
            </script>";
      }
  }

?>