<?php 
  session_start();
  $usuario =$_SESSION['usuario'];
  if (isset($_SESSION['usuario']) && $_SESSION['usuario'] == true) {
      $sql = "SELECT EmpleadoId FROM t_usuarios_web WHERE usuario = '$usuario';";
  if ($resultado = mysqli_query($mysqli, $sql)) {

    while ($row = mysqli_fetch_assoc($resultado)) {      
        $Empleado_Ref =$row["EmpleadoId"];
  
            if ($Empleado_Ref<>null)
            {
               $query = "SELECT Nombre,ApPaterno, ApMaterno,RutaFoto FROM t_personal WHERE IdPersonal = '$Empleado_Ref';";
              if ($ress = mysqli_query($mysqli, $query)) {

                while ($fila = mysqli_fetch_assoc($ress)) {
                    $nombre = $fila["Nombre"];        
                    $ApPaterno =$fila["ApPaterno"];
                    $ApMaterno =$fila["ApMaterno"];
                    $RutaFoto =$fila["RutaFoto"];
                  }
                }
            }
        }
    }
              $_SESSION['Nombre'] = $nombre;
              $_SESSION['ApPaterno'] = $ApPaterno;
              $_SESSION['ApMaterno'] = $ApMaterno;

  } else {
     header("Location:" .base_url.'Index');
  exit;
  }
?> 

 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Control de Accesos - Alpasa</title>
    <!-- Custom fonts for this template-->
    <link href="<?php echo base_url; ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>

    <script src="<?php echo base_url; ?>js/sb-admin-2.min.js"></script>

    <script src="<?php echo base_url; ?>Control-Accesos/Controlador/controlador.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="<?php echo base_url; ?>css/sb-admin-2.min.css" rel="stylesheet">

    <link href="<?php echo base_url; ?>css/card.css" rel="stylesheet">
</head>
<body id="page-top">
    <!-- Page Wrapper -->
     <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav  sidebar sidebar-dark accordion" style="background-color: #d94f00;" id="accordionSidebar">
            <!-- Sidebar - Brand -->
              <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url; ?>Menu">
                <img src="<?php echo base_url; ?>Fondos/LogoAlpasa.gif" id="Logo" position="center"alt="Logo Alpasa" width="200"> 
              </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
              <li class="nav-item active">
                <a class="nav-link" href="<?php echo base_url; ?>Menu">
                    <i class="fas fa-fw fa-house-alt"></i>
                    <span>Menu</span></a>
              </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Heading -->
            <div class="sidebar-heading">Procesos</div>
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Catalogos</span>
                </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="<?php echo base_url; ?>Catalogos/Personal">Personal</a>
                            <a class="collapse-item" href="<?php echo base_url; ?>Catalogos/Vehiculos">Vehiculos</a>
                            <a class="collapse-item" href="<?php echo base_url; ?>Catalogos/Maniobras">Maniobras</a> 
                    </div>
                </div>
            </li>
            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Control de Accesos</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="<?php echo base_url; ?>Control-Accesos/Personal">Personal</a>
                        <a class="collapse-item" href="<?php echo base_url; ?>Control-Accesos/Vehiculos">Vehiculos</a>
                        <a class="collapse-item" href="<?php echo base_url; ?>Control-Accesos/Visitantes">Visitantes</a>
                        <a class="collapse-item" href="<?php echo base_url; ?>Control-Accesos/Maniobras">Maniobras</a>
                    </div>
                </div>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                     <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                    <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo  $nombre;  ?> 
                                </span>
                                 <img class="img-profile rounded-circle"
                                    src="<?php echo $RutaFoto; ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
