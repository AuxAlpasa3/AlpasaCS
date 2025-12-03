<?php 
  Include '../api/db/conexion.php';
    $VERSION= getenv('VERSION');
  ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">

  <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <link rel="icon" type="image/x-icon" href="<?php echo base_url; ?>images/icportada.ico" />

<!-- jQuery (debe ir antes que Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

 <title><?php echo strtoupper($VERSION); ?></title>
 
  <style>
       th,td,option,select, input {
      text-align: center !important;
      vertical-align: middle !important;
    }
    .select2-results__option {
    display: block !important; /* Fuerza la visualización */
    }

    .white-nowrap {
      text-wrap: nowrap;
    }
    .marco {
      border: 2px solid rgba(0, 0, 0, .1);
      padding: 1rem;
      border-radius: .5rem;
      margin: 0 .25rem;
      margin-bottom: 1rem;
      text-align: left;
    }
    .block {
      display: block;
      width: 100%;
    }
    .hidden{
      display: none;
    }
    .no-hidden{
      display: table-cell !important;
      text-wrap: nowrap;
    }

.card-disabled {
  opacity: 0.6; 
  pointer-events: none; 
  background-color: #f0f0f0;
}

  </style>
</head>