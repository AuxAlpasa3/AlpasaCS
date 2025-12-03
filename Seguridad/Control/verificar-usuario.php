<?php
Include '../Config/conexion.php';
session_start();

if ($mysqli->connect_error) {
 die("La conexion falló: " . $mysqli->connect_error);
}
  $usuario = $_POST['usuario'];
  $password = $_POST['password'];


    $query = "SELECT contrasenia FROM t_usuarios_seg WHERE usuario='$usuario';";
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
  