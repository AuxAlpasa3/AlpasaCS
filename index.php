<?php
  Include 'api/db/conexion.php';
   $VERSION= getenv('VERSION');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
   <title><?php echo strtoupper($VERSION); ?></title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  
  <link rel="icon" type="image/x-icon" href="<?php echo base_url; ?>images/icportada.ico" />
  <link rel="stylesheet" href="./plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="./plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="./dist/css/adminlte.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .main-footer {
      margin-left: 0 !important;
      background-color: transparent !important;
      border: none;
      color:white;
    }
    
    body {
      background: url('dist/img/fondo3.jpg') center/cover no-repeat fixed;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    
    .login-box {
      background-color: transparent; 
    }
    
    .card {
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
      border-radius: 10px;
    }

  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

<body class="hold-transition login-page">
  <?php
  if (isset($_SESSION['current_user'.$VERSION])) {
    header("location:Menu/Index.php");
    exit;
  }
  ?>
  <div class="login-box" style="background-color:darkorange;" >
    <!-- /.login-logo -->
    <div class="card card-outline card-danger" >
      <div class="card-header text-center">
        <div class="pb-2">
          <img src="dist/img/logoalpasa.png" width="100%" />
        </div>
      </div>
      <div class="card-body">
        <div class="login-box-msg">
          <h3><?php echo $VERSION ;?> </h3>
          <p class="login-box-msg">Ingresa tus credenciales</p>
        </div>
        <form action="<?php echo base_url;?>api/login/login.php" method="post">
          <div class="input-group mb-3">
            <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
          </div>
          <div>
            <button type="submit" name="login" class="btn btn- btn-block" style="background-color: darkorange; color: white;"><b>INICIAR SESIÓN<b></button>
          
          </div>
        </form>

      </div>
    </div>
  </div>
  <div class="main-footer">
    <?php include_once 'templates/footer.php' ?>
  </div>
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
