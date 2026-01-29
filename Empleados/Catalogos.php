<?php
include_once "../templates/head.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                   <h1 class="m-0 fw-bold" style="color: #d94f00">Catálogo de Personal</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- CARD DE FILTROS -->
            <div class="card card-primary mb-4">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>No. Empleado:</label>
                                <input type="text" id="filtro-noempleado" class="form-control" style="border-color: #d94f00;" placeholder="Ej: 00123">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" id="filtro-nombre" class="form-control" style="border-color: #d94f00;" placeholder="Nombre o apellidos">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Cargo:</label>
                                <select id="filtro-cargo" class="form-control select2-cargo" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Departamento:</label>
                                <select id="filtro-departamento" class="form-control select2-departamento" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Ubicación:</label>
                                <select id="filtro-ubicacion" class="form-control select2-ubicacion" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todas</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>Empresa:</label>
                                <select id="filtro-empresa" class="form-control select2-empresa" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Estatus:</label>
                                <select id="filtro-estatus" class="form-control" style="border-color: #d94f00;">
                                    <option value="">Todos</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                    <option value="Baja">Baja</option>
                                    <option value="Vacaciones">Vacaciones</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Vehículo:</label>
                                <select id="filtro-vehiculo" class="form-control" style="border-color: #d94f00;">
                                    <option value="">Todos</option>
                                    <option value="1">Con Vehículo</option>
                                    <option value="0">Sin Vehículo</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="form-group text-right mt-4">
                                <button type="button" id="btn-aplicar-filtros" class="btn btn-primary" style="background-color: #d94f00; border-color: #d94f00;">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                                <button type="button" id="btn-limpiar-filtros" class="btn btn-outline-primary">
                                    <i class="fas fa-broom mr-1"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary" id="btn-export-excel">
                                    <i class="fas fa-file-excel mr-1"></i> Excel
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-export-pdf">
                                    <i class="fas fa-file-pdf mr-1"></i> PDF
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-print">
                                    <i class="fas fa-print mr-1"></i> Imprimir
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-refresh">
                                    <i class="fas fa-sync-alt mr-1"></i> Recargar
                                </button>
                                <button type="button" class="btn btn-primary" id="btn-nuevo-personal" style="background-color: #d94f00; border-color: #d94f00;">
                                    <i class="fas fa-plus mr-1"></i> Nuevo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CARD DE TABLA -->
            <div class="card">
                <div class="card-header text-white" style="background-color: #2c3e50; padding: 1rem;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users mr-2"></i>Lista de Personal
                    </h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" id="table-search" class="form-control" placeholder="Buscar en tabla...">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-primary">Cargando personal...</p>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTablePersonal">
                            <thead>
                                <tr>
                                    <th>NoEmpleado</th>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Apellido Paterno</th>
                                    <th>Apellido Materno</th>
                                    <th>Cargo</th>
                                    <th>Departamento</th>
                                    <th>Empresa</th>
                                    <th>Estatus</th>
                                    <th>Ubicación</th>
                                    <th>Vehículo</th>
                                    <th>Acceso</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dataTables_info" id="dataTablePersonal_info" role="status" aria-live="polite">
                                Mostrando 0 a 0 de 0 registros
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dataTables_paginate paging_simple_numbers" id="dataTablePersonal_paginate">
                                <!-- Paginación se generará automáticamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPhoto" src="" alt="Foto" class="img-fluid" style="max-height: 70vh;">
                <p id="modalEmployeeName" class="mt-3 font-weight-bold"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="vehiculoModal" tabindex="-1" aria-labelledby="vehiculoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title" id="vehiculoModalLabel">
                    <i class="fas fa-car mr-2"></i>Información del Vehículo
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="vehiculoModalBody">
                <!-- Contenido se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-gestionar-vehiculo" style="background-color: #d94f00; border-color: #d94f00;">
                    <i class="fas fa-edit mr-1"></i> Gestionar Vehículo
                </button>
            </div>
        </div>
    </div>
</div>

<div id="modal-container"></div>

