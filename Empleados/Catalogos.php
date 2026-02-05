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
            <div class="card card-primary mb-4">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem; cursor: pointer;" id="filtrosHeader">
                    <h3 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
                        </span>
                        <span class="toggle-icon">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </h3>
                </div>
                <div class="card-body" id="filtrosBody">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">No. Empleado:</label>
                                <input type="text" id="filtro-noempleado" class="form-control form-control-lg" placeholder="Ej: 00123">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Nombre:</label>
                                <input type="text" id="filtro-nombre" class="form-control form-control-lg" placeholder="Nombre o apellidos">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Cargo:</label>
                                <select id="filtro-cargo" class="form-control form-control-lg" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Departamento:</label>
                                <select id="filtro-departamento" class="form-control form-control-lg" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Ubicación:</label>
                                <select id="filtro-ubicacion" class="form-control form-control-lg" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="row mb-6">
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Empresa:</label>
                                <select id="filtro-empresa" class="form-control form-control-lg" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Estatus:</label>
                                <select id="filtro-estatus" class="form-control form-control-lg" multiple="multiple" style="width: 100%;">
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                    <option value="Baja">Baja</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Vehículo:</label>
                                <select id="filtro-vehiculo" class="form-control form-control-lg" multiple="multiple" style="width: 100%;">
                                    <option value="">Todos</option>
                                    <option value="1">Con Vehículo</option>
                                    <option value="0">Sin Vehículo</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group text-right mt-4">
                                <button type="button" id="btn-aplicar-filtros" class="btn btn-primary btn-lg" style="background-color: #d94f00; border-color: #d94f00;">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                                <button type="button" id="btn-limpiar-filtros" class="btn btn-outline-primary btn-lg">
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
            
            <div class="card">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-users mr-2"></i>Lista de Personal
                    </h3>
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
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    var filtrosExpandidos = true;
    var select2Instances = {};
    
    function toggleFiltros() {
        filtrosExpandidos = !filtrosExpandidos;
        if (filtrosExpandidos) {
            $('#filtrosBody').slideDown(300);
            $('#filtrosHeader .toggle-icon').html('<i class="fas fa-chevron-up"></i>');
            localStorage.setItem('filtrosExpandidos', 'true');
        } else {
            $('#filtrosBody').slideUp(300);
            $('#filtrosHeader .toggle-icon').html('<i class="fas fa-chevron-down"></i>');
            localStorage.setItem('filtrosExpandidos', 'false');
        }
    }
    
    var filtrosGuardados = localStorage.getItem('filtrosExpandidos');
    if (filtrosGuardados === 'false') {
        filtrosExpandidos = false;
        $('#filtrosBody').hide();
        $('#filtrosHeader .toggle-icon').html('<i class="fas fa-chevron-down"></i>');
    }
    
    $('#filtrosHeader').click(function() {
        toggleFiltros();
    });
    
    function showNotification(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : type === 'warning' ? 'alert-warning' : 'alert-info';
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>`;
        $('body').append(alertHtml);
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
    
    function getSelect2MultipleValue(selector) {
        try {
            var value = $(selector).val();
            if (!value) return '';
            if (Array.isArray(value)) {
                return value.filter(function(item) {
                    return item !== null && item !== undefined && item !== '';
                }).join(',');
            }
            return value.toString();
        } catch (e) {
            console.error('Error getting select2 value:', e);
            return '';
        }
    }
    
    function safeSelect2Destroy(selector) {
        if ($(selector).hasClass('select2-hidden-accessible')) {
            try {
                $(selector).select2('destroy');
                delete select2Instances[selector];
            } catch (e) {
                console.warn('Error destroying Select2:', e);
            }
        }
    }
    
    function safeSelect2Init(selector, options) {
        safeSelect2Destroy(selector);
        
        var defaultOptions = {
            theme: 'custom-theme',
            allowClear: true,
            width: '100%',
            language: 'es',
            closeOnSelect: false
        };
        
        var finalOptions = $.extend({}, defaultOptions, options);
        
        var instance = $(selector).select2(finalOptions);
        select2Instances[selector] = instance;
        return instance;
    }
    
    function destroyAllSelect2() {
        Object.keys(select2Instances).forEach(function(selector) {
            safeSelect2Destroy(selector);
        });
        select2Instances = {};
    }
    
    function cargarDatosFiltros() {
        destroyAllSelect2();
        
        safeSelect2Init('#filtro-estatus', {
            placeholder: 'Todos los estatus'
        });
        
        safeSelect2Init('#filtro-vehiculo', {
            placeholder: 'Todos',
            closeOnSelect: true
        });
        
        $.ajax({
            url: 'Controlador/ajax_get_cargos.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-cargo');
                    select.empty();
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    safeSelect2Init('#filtro-cargo', {
                        placeholder: 'Todos los cargos'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando cargos:', error);
            }
        });
        
        $.ajax({
            url: 'Controlador/ajax_get_departamentos.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-departamento');
                    select.empty();
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    safeSelect2Init('#filtro-departamento', {
                        placeholder: 'Todos los departamentos'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando departamentos:', error);
            }
        });
        
        $.ajax({
            url: 'Controlador/ajax_get_ubicaciones.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-ubicacion');
                    select.empty();
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    safeSelect2Init('#filtro-ubicacion', {
                        placeholder: 'Todas las ubicaciones'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando ubicaciones:', error);
            }
        });
        
        $.ajax({
            url: 'Controlador/ajax_get_empresas.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-empresa');
                    select.empty();
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    safeSelect2Init('#filtro-empresa', {
                        placeholder: 'Todas las empresas'
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
                    d.noempleado = $('#filtro-noempleado').val() || '';
                    d.nombre = $('#filtro-nombre').val() || '';
                    d.cargo = getSelect2MultipleValue('#filtro-cargo');
                    d.departamento = getSelect2MultipleValue('#filtro-departamento');
                    d.ubicacion = getSelect2MultipleValue('#filtro-ubicacion');
                    d.empresa = getSelect2MultipleValue('#filtro-empresa');
                    d.estatus = getSelect2MultipleValue('#filtro-estatus');
                    d.vehiculo = $('#filtro-vehiculo').val() || '';
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
                        if (!data || data === '') {
                            var nombre = row.Nombre || '';
                            var apPaterno = row.ApPaterno || '';
                            var iniciales = (nombre.charAt(0) + apPaterno.charAt(0)).toUpperCase();
                            var color = getColorForInitials(iniciales);
                            return `<div class="employee-initials" style="width: 40px; height: 40px; border-radius: 50%; background-color: ${color}; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin: 0 auto;" data-employee-name="${nombre} ${apPaterno}">${iniciales}</div>`;
                        }
                        return `<img src="${data}" class="thumbnail-image" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; cursor: pointer;" data-full-image="${data}" data-employee-name="${row.Nombre || ''} ${row.ApPaterno || ''}">`;
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
                },
                { 
                    data: null,
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var noEmpleado = row.NoEmpleado || '';
                        var nombre = row.Nombre || '';
                        var apPaterno = row.ApPaterno || '';
                        var estatus = row.Estatus || '';
                        var tieneVehiculo = row.TieneVehiculo || false;
                        var idPersonal = row.IdPersonal  || '';
                        return `
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-primary btn-editar" 
                                        data-noempleado="${idPersonal}"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-cambiar-estatus" 
                                        data-noempleado="${idPersonal}"
                                        data-nombre="${nombre} ${apPaterno}"
                                        data-estatus="${estatus}"
                                        title="${estatus === 'Activo' ? 'Dar de Baja' : 'Reactivar'}">
                                    <i class="fas ${estatus === 'Activo' ? 'fa-user-times' : 'fa-user-check'}"></i>
                                </button>
                                ${!tieneVehiculo ? `
                                <button type="button" class="btn btn-sm btn-info btn-gestion-vehiculo" 
                                        data-noempleado="${noEmpleado}"
                                        data-nombre="${nombre} ${apPaterno}"
                                        title="Gestionar Vehículo">
                                    <i class="fas fa-car"></i>
                                </button>
                                ` : ''}
                            </div>
                        `;
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
            searching: false,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            initComplete: function(settings, json) {
                $('#loading').hide();
                initEvents();
                showNotification('Catálogo cargado correctamente', 'success');
            },
            drawCallback: function(settings) {
                initEvents();
            }
        });
    }
    
    function getColorForInitials(initials) {
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
                                         onclick="mostrarFotoVehiculo('${vehiculo.RutaFoto}', '${vehiculo.Marca || ''} ${vehiculo.Modelo || ''} - ${vehiculo.Placas || ''}')">
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
    
    function mostrarFotoVehiculo(rutaFoto, titulo) {
        $('#modalPhoto').attr('src', rutaFoto);
        $('#modalEmployeeName').text(titulo);
        setTimeout(function() {
            $('#photoModal').modal('show');
            $('#photoModal').css('z-index', '99999');
        }, 100);
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
    
    function cargarModalEditar(noEmpleado) {
        $.ajax({
            url: 'Modales/Modificar.php',
            type: 'GET',
            data: {
                NoEmpleado: noEmpleado
            },
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(response) {
                $('#loading').hide();
                $('#modal-container').html(response);
                $('#ModificarPersonal').modal('show');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el formulario de edición', 'danger');
            }
        });
    }
    
    function cargarModalCambiarEstatus(noEmpleado, nombre, estatusActual) {
        $.ajax({
            url: 'Modales/CambiarEstatus.php',
            type: 'GET',
            data: {
                NoEmpleado: noEmpleado,
                Nombre: nombre,
                EstatusActual: estatusActual
            },
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(response) {
                $('#loading').hide();
                $('#modal-container').html(response);
                $('#CambiarEstatusPersonal').modal('show');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el formulario de cambio de estatus', 'danger');
            }
        });
    }
    
    function initEvents() {
        $(document).off('click.personal').on('click.personal', '.employee-initials, .thumbnail-image', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var fullImage = $(this).data('full-image') || null;
            var employeeName = $(this).data('employee-name') || $(this).closest('tr').find('td:eq(2)').text() + ' ' + $(this).closest('tr').find('td:eq(3)').text() + ' ' + $(this).closest('tr').find('td:eq(4)').text();
            if ($(this).hasClass('employee-initials')) {
                return;
            }
            if (fullImage) {
                $('#modalPhoto').attr('src', fullImage);
                $('#modalEmployeeName').text(employeeName);
                $('#photoModal').modal('show');
            }
        });
        
        $(document).off('click.vehiculo').on('click.vehiculo', '.btn-ver-vehiculo', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var noEmpleado = $(this).data('noempleado');
            var nombre = $(this).data('nombre');
            if (noEmpleado) {
                mostrarInformacionVehiculo(noEmpleado, nombre);
            }
        });
        
        $(document).off('click.editar').on('click.editar', '.btn-editar', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var noEmpleado = $(this).data('noempleado');
            if (noEmpleado) {
                cargarModalEditar(noEmpleado);
            }
        });
        
        $(document).off('click.estatus').on('click.estatus', '.btn-cambiar-estatus', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var noEmpleado = $(this).data('noempleado');
            var nombre = $(this).data('nombre');
            var estatus = $(this).data('estatus');
            if (noEmpleado) {
                cargarModalCambiarEstatus(noEmpleado, nombre, estatus);
            }
        });
        
        $(document).off('click.gestion').on('click.gestion', '.btn-gestion-vehiculo', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var noEmpleado = $(this).data('noempleado');
            var nombre = $(this).data('nombre');
            if (noEmpleado) {
                cargarModalVehiculos(noEmpleado, nombre);
            }
        });
    }
    
    $('#btn-aplicar-filtros').click(function() {
        if (dataTable) {
            dataTable.ajax.reload();
        }
    });
    
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-noempleado').val('');
        $('#filtro-nombre').val('');
        
        destroyAllSelect2();
        
        $('#filtro-estatus').val(null);
        $('#filtro-vehiculo').val('');
        
        safeSelect2Init('#filtro-estatus', {
            placeholder: 'Todos los estatus'
        });
        
        safeSelect2Init('#filtro-vehiculo', {
            placeholder: 'Todos',
            closeOnSelect: true
        });
        
        $('#filtro-cargo').val(null);
        $('#filtro-departamento').val(null);
        $('#filtro-ubicacion').val(null);
        $('#filtro-empresa').val(null);
        
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
        var params = {
            noempleado: $('#filtro-noempleado').val() || '',
            nombre: $('#filtro-nombre').val() || '',
            cargo: getSelect2MultipleValue('#filtro-cargo'),
            departamento: getSelect2MultipleValue('#filtro-departamento'),
            ubicacion: getSelect2MultipleValue('#filtro-ubicacion'),
            estatus: getSelect2MultipleValue('#filtro-estatus'),
            empresa: getSelect2MultipleValue('#filtro-empresa'),
            vehiculo: $('#filtro-vehiculo').val() || ''
        };
        var url = 'Controlador/Exportar_Excel.php?' + $.param(params);
        window.open(url, '_blank');
        showNotification('Generando archivo Excel...', 'info');
    });
    
    $('#btn-export-pdf').click(function(e) {
        e.preventDefault();
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        var params = {
            noempleado: $('#filtro-noempleado').val() || '',
            nombre: $('#filtro-nombre').val() || '',
            cargo: getSelect2MultipleValue('#filtro-cargo'),
            departamento: getSelect2MultipleValue('#filtro-departamento'),
            ubicacion: getSelect2MultipleValue('#filtro-ubicacion'),
            estatus: getSelect2MultipleValue('#filtro-estatus'),
            empresa: getSelect2MultipleValue('#filtro-empresa'),
            vehiculo: $('#filtro-vehiculo').val() || ''
        };
        var url = 'Controlador/Exportar_PDF.php?' + $.param(params);
        window.open(url, '_blank');
        showNotification('Generando archivo PDF...', 'info');
    });
    
    $('#btn-print').click(function(e) {
        e.preventDefault();
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para imprimir', 'warning');
            return;
        }
        var params = {
            noempleado: $('#filtro-noempleado').val() || '',
            nombre: $('#filtro-nombre').val() || '',
            cargo: getSelect2MultipleValue('#filtro-cargo'),
            departamento: getSelect2MultipleValue('#filtro-departamento'),
            ubicacion: getSelect2MultipleValue('#filtro-ubicacion'),
            estatus: getSelect2MultipleValue('#filtro-estatus'),
            empresa: getSelect2MultipleValue('#filtro-empresa'),
            vehiculo: $('#filtro-vehiculo').val() || ''
        };
        var url = 'Controlador/Imprimir.php?' + $.param(params);
        window.open(url, '_blank', 'width=1024,height=768');
        showNotification('Abriendo vista de impresión...', 'info');
    });
    
    $('#btn-refresh').click(function(e) {
        e.preventDefault();
        if (dataTable) {
            dataTable.ajax.reload(null, false);
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
                $('#NuevoPersonal').on('shown.bs.modal', function() {
                    $('#NuevoPersonal .select2').each(function() {
                        var selectId = '#' + $(this).attr('id');
                        if (!select2Instances[selectId]) {
                            safeSelect2Init(selectId, {
                                placeholder: 'Seleccione una opción',
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#NuevoPersonal')
                            });
                        }
                    });
                }).modal('show');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el formulario de nuevo personal', 'danger');
            }
        });
    });
    
    $('#filtro-noempleado, #filtro-nombre').keypress(function(e) {
        if (e.which == 13) {
            if (dataTable) {
                dataTable.ajax.reload();
            }
        }
    });
    
    $(window).on('beforeunload', function() {
        destroyAllSelect2();
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        if ($(this).attr('id') === 'NuevoPersonal' || $(this).attr('id') === 'ModificarPersonal' || 
            $(this).attr('id') === 'GestionarVehiculos' || $(this).attr('id') === 'CambiarEstatusPersonal') {
            $(this).find('.select2').each(function() {
                var selectId = '#' + $(this).attr('id');
                safeSelect2Destroy(selectId);
            });
            $('#modal-container').empty();
        }
    });
    
    cargarDatosFiltros();
    inicializarDataTable();
    
    $(document).on('submit', '#formNuevoPersonal, #formModificarPersonal, #formCambiarEstatusPersonal, #formGestionarVehiculos', function(e) {
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
                        form.find('select').val(null);
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
    
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $(this).closest('.modal').modal('hide');
    });
});
</script>

