<?php
include '../api/db/conexion.php';
$VERSION = getenv('VERSION');
session_start();

if (!isset($_SESSION['current_user' . $VERSION])) {
    header("location: ../api/login/logout.php");
    exit;
}

$session_duration = 30 * 60;
$current_time = time();
if (isset($_SESSION['login_time' . $VERSION]) && ($current_time - $_SESSION['login_time' . $VERSION] > $session_duration)) {
    session_unset();
    session_destroy();
    header('location: ../api/login/logout.php');
    exit;
} else {
    $_SESSION['login_time' . $VERSION] = $current_time;
}

$user = $_SESSION['current_user' . $VERSION];
$IdUsuario = $_SESSION['idusuario' . $VERSION];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AlpasaCS | Sistema de Control de Accesos</title>
    <link rel="icon" type="image/x-icon" href="<?php echo base_url; ?>images/icportada.ico" />
    
    <!-- Google Fonts -->
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
    
    <!-- Summernote -->
    <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
    
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    
    <style>
        /* Estilos generales */
        th, td, option, select, input {
            text-align: center !important;
            vertical-align: middle !important;
        }
        
        /* Select2 personalizado */
        .select2-results__option {
            display: block !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: #000 !important;
        }
        
        .select2-container--default .select2-search--inline .select2-search__field {
            color: #000 !important;
        }
        
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            height: auto !important;
        }
        
        .select2-container .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            max-height: none;
        }
        
        /* Utilidades */
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
        
        .hidden {
            display: none;
        }
        
        .no-hidden {
            display: table-cell !important;
            text-wrap: nowrap;
        }
        
        .card-disabled {
            opacity: 0.6;
            pointer-events: none;
            background-color: #f0f0f0;
        }
        
        /* DataTables personalización */
        .dt-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .dataTables_wrapper .dataTables_filter {
            float: right;
        }
        
        .dataTables_wrapper .dataTables_length {
            float: left;
        }
        
        .dataTables_wrapper .dataTables_info {
            float: left;
        }
        
        .dataTables_wrapper .dataTables_paginate {
            float: right;
        }
        
        /* Estilos específicos para el catálogo */
        .thumbnail-image {
            cursor: pointer;
            transition: transform 0.3s ease;
            border: 2px solid #ddd;
            border-radius: 5px;
            object-fit: cover;
        }
        
        .thumbnail-image:hover {
            transform: scale(1.05);
            border-color: #d94f00;
        }
        
        .view-photo-link {
            cursor: pointer;
            color: #007bff;
            text-decoration: none;
            font-size: 12px;
        }
        
        .view-photo-link:hover {
            text-decoration: underline;
        }
        
        #modalPhoto {
            max-width: 100%;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        
        .btn-group .btn-sm {
            margin: 1px;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px 8px;
            margin-left: 5px;
        }
        
        .table td, .table th {
            vertical-align: middle;
        }
        
        .badge {
            font-size: 0.9em;
            font-weight: 500;
        }
        
        .btn-g {
            background-color: #d94f00;
            border-color: #d94f00;
        }
        
        .btn-g:hover {
            background-color: #b84200;
            border-color: #b84200;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once "../templates/nav.php"; ?>
        <?php include_once "../templates/aside.php"; ?>

        