<?php
include_once '../templates/footer.php';
?>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.6.0/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    var dataTable = null;
    var selectedRowData = null;
    
    function showNotification(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 
                         type === 'error' ? 'alert-danger' : 
                         type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
    
    function cargarDatosFiltros() {
        // Cargar cargos
        $.ajax({
            url: 'Controlador/ajax_get_cargos.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-cargo');
                    select.empty();
                    select.append('<option value="">Todos</option>');
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'bootstrap4',
                        language: 'es',
                        placeholder: 'Seleccionar cargo...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando cargos:', error);
            }
        });
        
        // Cargar departamentos
        $.ajax({
            url: 'Controlador/ajax_get_departamentos.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-departamento');
                    select.empty();
                    select.append('<option value="">Todos</option>');
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'bootstrap4',
                        language: 'es',
                        placeholder: 'Seleccionar departamento...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando departamentos:', error);
            }
        });
        
        // Cargar ubicaciones
        $.ajax({
            url: 'Controlador/ajax_get_ubicaciones.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-ubicacion');
                    select.empty();
                    select.append('<option value="">Todas</option>');
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'bootstrap4',
                        language: 'es',
                        placeholder: 'Seleccionar ubicación...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando ubicaciones:', error);
            }
        });
        
        // Cargar empresas
        $.ajax({
            url: 'Controlador/ajax_get_empresas.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-empresa');
                    select.empty();
                    select.append('<option value="">Todas</option>');
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'bootstrap4',
                        language: 'es',
                        placeholder: 'Seleccionar empresa...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando empresas:', error);
            }
        });
    }
    
    function inicializarDataTable() {
        if ($.fn.DataTable.isDataTable('#dataTablePersonal')) {
            if (dataTable) {
                dataTable.destroy();
            }
            $('#dataTablePersonal tbody').empty();
        }
        
        dataTable = $('#dataTablePersonal').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "Controlador/Obtener_Personal.php",
                type: "POST",
                data: function(d) {
                    d.noempleado = $('#filtro-noempleado').val();
                    d.nombre = $('#filtro-nombre').val();
                    d.cargo = $('#filtro-cargo').val();
                    d.departamento = $('#filtro-departamento').val();
                    d.ubicacion = $('#filtro-ubicacion').val();
                    d.estatus = $('#filtro-estatus').val();
                    d.empresa = $('#filtro-empresa').val();
                    d.vehiculo = $('#filtro-vehiculo').val();
                },
                beforeSend: function() {
                    $('#loading').show();
                },
                complete: function() {
                    $('#loading').hide();
                },
                dataSrc: function(json) {
                    if (json.error) {
                        showNotification('Error al cargar los datos: ' + json.error, 'error');
                        return [];
                    }
                    
                    if (json.data && Array.isArray(json.data)) {
                        return json.data;
                    } else {
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    showNotification('Error al cargar los datos.', 'error');
                }
            },
            columns: [
                { 
                    data: "NoEmpleado",
                    className: "text-center",
                    orderable: true
                },
                { 
                    data: "Foto",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        // Si no hay foto, mostrar iniciales
                        if (!data || data === '') {
                            var nombre = row.Nombre || '';
                            var apPaterno = row.ApPaterno || '';
                            var iniciales = (nombre.charAt(0) + apPaterno.charAt(0)).toUpperCase();
                            var color = getColorForInitials(iniciales);
                            
                            return `<div class="employee-initials" style="width: 40px; height: 40px; border-radius: 50%; background-color: ${color}; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin: 0 auto;">${iniciales}</div>`;
                        }
                        return data;
                    }
                },
                { 
                    data: "Nombre",
                    orderable: true
                },
                { 
                    data: "ApPaterno",
                    orderable: true
                },
                { 
                    data: "ApMaterno",
                    orderable: true
                },
                { 
                    data: "Cargo",
                    orderable: true
                },
                { 
                    data: "Departamento",
                    orderable: true
                },
                { 
                    data: "Empresa",
                    orderable: true
                },
                { 
                    data: "Estatus",
                    className: "text-center",
                    orderable: true,
                    render: function(data, type, row) {
                        var badgeClass = 'badge-secondary';
                        if (data === 'Activo') badgeClass = 'badge-success';
                        else if (data === 'Inactivo') badgeClass = 'badge-danger';
                        else if (data === 'Vacaciones') badgeClass = 'badge-warning';
                        else if (data === 'Baja') badgeClass = 'badge-dark';
                        
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { 
                    data: "Ubicacion",
                    orderable: true,
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                },
                { 
                    data: "Vehiculo",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var tieneVehiculo = row.TieneVehiculo || false;
                        var badgeClass = tieneVehiculo ? 'badge-primary vehicle-badge' : 'badge-secondary';
                        var badgeText = tieneVehiculo ? 'Con Vehículo' : 'Sin Vehículo';
                        var clickable = tieneVehiculo ? 'cursor-pointer' : '';
                        var noEmpleado = row.NoEmpleado || '';
                        var nombreCompleto = (row.Nombre || '') + ' ' + (row.ApPaterno || '') + ' ' + (row.ApMaterno || '');
                        
                        if (tieneVehiculo) {
                            return `<span class="badge ${badgeClass} ${clickable} btn-ver-vehiculo" 
                                    data-noempleado="${noEmpleado}" 
                                    data-nombre="${nombreCompleto.trim()}">
                                    ${badgeText}
                                </span>`;
                        } else {
                            return `<span class="badge ${badgeClass}">
                                    ${badgeText}
                                </span>`;
                        }
                    }
                },
                { 
                    data: "Acceso",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return data || 'N/A';
                    }
                }
            ],
            language: {
                processing: "Procesando...",
                lengthMenu: "Mostrar _MENU_ registros",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                search: "Buscar:",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            initComplete: function(settings, json) {
                $('#loading').hide();
                initEvents();
                configurarBotonesExportacion();
                showNotification('Catálogo cargado correctamente', 'success');
            },
            drawCallback: function(settings) {
                initEvents();
            }
        });
        
        // Configurar búsqueda personalizada en la tabla
        $('#table-search').on('keyup', function() {
            dataTable.search(this.value).draw();
        });
    }
    
    function getColorForInitials(initials) {
        // Colores para las iniciales
        var colors = [
            '#d94f00', '#2c3e50', '#3498db', '#e74c3c', '#2ecc71',
            '#9b59b6', '#1abc9c', '#f39c12', '#d35400', '#c0392b'
        ];
        
        var sum = 0;
        for (var i = 0; i < initials.length; i++) {
            sum += initials.charCodeAt(i);
        }
        
        return colors[sum % colors.length];
    }
    
    function configurarBotonesExportacion() {
        if (dataTable) {
            // Agregar botones de exportación a DataTable
            dataTable.buttons().container().appendTo('#dataTablePersonal_wrapper .col-md-6:eq(0)');
        }
    }
    
    function mostrarInformacionVehiculo(noEmpleado, nombre) {
        $('#loading').show();
        
        $.ajax({
            url: 'Controlador/ajax_get_vehiculo_personal.php',
            type: 'GET',
            data: {
                NoEmpleado: noEmpleado
            },
            dataType: 'json',
            success: function(response) {
                $('#loading').hide();
                
                if (response.success && response.vehiculo) {
                    var vehiculo = response.vehiculo;
                    var html = `
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Empleado:</strong> ${nombre} (No. ${noEmpleado})
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-car mr-2"></i>Información del Vehículo</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Marca:</th>
                                        <td>${vehiculo.Marca || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>Modelo:</th>
                                        <td>${vehiculo.Modelo || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>Año:</th>
                                        <td>${vehiculo.Anio || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>Color:</th>
                                        <td>${vehiculo.Color || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>Placas:</th>
                                        <td>${vehiculo.Placas || 'N/A'}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-id-card mr-2"></i>Información Adicional</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>Número de Serie:</th>
                                        <td>${vehiculo.NumSerie || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>ID Vehículo:</th>
                                        <td>${vehiculo.IdVehiculo || 'N/A'}</td>
                                    </tr>
                                    <tr>
                                        <th>Estado:</th>
                                        <td><span class="badge badge-success">Asignado</span></td>
                                    </tr>
                                </table>
                                
                                ${vehiculo.RutaFoto ? `
                                <div class="text-center mt-3">
                                    <img src="${vehiculo.RutaFoto}" alt="Foto del vehículo" 
                                         class="img-fluid rounded" style="max-height: 150px; cursor: pointer; border: 1px solid #ddd;"
                                         onclick="$('#modalPhoto').attr('src', '${vehiculo.RutaFoto}'); 
                                                  $('#modalEmployeeName').text('${vehiculo.Marca || ''} ${vehiculo.Modelo || ''} - ${vehiculo.Placas || ''}'); 
                                                  $('#photoModal').modal('show');">
                                    <small class="text-muted d-block mt-1">Click para ampliar imagen</small>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    
                    $('#vehiculoModalBody').html(html);
                    
                    $('#btn-gestionar-vehiculo').off('click').on('click', function() {
                        cargarModalVehiculos(noEmpleado, nombre);
                    });
                    
                    $('#vehiculoModal').modal('show');
                    
                } else if (response.success && !response.vehiculo) {
                    var html = `
                        <div class="text-center py-4">
                            <i class="fas fa-car fa-4x text-muted mb-3"></i>
                            <h5>No hay vehículo asignado</h5>
                            <p class="text-muted">${nombre} no tiene un vehículo asignado actualmente.</p>
                            
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Para asignar un vehículo, use la opción de gestión.
                            </div>
                        </div>
                    `;
                    
                    $('#vehiculoModalBody').html(html);
                    
                    $('#btn-gestionar-vehiculo').off('click').on('click', function() {
                        cargarModalVehiculos(noEmpleado, nombre);
                    });
                    
                    $('#vehiculoModal').modal('show');
                    
                } else {
                    showNotification(response.message || 'Error al obtener información del vehículo', 'error');
                }
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al conectar con el servidor', 'error');
            }
        });
    }
    
    function cargarModalVehiculos(noEmpleado, nombre) {
        $('#vehiculoModal').modal('hide');
        
        $.ajax({
            url: 'Modales/Gestionar_Vehiculos.php',
            type: 'GET',
            data: {
                NoEmpleado: noEmpleado,
                Nombre: nombre
            },
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(response) {
                $('#loading').hide();
                $('#modal-container').html(response);
                $('#GestionarVehiculos').modal('show');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el formulario de vehículos', 'danger');
            }
        });
    }
    
    function initEvents() {
        // Evento para ver foto
        $(document).off('click', '.employee-initials, .thumbnail-image, .view-photo-link').on('click', '.employee-initials, .thumbnail-image, .view-photo-link', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var fullImage = $(this).data('full-image') || $(this).attr('src');
            var employeeName = $(this).data('employee-name') || $(this).closest('tr').find('td:eq(2)').text() + ' ' + 
                              $(this).closest('tr').find('td:eq(3)').text() + ' ' + 
                              $(this).closest('tr').find('td:eq(4)').text();
            
            if (fullImage) {
                $('#modalPhoto').attr('src', fullImage);
                $('#modalEmployeeName').text(employeeName);
                $('#photoModal').modal('show');
            }
        });
        
        // Evento para ver vehículo (solo si tiene vehículo)
        $(document).off('click', '.btn-ver-vehiculo').on('click', '.btn-ver-vehiculo', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var noEmpleado = $(this).data('noempleado');
            var nombre = $(this).data('nombre');
            
            if (noEmpleado) {
                mostrarInformacionVehiculo(noEmpleado, nombre);
            }
        });
        
        // Seleccionar fila
        $(document).off('click', '#dataTablePersonal tbody tr').on('click', '#dataTablePersonal tbody tr', function(e) {
            if (!$(e.target).hasClass('btn-ver-vehiculo') && 
                !$(e.target).hasClass('employee-initials') && 
                !$(e.target).hasClass('thumbnail-image') &&
                !$(e.target).closest('.btn-ver-vehiculo').length) {
                
                $('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                selectedRowData = dataTable.row(this).data();
            }
        });
    }
    
    // Eventos de botones
    $('#btn-aplicar-filtros').click(function() {
        if (dataTable) {
            selectedRowData = null;
            $('tr.selected').removeClass('selected');
            dataTable.ajax.reload();
        }
    });
    
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-noempleado').val('');
        $('#filtro-nombre').val('');
        $('#filtro-cargo').val('').trigger('change');
        $('#filtro-departamento').val('').trigger('change');
        $('#filtro-ubicacion').val('').trigger('change');
        $('#filtro-empresa').val('').trigger('change');
        $('#filtro-estatus').val('');
        $('#filtro-vehiculo').val('');
        
        selectedRowData = null;
        $('tr.selected').removeClass('selected');
        
        if (dataTable) {
            dataTable.ajax.reload();
        }
        showNotification('Filtros limpiados', 'info');
    });
    
    $('#btn-export-excel').click(function(e) {
        e.preventDefault();
        
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        showNotification('Generando archivo Excel...', 'info');
        dataTable.button('.buttons-excel').trigger();
    });
    
    $('#btn-export-pdf').click(function(e) {
        e.preventDefault();
        
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        showNotification('Generando archivo PDF...', 'info');
        dataTable.button('.buttons-pdf').trigger();
    });
    
    $('#btn-print').click(function(e) {
        e.preventDefault();
        
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para imprimir', 'warning');
            return;
        }
        
        showNotification('Preparando impresión...', 'info');
        dataTable.button('.buttons-print').trigger();
    });
    
    $('#btn-refresh').click(function(e) {
        e.preventDefault();
        if (dataTable) {
            dataTable.ajax.reload(null, false);
            selectedRowData = null;
            $('tr.selected').removeClass('selected');
            showNotification('Tabla recargada correctamente', 'success');
        }
    });
    
    $('#btn-nuevo-personal').click(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'Modales/Nuevo.php',
            type: 'GET',
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(response) {
                $('#loading').hide();
                $('#modal-container').html(response);
                $('#NuevoPersonal').modal('show');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el formulario de nuevo personal', 'danger');
            }
        });
    });
    
    // Búsqueda con Enter
    $('#filtro-noempleado, #filtro-nombre').keypress(function(e) {
        if (e.which == 13) {
            if (dataTable) {
                selectedRowData = null;
                $('tr.selected').removeClass('selected');
                dataTable.ajax.reload();
            }
        }
    });
    
    // Inicializar
    cargarDatosFiltros();
    inicializarDataTable();
    
    // Eventos para formularios en modales
    $(document).on('submit', '#formNuevoPersonal, #formModificarPersonal, #formCambiarEstatusPersonal, #formNuevoVehiculo', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(this);
        var action = form.attr('action');
        var method = form.attr('method') || 'POST';
        
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Procesando...');
        
        $.ajax({
            url: action,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    $('.modal').modal('hide');
                    if (dataTable) {
                        dataTable.ajax.reload(null, false);
                    }
                    
                    if (form.attr('id') === 'formNuevoPersonal') {
                        form[0].reset();
                        if ($.fn.select2) {
                            form.find('select').val(null).trigger('change');
                        }
                    }
                } else {
                    showNotification(response.message || 'Error en la operación', 'danger');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Error: ' + error, 'danger');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Cerrar modales
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        if ($(this).attr('id') !== 'photoModal' && $(this).attr('id') !== 'vehiculoModal') {
            $('#modal-container').empty();
        }
    });
});
</script>

