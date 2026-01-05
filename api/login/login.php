<?php
Include '../db/conexion.php';
session_start();

  $usuario = $_POST['usuario'];
  $password = $_POST['password'];
  $VERSION= getenv('VERSION');

    $sentencia = $Conexion->prepare("SELECT trim(contrasenia) as pass,Usuario,IdUsuario,rol FROM t_usuario where Usuario= ? and Status=1");
    $sentencia->execute([$usuario]);
    $client = $sentencia->fetch();

      if (password_verify($_POST['password'], $client['pass']))
      {
                    $_SESSION['current_user'.$VERSION] =  $usuario;
                    $_SESSION['rol_current_users'.$VERSION]  = $client['rol'];
                    $_SESSION['idusuario'.$VERSION] = $client['IdUsuario'];
                    $_SESSION['loggedin'.$VERSION] = true;
                    $_SESSION['login_time'.$VERSION] = time(); 

                  header('location: ../../Menu/Index.php');
      }else {
        echo "
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script language='JavaScript'>
                document.addEventListener('DOMContentLoaded',function(){
                  Swal.fire({
                        icon: 'error',
                        title: 'Usuario o Contraseña Incorrectos',
                        showConfirmButton: false
                        }).then(function() {
                      window.location = '../../Index.php';
                  });
                     });
                  </script>";
       }
     
     
 ?>
  