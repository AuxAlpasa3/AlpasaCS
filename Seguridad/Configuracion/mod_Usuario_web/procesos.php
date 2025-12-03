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
      case 'cambiar':
        cambiar();
      break;
    }
  }

  function alta()
  {

    include ('../../Config/conexion.php');
    date_default_timezone_set('America/Guatemala');
      $fecha = date('Y-m-d H:i:s');

    $usuario = (!empty($_POST['usuario']))   ?  $_POST['usuario']: NULL;
    $IdUsuario = (!empty($_POST['Id']))   ?  $_POST['Id']: NULL;
    $User = (!empty($_POST['User']))   ?  $_POST['User']: NULL;
    $Contrasenia = (!empty($_POST['Contrasenia']))   ?  $_POST['Contrasenia']: NULL;
    $Descripcion = (!empty($_POST['Descripcion']))   ?  $_POST['Descripcion']: NULL;

    $Empleado = (!empty($_POST['s_Empleado']))   ?  $_POST['s_Empleado']: NULL;
    $FechaCreacion = (!empty($_POST['FechaCreacion']))   ?  $_POST['FechaCreacion']: NULL;

       $passCifrada = password_hash($Contrasenia,PASSWORD_DEFAULT);
    $passCifrada = $passCifrada;
        
      $sql3 = "INSERT INTO t_usuarios_web(IdUsuario, Usuario, Contrasenia, Descripcion, EmpleadoId, CreateDate,status) VALUES ($IdUsuario,'$User','$passCifrada','$Descripcion','$Empleado','$FechaCreacion',1);";
 
        $data = "INSERT INTO t_usuarios_web(IdUsuario, Usuario, Contrasenia, Descripcion, EmpleadoId, CreateDate,status) VALUES ($IdUsuario,$User,$Contrasenia,$Descripcion,$Empleado,$FechaCreacion,1);";

        $validar=mysqli_query($mysqli,$sql3);

      if($validar)
      {   
            mysqli_query($mysqli,"INSERT INTO accesosbackup(Usuario, Contrasenia, Fecha, user) VALUES ('$User','$Contrasenia','$fecha','$usuario')")or die ("Error consulta");

              mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_usuarios_web','AltaUsuarios','$fecha','$data','$usuario')") or die ("Error consulta");
    
        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../UsuariosWeb.php';
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
                window.location = '../UsuariosWeb.php';
            });
               });
            </script>";
      }
  }

  function editar()
  {
    include ('../../Config/conexion.php');
    date_default_timezone_set('America/Guatemala');
    $fecha = date('Y-m-d H:i:s');
    extract($_POST);

    $Usuario = (!empty($_POST['Usuario']))   ?  $_POST['Usuario']: NULL;
    $IdUsuario = (!empty($_POST['IdUsuario']))   ?  $_POST['IdUsuario']: NULL;
    $User = (!empty($_POST['User']))   ?  $_POST['User']: NULL;
    $Descripcion = (!empty($_POST['Descripcion']))   ?  $_POST['Descripcion']: NULL;

    $Empleado = (!empty($_POST['s_Empleado']))   ?  $_POST['s_Empleado']: NULL;
    $Estatus = (!empty($_POST['s_estatus']))   ?  $_POST['s_estatus']: NULL;
                       
      $sql2 = "Update t_usuarios_web set Usuario='$User', Descripcion='$Descripcion',EmpleadoId='$Empleado',Status='$Estatus' where IdUsuario=$IdUsuario;";

        $data = "'Update t_usuarios_web set Usuario=$User, Descripcion=$Descripcion,EmpleadoId=$Empleado,Status=$Estatus where IdUsuario=$IdUsuario;'";

        $validar=mysqli_query($mysqli,$sql2);
      if($validar)
      {   
        mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,usuario) VALUES ('t_usuarios_web','Modificar Usuario','$fecha',$data,'$Usuario')");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha Modificado Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../UsuariosWeb.php';
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
                window.location = '../UsuariosWeb.php';
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
      
      $IdUsuario=$_POST['id'];
    $usuario=$_POST['usuario'];

      $sql4 = "DELETE FROM t_usuarios_web WHERE IdUsuario=$IdUsuario;";

        $data = "'DELETE FROM t_usuarios_web WHERE IdUsuario=$IdUsuario'";

       
      $validar=mysqli_query($mysqli,$sql4);

      if($validar)
      {  
           mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,Usuario) VALUES ('t_usuarios_web','EliminarUsuario','$fecha',$data,'$usuario')") or die ("Error consulta");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha dado de Alta Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../UsuariosWeb.php';
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
                window.location = '../UsuariosWeb.php';
            });
               });
            </script>";
      }
  }

  function cambiar()
  {
    include ('../../Config/conexion.php');
    date_default_timezone_set('America/Guatemala');
    $fecha = date('Y-m-d H:i:s');
    extract($_POST);

    $Usuario = (!empty($_POST['Usuario']))   ?  $_POST['Usuario']: NULL;
    $IdUsuario = (!empty($_POST['IdUsuario']))   ?  $_POST['IdUsuario']: NULL;
    $user = (!empty($_POST['user']))   ?  $_POST['user']: NULL;
    $contrasenia = (!empty($_POST['pass1']))   ?  $_POST['pass1']: NULL;
     $contrasenia2 = (!empty($_POST['pass2']))   ?  $_POST['pass2']: NULL;

    if($contrasenia==$contrasenia2)
    {
       $passCifrada = password_hash($contrasenia,PASSWORD_DEFAULT);
                       
      $sql2 = "Update t_usuarios_web set Contrasenia='$passCifrada' where IdUsuario=$IdUsuario;";

        $data = "'Update t_usuarios_web set Contrasenia=$passCifrada where IdUsuario=$IdUsuario;'";

        $validar=mysqli_query($mysqli,$sql2);
      if($validar)
      {   

         mysqli_query($mysqli,"INSERT INTO accesosbackup(Usuario, Contrasenia, Fecha, user) VALUES ('$user','$contrasenia','$fecha','$Usuario')")or die ("Error consulta");

         mysqli_query($mysqli,"INSERT INTO bitacora(Tabla,FolMovimiento,Fecha,Consulta,usuario) VALUES ('t_usuarios_web',Cambio Contraseña','$fecha',$data,'$Usuario')");

        echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'success',
                  title: 'Se ha Modificado Correctamente',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../UsuariosWeb.php';
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
                window.location = '../UsuariosWeb.php';
            });
               });
            </script>";
      }
  }
  else
  {
     echo "
          <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script language='JavaScript'>
          document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                  icon: 'error',
                  title: 'Las Contraseñas no son iguales',
                  showConfirmButton: false
                  }).then(function() {
                window.location = '../UsuarioSeg.php';
            });
               });
            </script>";
  }
}

?>