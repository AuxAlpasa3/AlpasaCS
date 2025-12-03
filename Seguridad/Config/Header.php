<?php 
  session_start();
  $usuario =$_SESSION['usuario'];
  if (isset($_SESSION['usuario']) && $_SESSION['usuario'] == true) {

       $_SESSION['usuario'] = $usuario;

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
    <title>AdminSeguridad - Alpasa</title>
    <!-- Custom fonts for this template-->
    <link href="<?php echo base_url; ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="<?php echo base_url; ?>css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <!-- Page Wrapper -->
     <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav  sidebar sidebar-dark accordion" style="background-color: #808080;" id="accordionSidebar">
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
            <!-- Heading -->
            <div class="sidebar-heading">
                Configuración
            </div>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Catalogos</span>
                </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="<?php echo base_url;?>Configuracion/Usuarios">Usuarios Moviles</a>
                        <a class="collapse-item" href="<?php echo base_url;?>Configuracion/UsuariosWeb">Usuarios Web</a>
                        <a class="collapse-item" href="<?php echo base_url;?>Configuracion/UsuarioSeg">Usuarios Seguridad</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Catalogos Internos</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="<?php echo base_url;?>Catalogos-Internos/Empresas">Empresas</a>
                        <a class="collapse-item" href="<?php echo base_url;?>Catalogos-Internos/Almacenes">Almacenes</a>
                        <a class="collapse-item" href="<?php echo base_url;?>Catalogos-Internos/Departamentos">Departamentos</a>
                        <a class="collapse-item" href="<?php echo base_url;?>Catalogos-Internos/Cargo">Cargos</a>
                    </div>
                </div>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url;?>Configuracion/Bitacora">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Bitacora de Seguridad</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo  $usuario;  ?> 
                                </span>
                                 <img class="img-profile rounded-circle"
                                    src="../Fondos/seg.png">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Cerrar Sesión
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
