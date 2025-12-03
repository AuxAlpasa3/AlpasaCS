<?php
Include '../db/conexion.php';
session_start();

  $usuario = $_POST['usuario'];
  $password = $_POST['password'];
  $VERSION= getenv('VERSION');

    $sentencia = $Conexion->prepare("SELECT trim(contrasenia) as pass,Usuario,IdUsuario FROM t_usuarios_web where Usuario= ? and Status=1");
    $sentencia->execute([$usuario]);
    $client = $sentencia->fetch();

      if (password_verify($_POST['password'], $client['pass']))
      {
                    $_SESSION['current_user'.$VERSION] =  $usuario;
                    $_SESSION['rol_current_users'.$VERSION] = $client['TipoUsuario'];
                    $_SESSION['idusuario'.$VERSION] = $client['IdUsuario'];
                    $_SESSION['loggedin'.$VERSION] = true;
                    $_SESSION['login_time'.$VERSION] = time(); 

                  header('location: ../../Menu.php');
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
  
    $query = "SELECT contrasenia FROM t_usuarios_web WHERE usuario='$usuario';";
  $result = mysqli_query($mysqli,$query);
   while($row = mysqli_fetch_assoc($result)):

       $hash=$row['contrasenia'];

if (password_verify($password, $hash)) {
    
              $_SESSION['usuario'] = $_REQUEST['usuario'];
              $_SESSION['password'] = $_REQUEST['password'];

              $_SESSION['loggedin'] = true;
              $_SESSION['usuario'] = $usuario;

            header('location: ../Menu.php');
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
                window.location = '../index.php';
            });
               });
            </script>";
 }
 mysqli_close($mysqli); 
 endwhile;
 ?>
  