<style>
.badge { 
    padding: 4px 8px; 
    border-radius: 12px; 
    font-size: 12px; 
    font-weight: 600; 
}
.badge-success { background-color: #28a745; color: white; }
.badge-danger { background-color: #dc3545; color: white; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.badge-primary { background-color: #d94f00; color: white; }
.badge-dark { background-color: #343a40; color: white; }

.vehicle-badge {
    cursor: pointer;
    transition: all 0.2s;
}
.vehicle-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

#loading {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.95);
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
    z-index: 99999;
    text-align: center;
}

.select2-container--bootstrap4 {
    width: 100% !important;
}

.select2-container--bootstrap4 .select2-selection {
    min-height: 38px;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
}

.select2-container--bootstrap4 .select2-selection--single {
    height: 38px;
}

.select2-container--bootstrap4 .select2-dropdown {
    border-color: #ced4da;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    z-index: 1060 !important;
}

.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
}

.btn-outline-primary {
    border-color: #d94f00;
    color: #d94f00;
}

.btn-outline-primary:hover {
    background-color: #d94f00;
    border-color: #d94f00;
    color: white;
}

.btn-group {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.btn-group .btn {
    padding: 8px 12px;
    font-size: 14px;
}

.table th {
    background-color: #2c3e50;
    color: white;
    border-color: #1a252f;
    text-align: center;
    vertical-align: middle;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(44, 62, 80, 0.05);
}

#dataTablePersonal tbody tr {
    cursor: pointer;
    transition: background-color 0.2s;
}

#dataTablePersonal tbody tr:hover {
    background-color: rgba(217, 79, 0, 0.05) !important;
}

