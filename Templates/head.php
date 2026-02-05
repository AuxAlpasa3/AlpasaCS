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
    <!-- En el head de tu HTML -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    
    <!-- Summernote -->
    <link rel="stylesheet" href="../plugins/summernote/summernote-bs4.min.css">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">

    <!-- Incluir el tema personalizado -->
    <link rel="stylesheet" href="../css/theme-primary.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
        
    <style>
        /* Estilos generales */
        th, td, option, select, input {
            text-align: center !important;
            vertical-align: middle !important;
        }
        
        /* Select2 personalizado */
        .select2-container--bootstrap-5 {
            width: 100% !important;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .select2-container--bootstrap-5 .select2-selection:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
            color: #212529;
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            height: 38px;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #ced4da;
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            z-index: 1060 !important;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 6px 12px;
            font-size: 14px;
        }

        .select2-container--bootstrap-5 .select2-selection__clear {
            margin-right: 30px;
            font-size: 18px;
            color: #6c757d;
        }

        .select2-container--bootstrap-5 .select2-selection__clear:hover {
            color: #dc3545;
        }

        /* Estilos para resultados personalizados */
        .select2-result-item {
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .select2-result-item:hover {
            background-color: #f8f9fa;
        }

        .select2-result-item:last-child {
            border-bottom: none;
        }

        .select2-result-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            font-size: 14px;
            line-height: 1.4;
        }

        .select2-result-meta {
            font-size: 12px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .select2-result-meta .badge {
            font-size: 11px;
            padding: 3px 8px;
            font-weight: 500;
        }

        .select2-loading {
            padding: 12px;
            color: #666;
            font-size: 14px;
            text-align: center;
        }

        .select2-loading .fa-spinner {
            margin-right: 8px;
            color: #d94f00;
        }

        .select2-results__message {
            padding: 12px;
            color: #6c757d;
            font-style: italic;
            text-align: center;
            font-size: 14px;
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
            color: #d94f00;
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
        
        /* Estilos para loading */
        #loading {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1000;
            top: 0;
            left: 0;
        }

        /* Ajustes para responsividad */
        @media (max-width: 768px) {
            .select2-container--bootstrap-5 .select2-selection {
                min-height: 42px;
            }
            
            .select2-container--bootstrap-5 .select2-selection--single {
                height: 42px;
            }
            
            .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
                line-height: 40px;
                font-size: 16px;
            }
            
            .select2-dropdown {
                font-size: 16px;
            }
            
            .select2-result-item {
                padding: 12px;
            }
            
            .select2-result-title {
                font-size: 15px;
            }
        }

        @media (max-width: 576px) {
            .select2-container--bootstrap-5 {
                font-size: 16px;
            }
            
            .select2-search--dropdown .select2-search__field {
                font-size: 16px;
                height: 44px;
            }
        }

        /* Scroll personalizado para Select2 en móviles */
        @media (max-width: 768px) {
            .select2-dropdown {
                max-height: 300px;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Mejoras para el layout de filtros */
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
            font-size: 14px;
        }

        .form-control:focus, .select2-container--bootstrap-5 .select2-selection:focus {
            border-color: #d94f00 !important;
            box-shadow: 0 0 0 0.2rem rgba(217, 79, 0, 0.25) !important;
        }

        /* Animación suave para cambios */
        #movimientos-container {
            transition: opacity 0.3s ease;
        }

        #movimientos-container.loading {
            opacity: 0.5;
        }

        /* Estilos para alertas */
        .alert {
            border-radius: 0.375rem;
            border: 1px solid transparent;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        /* Mejoras visuales para tabla */
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .table td, .table th {
            vertical-align: middle;
            padding: 12px 8px;
        }

        /* Estilos para los botones de acción */
        .btn-ver-entrada, .btn-ver-salida {
            min-width: 70px;
            font-size: 13px;
            padding: 4px 10px;
        }
        
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include_once "../templates/nav.php"; ?>
        <?php include_once "../templates/aside.php"; ?>