<style>
:root {
    --primary-orange: #d94f00;
    --primary-orange-dark: #b53d00;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #17a2b8;
    --secondary-color: #6c757d;
    --dark-color: #343a40;
    --border-color: #e9ecef;
    --table-striped: rgba(217, 79, 0, 0.05);
    --table-hover: rgba(217, 79, 0, 0.08);
}

.badge { 
    padding: 4px 8px; 
    border-radius: 12px; 
    font-size: 12px; 
    font-weight: 600;
}

.badge-success { 
    background-color: var(--success-color);
    color: white; 
}

.badge-danger { 
    background-color: var(--danger-color);
    color: white; 
}

.badge-warning { 
    background-color: var(--warning-color);
    color: #212529; 
}

.badge-info { 
    background-color: var(--info-color);
    color: white; 
}

.badge-secondary { 
    background-color: var(--secondary-color);
    color: white; 
}

.badge-primary { 
    background-color: var(--primary-orange);
    color: white; 
}

.badge-dark { 
    background-color: var(--dark-color);
    color: white; 
}

.vehicle-badge {
    cursor: pointer;
}

.vehicle-badge:hover {
    opacity: 0.9;
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

#loading .spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.25em;
    color: var(--primary-orange);
}

#loading p {
    margin-top: 10px;
    color: var(--primary-orange);
    font-size: 14px;
}