#dataTablePersonal tbody tr.selected {
    background-color: rgba(217, 79, 0, 0.15) !important;
}

.thumbnail-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
    cursor: pointer;
    transition: transform 0.2s;
}

.thumbnail-image:hover {
    transform: scale(1.05);
}

.dataTables_wrapper {
    margin-top: 10px;
}

.dataTables_length,
.dataTables_filter {
    margin-bottom: 10px;
}

.dataTables_info {
    padding-top: 10px;
}

.card-tools .input-group {
    margin-top: -5px;
}

.employee-initials {
    cursor: pointer;
    transition: transform 0.2s;
}

.employee-initials:hover {
    transform: scale(1.1);
}

@media (max-width: 1200px) {
    .btn-group .btn {
        padding: 6px 8px !important;
        font-size: 12px !important;
        margin: 2px !important;
    }
    
    .btn-group .btn i {
        margin-right: 3px !important;
    }
}

@media (max-width: 768px) {
    .badge { 
        font-size: 10px !important; 
        padding: 3px 6px !important;
    }
    
    .select2-container--bootstrap4 .select2-selection {
        min-height: 42px;
    }
    
    .select2-container--bootstrap4 .select2-selection--single {
        height: 42px;
    }
    
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
    }
    
    .col-md-2, .col-md-3 {
        margin-bottom: 15px !important;
    }
    
    .form-group label {
        font-size: 14px !important;
        margin-bottom: 5px !important;
    }
    
    .btn-group {
        width: 100%;
        justify-content: center;
        margin-top: 15px !important;
    }
    
    .btn-group .btn {
        flex: 1;
        margin-bottom: 5px;
        padding: 6px 8px !important;
        font-size: 12px !important;
    }
    
    .dataTables_length,
    .dataTables_filter {
        text-align: center !important;
        margin-bottom: 15px !important;
    }
    
    .dataTables_filter input {
        width: 100% !important;
    }
    
    .table td, .table th {
        font-size: 12px !important;
        padding: 6px 4px !important;
    }
    
    .thumbnail-image {
        width: 40px !important;
        height: 40px !important;
    }
    
    .card-tools {
        width: 100%;
        margin-top: 10px;
    }
    
    .card-tools .input-group {
        width: 100% !important;
    }
}

