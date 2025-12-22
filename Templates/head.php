<!-- En head.php -->
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
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $BaseURL;?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?php echo $BaseURL;?>/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo $BaseURL;?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $BaseURL;?>/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo $BaseURL;?>/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?php echo $BaseURL;?>/plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="<?php echo $BaseURL;?>/plugins/select2/css/select2.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/x-icon" href="<?php echo $BaseURL; ?>/images/icportada.ico" />
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
  
  <title><?php echo strtoupper($VERSION); ?></title>

  <style>
    th, td, option, select, input {
      text-align: center !important;
      vertical-align: middle !important;
    }
    .select2-results__option {
      display: block !important;
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
    
    /* Estilos para la tabla de personal */
    .employee-photo {
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #4e73df;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .employee-photo:hover {
      transform: scale(1.1);
      cursor: pointer;
    }
    .view-photo-link {
      font-size: 11px;
      color: #4e73df;
      text-decoration: none;
    }
    .view-photo-link:hover {
      text-decoration: underline;
    }
    .badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 600;
    }
    .badge-active {
      background-color: #28a745;
      color: white;
    }
    .badge-inactive {
      background-color: #dc3545;
      color: white;
    }
    .badge-department {
      background-color: #6c757d;
      color: white;
    }
    .badge-company {
      background-color: #17a2b8;
      color: white;
    }
    .btn-group .btn {
      padding: 0.25rem 0.5rem;
      font-size: 12px;
    }
    .table {
      font-size: 14px;
    }
    .table thead th {
      vertical-align: middle;
      font-weight: 600;
    }
    .table tbody td {
      vertical-align: middle;
    }
    
    .badge-estatus {
      transition: all 0.3s ease;
      border: 2px solid transparent;
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    .badge-estatus:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <?php
  include_once $BaseURL."/templates/nav.php";
  include_once $BaseURL."/templates/aside.php";
  ?>
  <div class="content-wrapper">
    <section class="content mt-4">
      <div class="container-fluid">