.select2-container--custom-theme {
    width: 100% !important;
}

.select2-container--custom-theme .select2-selection--multiple {
    min-height: 48px;
    border: 2px solid var(--border-color) !important;
    border-radius: 8px !important;
    padding: 4px 8px;
}

.select2-container--custom-theme .select2-selection--single {
    min-height: 48px;
    border: 2px solid var(--border-color) !important;
    border-radius: 8px !important;
    padding: 4px 8px;
}

.select2-container--custom-theme .select2-selection--single .select2-selection__rendered {
    line-height: 44px;
    padding-left: 12px;
    color: #495057;
    font-size: 14px;
}

.select2-container--custom-theme .select2-selection--multiple .select2-selection__rendered {
    margin: 0;
    padding: 0;
}

.select2-container--custom-theme .select2-selection--multiple .select2-selection__choice {
    background-color: var(--primary-orange);
    border: none;
    border-radius: 6px;
    color: white;
    padding: 4px 8px;
    margin: 4px 4px 4px 0;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
}

.select2-container--custom-theme .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255,255,255,0.8);
    margin-right: 4px;
    border: none;
    background: transparent;
    font-size: 16px;
    line-height: 1;
    padding: 0 2px;
}

.select2-container--custom-theme .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: white;
}

.select2-container--custom-theme.select2-container--focus .select2-selection--multiple,
.select2-container--custom-theme.select2-container--focus .select2-selection--single {
    border-color: var(--primary-orange) !important;
    box-shadow: 0 0 0 3px rgba(217, 79, 0, 0.1);
}