@media (max-width: 576px) {
    .btn-group {
        flex-wrap: wrap;
    }
    
    .btn-group .btn {
        flex-basis: calc(50% - 5px);
        margin-bottom: 10px;
        font-size: 11px !important;
    }
    
    .btn-group .btn i {
        font-size: 10px !important;
    }
    
    .table-responsive {
        border: none;
    }
}

@media print {
    .btn-group,
    .card-header,
    .form-group,
    #loading,
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate,
    .btn-ver-vehiculo,
    .employee-initials,
    .thumbnail-image,
    .select2-container,
    .card-tools {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-body {
        padding: 0 !important;
    }
    
    .card-footer {
        display: none !important;
    }
    
    .table th {
        background-color: #f8f9fa !important;
        color: #000 !important;
        border: 1px solid #dee2e6 !important;
    }
    
    body {
        margin: 0.5cm !important;
        font-size: 10pt !important;
    }
    
    .table {
        font-size: 10pt !important;
        border: 1px solid #000 !important;
    }
    
    .badge {
        background-color: transparent !important;
        color: #000 !important;
        border: 1px solid #000 !important;
        padding: 1px 4px !important;
    }
    
    a[href]:after {
        content: none !important;
    }
    
    #dataTablePersonal tbody tr.selected {
        background-color: #f8f9fa !important;
    }
}
</style>