.select2-dropdown.select2-dropdown-enhanced {
    border: 2px solid var(--primary-orange);
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-top: 4px;
    z-index: 1060 !important;
}

.select2-container--custom-theme .select2-search--dropdown .select2-search__field {
    border: 2px solid var(--border-color);
    border-radius: 6px;
    padding: 8px 12px;
    margin: 8px;
    width: calc(100% - 16px) !important;
}

.select2-container--custom-theme .select2-search--dropdown .select2-search__field:focus {
    border-color: var(--primary-orange);
    outline: none;
}

.select2-container--custom-theme .select2-results__option {
    padding: 10px 15px;
    font-size: 14px;
}

.select2-container--custom-theme .select2-results__option--highlighted[aria-selected] {
    background-color: var(--primary-orange);
    color: white;
}

.select2-container--custom-theme .select2-results__option[aria-selected=true] {
    background-color: rgba(217, 79, 0, 0.1);
    color: var(--primary-orange);
}

.select2-container--custom-theme .select2-selection__clear {
    color: var(--secondary-color);
    font-size: 18px;
    margin-right: 8px;
    padding: 2px;
}

.select2-container--custom-theme .select2-selection__clear:hover {
    color: var(--danger-color);
}

.select2-selection-enhanced {
    cursor: pointer !important;
}

.select2-container--custom-theme .select2-selection__arrow {
    height: 46px;
    right: 8px;
    width: 20px;
}

.select2-container--custom-theme .select2-selection__arrow b {
    border-color: var(--secondary-color) transparent transparent transparent;
    border-width: 6px 6px 0 6px;
}

.select2-container--custom-theme.select2-container--open .select2-selection__arrow b {
    border-color: transparent transparent var(--secondary-color) transparent;
    border-width: 0 6px 6px 6px;
}

.select2-container--custom-theme .select2-selection--single .select2-selection__placeholder,
.select2-container--custom-theme .select2-selection--multiple .select2-selection__placeholder {
    color: var(--secondary-color);
    font-style: italic;
}

.select2-selection__choice-count {
    background: rgba(255, 255, 255, 0.9);
    color: var(--primary-orange);
    border-radius: 12px;
    padding: 1px 6px;
    font-size: 11px;
    font-weight: bold;
    margin-left: 4px;
}

.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
}

.btn-outline-primary {
    border-color: var(--primary-orange);
    color: var(--primary-orange);
}

.btn-outline-primary:hover {
    background-color: var(--primary-orange);
    border-color: var(--primary-orange);
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

#filtrosHeader {
    transition: background-color 0.2s;
}

#filtrosHeader:hover {
    background-color: var(--primary-orange-dark) !important;
}

#filtrosHeader .toggle-icon {
    transition: transform 0.2s;
}

.table th {
    background-color: var(--primary-orange);
    color: white;
    border-color: var(--primary-orange-dark);
    text-align: center;
    vertical-align: middle;
    font-weight: 600;
    padding: 1rem;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: var(--table-striped);
}

#dataTablePersonal tbody tr {
    cursor: default;
}

#dataTablePersonal tbody tr:hover {
    background-color: var(--table-hover);
}

.thumbnail-image {
    cursor: pointer;
    transition: transform 0.2s;
}

.thumbnail-image:hover {
    transform: scale(1.05);
}

.employee-initials {
    cursor: default;
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

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
    font-size: 14px;
    display: block;
}

.form-control-lg {
    min-height: 48px;
    padding: 10px 12px;
    font-size: 14px;
    border-radius: 8px;
    border: 2px solid var(--border-color);
}

.form-control-lg:focus {
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 3px rgba(217, 79, 0, 0.1);
    outline: none;
}

.btn-lg {
    padding: 10px 20px;
    font-size: 15px;
    border-radius: 8px;
    font-weight: 600;
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
    
    .form-control-lg {
        min-height: 42px;
        padding: 8px 10px;
    }
    
    .select2-container--custom-theme .select2-selection--multiple,
    .select2-container--custom-theme .select2-selection--single {
        min-height: 42px;
    }
    
    .select2-container--custom-theme .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
    }
}

@media (max-width: 768px) {
    .badge { 
        font-size: 10px !important; 
        padding: 3px 6px !important;
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
    
    .thumbnail-image, .employee-initials {
        width: 35px !important;
        height: 35px !important;
    }
    
    .form-control-lg {
        min-height: 38px;
        padding: 6px 10px;
        font-size: 13px;
    }
    
    .btn-lg {
        padding: 8px 16px;
        font-size: 14px;
    }
    
    .select2-container--custom-theme .select2-selection--multiple .select2-selection__choice {
        font-size: 11px;
        padding: 3px 6px;
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
    
    .form-control-lg {
        font-size: 12px;
    }
    
    .select2-container--custom-theme .select2-results__option {
        padding: 8px 12px;
        font-size: 13px;
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
    .toggle-icon {
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
}

#photoModal {
    z-index: 99999 !important;
}

#photoModal .modal-dialog {
    z-index: 99999 !important;
}

.modal.fade.show {
    z-index: 99999 !important;
}

.modal-backdrop.show {
    z-index: 99998 !important;
}

.modal.fade.show ~ .modal-backdrop {
    z-index: 99998 !important;
}

.btn-group .btn {
    padding: 4px 8px !important;
    margin: 0 2px !important;
    font-size: 12px !important;
}

.btn-group .btn i {
    font-size: 12px !important;
}

.btn-sm {
    min-width: 32px;
}

.select2-multiple-hint {
    display: block;
    font-size: 11px;
    color: var(--secondary-color);
    margin-top: 4px;
    font-style: italic;
}
</style>