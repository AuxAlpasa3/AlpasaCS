<?php
include '../templates/head.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 fw-bold" style="color: #d94f00">Catálogo de Personal Externo</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <!-- No. Identificación -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>No. Identificación:</label>
                                <input type="text" id="filtro-numeroIdentificacion" class="form-control" style="border-color: #d94f00;" placeholder="Ej: EXT-001">
                            </div>
                        </div>
                        
                        <!-- Nombre Completo -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" id="filtro-nombre" class="form-control" style="border-color: #d94f00;" placeholder="Buscar por nombre">
                            </div>
                        </div>
                        
                        <!-- Cargo -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Cargo:</label>
                                <select id="filtro-cargo" class="form-control select2-cargo" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Área de Visita -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Área de Visita:</label>
                                <select id="filtro-areaVisita" class="form-control select2-areaVisita" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todas</option>
                                </select>
                            </div>
                        </div>
                         <!-- Personal Responsable -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Personal Responsable:</label>
                                <select id="filtro-personalResponsable" class="form-control select2-personalResponsable" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="btn-aplicar-filtros" class="btn btn-primary btn-block" style="background-color: #d94f00; border-color: #d94f00;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                       
                        
                        <!-- Estatus -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Estatus:</label>
                                <select id="filtro-estatus" class="form-control" style="border-color: #d94f00;">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                    <option value="2">Baja</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="btn-limpiar-filtros" class="btn btn-outline-primary btn-block">
                                    <i class="fas fa-broom"></i> Limpiar
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6 text-right">
                            <div class="btn-group mt-6" role="group">
                                <button type="button" class="btn btn-outline-primary" id="btn-export-excel">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-export-pdf">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-print">
                                    <i class="fas fa-print"></i> Imprimir
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-refresh">
                                    <i class="fas fa-sync-alt"></i> Recargar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-primary">Cargando Personal Externo...</p>
                    </div>
                    
                    <!-- Botón añadir nuevo -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn-nuevo btn btn-primary btn-g" style="background-color: #d94f00; border-color: #d94f00;">
                                <i class="fa fa-plus"></i> Añadir Nuevo Personal Externo
                            </button>
                        </div>
                    </div>
                    
                    <div id="notification-area" class="mt-3"></div>
                    
                    <div class="table-responsive pt-2">
                        <table class="table table-bordered table-striped" id="dataTablePersonalExterno">
                            <thead>
                                <tr>
                                    <th>No. Identificación</th>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Apellido Paterno</th>
                                    <th>Apellido Materno</th>
                                    <th>Empresa</th>
                                    <th>Cargo</th>
                                    <th>Área Visita</th>
                                    <th>Personal Responsable</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Vigencia Acceso</th>
                                    <th>Estatus</th>
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
            
            <!-- Modal para ver foto -->
            <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="photoModalLabel">Foto del Personal Externo</h5>
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
            
            <!-- Contenedor para modales dinámicos -->
            <div id="modal-container"></div>
        </div>
    </section>
</div>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>

<!-- JSZip para Excel -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.6.0/jszip.min.js"></script>

<!-- PDFMake para PDF -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Variables globales
    var dataTable = null;
    var currentData = [];
    
    // Función para mostrar notificación
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
        
        // Auto-remover después de 5 segundos
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
                        placeholder: 'Seleccionar cargo...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            },
            error: function() {
                $('#filtro-cargo').html('<option value="">Error al cargar</option>');
            }
        });
        
        // Cargar áreas de visita
        $.ajax({
            url: 'Controlador/ajax_get_areasVisita.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-areaVisita');
                    select.empty();
                    select.append('<option value="">Todas</option>');
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'bootstrap4',
                        placeholder: 'Seleccionar área...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            },
            error: function() {
                $('#filtro-areaVisita').html('<option value="">Error al cargar</option>');
            }
        });
        
        // Cargar personal responsable
        $.ajax({
            url: 'Controlador/ajax_get_personalResponsable.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-personalResponsable');
                    select.empty();
                    select.append('<option value="">Todos</option>');
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'bootstrap4',
                        placeholder: 'Seleccionar responsable...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            },
            error: function() {
                $('#filtro-personalResponsable').html('<option value="">Error al cargar</option>');
            }
        });
    }
    
    // Inicializar DataTable
    function inicializarDataTable() {
        if ($.fn.DataTable.isDataTable('#dataTablePersonalExterno')) {
            if (dataTable) {
                dataTable.destroy();
            }
            $('#dataTablePersonalExterno').removeClass('dataTable no-footer');
        }
        
        dataTable = $('#dataTablePersonalExterno').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "Controlador/Obtener_PersonalExterno.php",
                "type": "POST",
                "data": function(d) {
                    d.numeroIdentificacion = $('#filtro-numeroIdentificacion').val();
                    d.nombre = $('#filtro-nombre').val();
                    d.cargo = $('#filtro-cargo').val();
                    d.areaVisita = $('#filtro-areaVisita').val();
                    d.personalResponsable = $('#filtro-personalResponsable').val();
                    d.estatus = $('#filtro-estatus').val();
                },
                "dataType": "json",
                "error": function(xhr, error, thrown) {
                    console.error("Error al cargar datos:", error);
                    showNotification('Error al cargar los datos. Por favor, recarga la página.', 'error');
                    $('#loading').hide();
                }
            },
            "columns": [
                { 
                    "data": "NumeroIdentificacion",
                    "className": "text-center"
                },
                 { 
                    "data": "Foto",
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center"
                },
                { "data": "Nombre" },
                { "data": "ApPaterno" },
                { "data": "ApMaterno" },
                { "data": "EmpresaProcedencia" },
                { "data": "Cargo" },
                { "data": "AreaVisita" },
                { "data": "PersonalResponsable" },
                { "data": "Email" },
                { "data": "Telefono" },
                { "data": "VigenciaAcceso" },
                { 
                    "data": "Status",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        var badgeClass = '';
                        var statusText = '';
                        
                        if (data == '1' || data == 'Activo') {
                            badgeClass = 'badge-success';
                            statusText = 'Activo';
                        } else if (data == '0' || data == 'Inactivo') {
                            badgeClass = 'badge-danger';
                            statusText = 'Inactivo';
                        } else if (data == '2' || data == 'Baja') {
                            badgeClass = 'badge-secondary';
                            statusText = 'Baja';
                        } else {
                            badgeClass = 'badge-info';
                            statusText = data || 'Desconocido';
                        }
                        
                        return '<span class="badge ' + badgeClass + '">' + statusText + '</span>';
                    }
                },
                { 
                    "data": "Acceso",
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        if (row.IdPersonalExterno) {
                            return '<a href="Controlador/GenerarDoc.php?id=' + row.IdPersonalExterno + '" ' +
                                   'class="btn btn-sm btn-info" title="Generar Documento de Acceso" target="_blank">' +
                                   '<i class="fas fa-file-alt"></i></a>';
                        }
                        return '';
                    }
                },
                { 
                    "data": "Acciones",
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        var acciones = '';
                        
                        if (row.IdPersonalExterno) {
                            acciones += '<button class="btn btn-sm btn-warning btn-editar mr-1" ' +
                                       'data-id="' + row.IdPersonalExterno + '" title="Editar">' +
                                       '<i class="fas fa-edit"></i></button>';
                            
                            acciones += '<button class="btn btn-sm btn-secondary btn-cambiar-estatus mr-1" ' +
                                       'data-id="' + row.IdPersonalExterno + '" title="Cambiar Estatus">' +
                                       '<i class="fas fa-exchange-alt"></i></button>';
                            
                            acciones += '<button class="btn btn-sm btn-danger btn-eliminar" ' +
                                       'data-id="' + row.IdPersonalExterno + '" title="Eliminar">' +
                                       '<i class="fas fa-trash"></i></button>';
                        }
                        
                        return acciones;
                    }
                }
            ],
            "language": {
                "processing": "<div class='spinner-border text-primary' role='status'><span class='sr-only'>Cargando...</span></div>",
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron registros",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "loadingRecords": "Cargando...",
                "emptyTable": "No hay datos disponibles en la tabla"
            },
            "responsive": true,
            "autoWidth": false,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "initComplete": function(settings, json) {
                initEvents();
                console.log('DataTable inicializado correctamente');
                $('#loading').hide();
                showNotification('Catálogo cargado correctamente', 'success');
            },
            "drawCallback": function(settings) {
                initEvents();
                $('#loading').hide();
                
                // Guardar datos actuales
                currentData = dataTable.data().toArray();
            },
            "preDrawCallback": function(settings) {
                $('#loading').show();
            }
        });
    }
    
    function exportarExcel() {
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        showNotification('Generando archivo Excel...', 'info');
        
        $.fn.dataTable.ext.buttons.excelHtml5.action.call(
            { 
                node: $('#btn-export-excel')[0],
                conf: {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    title: 'Catalogo_Personal_Externo_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        modifier: {
                            page: 'all'
                        }
                    },
                    filename: 'catalogo_personal_externo_' + new Date().toISOString().split('T')[0],
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        
                        $('row:first c', sheet).attr('s', '2');
                        $('row:eq(1) c', sheet).each(function() {
                            if ($(this).is(':first-child')) {
                                $(this).attr('s', '2');
                                $(this).text('Fecha: ' + new Date().toLocaleDateString());
                            }
                        });
                    }
                },
                dt: dataTable
            }
        );
        
        setTimeout(() => {
            showNotification('Archivo Excel generado correctamente', 'success');
        }, 1000);
    }
    
    function exportarPDF() {
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        showNotification('Generando archivo PDF...', 'info');
        
        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(
            { 
                node: $('#btn-export-pdf')[0],
                conf: {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    title: 'Catálogo de Personal Externo',
                    message: 'Fecha: ' + new Date().toLocaleDateString(),
                    exportOptions: {
                        columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        modifier: {
                            page: 'all'
                        }
                    },
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.styles.tableHeader.fillColor = '#d94f00';
                        doc.styles.tableHeader.color = '#ffffff';
                        doc.content[1].table.widths = 
                            Array(doc.content[1].table.body[0].length).fill('*');
                        
                        // Agregar encabezado
                        doc.content.splice(0, 0, {
                            text: 'Catálogo de Personal Externo',
                            fontSize: 16,
                            alignment: 'center',
                            color: '#d94f00',
                            margin: [0, 0, 0, 10]
                        });
                        
                        // Agregar información de filtros
                        var filtros = 'Filtros aplicados: ';
                        var filtrosArray = [];
                        
                        if ($('#filtro-numeroIdentificacion').val()) filtrosArray.push('No. Identificación: ' + $('#filtro-numeroIdentificacion').val());
                        if ($('#filtro-nombre').val()) filtrosArray.push('Nombre: ' + $('#filtro-nombre').val());
                       if ($('#filtro-cargo').val()) filtrosArray.push('Cargo: ' + $('#filtro-cargo option:selected').text());
                        if ($('#filtro-areaVisita').val()) filtrosArray.push('Área: ' + $('#filtro-areaVisita option:selected').text());
                        if ($('#filtro-personalResponsable').val()) filtrosArray.push('Responsable: ' + $('#filtro-personalResponsable option:selected').text());
                        if ($('#filtro-estatus').val()) filtrosArray.push('Estatus: ' + $('#filtro-estatus option:selected').text());
                        
                        if (filtrosArray.length > 0) {
                            filtros += filtrosArray.join(' | ');
                        } else {
                            filtros += 'Ninguno (Todos los registros)';
                        }
                        
                        doc.content.splice(1, 0, {
                            text: filtros,
                            fontSize: 9,
                            alignment: 'left',
                            margin: [0, 0, 0, 10]
                        });
                    }
                },
                dt: dataTable
            }
        );
        
        setTimeout(() => {
            showNotification('Archivo PDF generado correctamente', 'success');
        }, 1000);
    }
    
    // Función para imprimir
    function imprimirTabla() {
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para imprimir', 'warning');
            return;
        }
        
        showNotification('Preparando impresión...', 'info');
        
        // Crear DataTable temporal para imprimir
        $.fn.dataTable.ext.buttons.print.action.call(
            { 
                node: $('#btn-print')[0],
                conf: {
                    extend: 'print',
                    text: 'Imprimir',
                    title: 'Catálogo de Personal Externo',
                    exportOptions: {
                        columns: [0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        modifier: {
                            page: 'all'
                        }
                    },
                    customize: function(win) {
                        $(win.document.body).find('h1').css({
                            'color': '#d94f00',
                            'text-align': 'center',
                            'margin-bottom': '20px'
                        });
                        
                        // Agregar encabezado con información
                        $(win.document.body).prepend(
                            '<div style="text-align: center; margin-bottom: 20px;">' +
                            '<h1 style="color: #d94f00;">Catálogo de Personal Externo</h1>' +
                            '<p><strong>Fecha:</strong> ' + new Date().toLocaleDateString() + '</p>' +
                            '<p><strong>Hora:</strong> ' + new Date().toLocaleTimeString() + '</p>' +
                            '</div>'
                        );
                        
                        // Agregar información de filtros
                        var filtros = '<div style="margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">' +
                            '<strong>Filtros aplicados:</strong> ';
                        var filtrosArray = [];
                        
                        if ($('#filtro-numeroIdentificacion').val()) filtrosArray.push('No. Identificación: ' + $('#filtro-numeroIdentificacion').val());
                        if ($('#filtro-nombre').val()) filtrosArray.push('Nombre: ' + $('#filtro-nombre').val());
                        if ($('#filtro-cargo').val()) filtrosArray.push('Cargo: ' + $('#filtro-cargo option:selected').text());
                        if ($('#filtro-areaVisita').val()) filtrosArray.push('Área: ' + $('#filtro-areaVisita option:selected').text());
                        if ($('#filtro-personalResponsable').val()) filtrosArray.push('Responsable: ' + $('#filtro-personalResponsable option:selected').text());
                        if ($('#filtro-estatus').val()) filtrosArray.push('Estatus: ' + $('#filtro-estatus option:selected').text());
                        
                        if (filtrosArray.length > 0) {
                            filtros += filtrosArray.join(' | ');
                        } else {
                            filtros += 'Ninguno (Todos los registros)';
                        }
                        
                        filtros += '</div>';
                        
                        $(win.document.body).find('div').first().after(filtros);
                        
                        $(win.document.body).find('table').addClass('table table-bordered table-striped');
                        $(win.document.body).find('thead th').css({
                            'background-color': '#d94f00',
                            'color': 'white',
                            'padding': '8px',
                            'text-align': 'center'
                        });
                        $(win.document.body).find('td').css({
                            'padding': '6px',
                            'text-align': 'center'
                        });
                        
                        // Agregar pie de página
                        $(win.document.body).append(
                            '<div style="margin-top: 20px; text-align: center; font-size: 10px; color: #666;">' +
                            'Generado el: ' + new Date().toLocaleString() + ' | Total de registros: ' + dataTable.rows().count() +
                            '</div>'
                        );
                    }
                },
                dt: dataTable
            }
        );
    }
    
    // Event Listeners para botones de exportación
    $('#btn-export-excel').click(function(e) {
        e.preventDefault();
        exportarExcel();
    });
    
    $('#btn-export-pdf').click(function(e) {
        e.preventDefault();
        exportarPDF();
    });
    
    $('#btn-print').click(function(e) {
        e.preventDefault();
        imprimirTabla();
    });
    
    $('#btn-refresh').click(function(e) {
        e.preventDefault();
        dataTable.ajax.reload();
        showNotification('Tabla recargada correctamente', 'success');
    });
    
    // Event Listeners para filtros
    $('#btn-aplicar-filtros').click(function() {
        dataTable.ajax.reload();
    });
    
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-numeroIdentificacion').val('');
        $('#filtro-nombre').val('');
        $('#filtro-cargo').val('').trigger('change');
        $('#filtro-areaVisita').val('').trigger('change');
        $('#filtro-personalResponsable').val('').trigger('change');
        $('#filtro-estatus').val('');
        
        dataTable.ajax.reload();
        showNotification('Filtros limpiados', 'info');
    });
    
    // Event Listeners para búsqueda al presionar Enter
    $('#filtro-numeroIdentificacion, #filtro-nombre').keypress(function(e) {
        if (e.which == 13) {
            dataTable.ajax.reload();
        }
    });
    
    // Funciones para modal de fotos
    function initEvents() {
        $('.thumbnail-image').off('click').on('click', function(e) {
            e.preventDefault();
            
            var fullImage = $(this).data('full-image');
            var employeeName = $(this).data('employee-name');
            
            if (fullImage) {
                $('#modalPhoto').attr('src', fullImage);
                $('#modalEmployeeName').text(employeeName);
                $('#photoModal').modal('show');
            }
        });
    }
    
    $(document).on('click', '.btn-nuevo', function() {
        loadModal('Modales/Nuevo.php', '#NuevoPersonalExterno', 'nuevo');
    });
    
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        loadModal('Modales/Modificar.php?IdPersonalExterno=' + id, '#ModificarPersonalExterno', 'editar');
    });
    
    $(document).on('click', '.btn-cambiar-estatus', function() {
        var id = $(this).data('id');
        loadModal('Modales/CambiarEstatus.php?IdPersonalExterno=' + id, '#CambiarEstatusPersonalExterno', 'cambiar_estatus');
    });
    
    $(document).on('click', '.btn-eliminar', function() {
        var id = $(this).data('id');
        var nombre = $(this).closest('tr').find('td:nth-child(3)').text();
        var apPaterno = $(this).closest('tr').find('td:nth-child(4)').text();
        var apMaterno = $(this).closest('tr').find('td:nth-child(5)').text();
        var nombreCompleto = nombre + ' ' + apPaterno + ' ' + apMaterno;
        
        if (confirm('¿Está seguro de eliminar al personal externo: ' + nombreCompleto + '?\nEsta acción no se puede deshacer.')) {
            $.ajax({
                url: 'Controlador/Eliminar_PersonalExterno.php',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        dataTable.ajax.reload(null, false);
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('Error al eliminar el registro: ' + error, 'error');
                    console.error('Error:', xhr.responseText);
                }
            });
        }
    });
    
    function loadModal(url, modalId, actionType) {
        $('#modal-container').load(url, function(response, status, xhr) {
            if (status === "error") {
                showNotification('Error al cargar el formulario', 'danger');
                return;
            }
            $(modalId).modal('show');
            
            // Inicializar Select2 si está disponible
            if ($.fn.select2) {
                $(modalId).find('select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'bootstrap4',
                            placeholder: 'Seleccione una opción',
                            allowClear: true
                        });
                    }
                });
            }
        });
    }
    
    $(document).on('submit', '#formNuevoPersonalExterno, #formModificarPersonalExterno, #formCambiarEstatusPersonalExterno', function(e) {
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
                    dataTable.ajax.reload(null, false);
                    
                    if (form.attr('id') === 'formNuevoPersonalExterno') {
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
                console.error('Error AJAX:', xhr.responseText);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        if ($(this).attr('id') !== 'photoModal') {
            $('#modal-container').empty();
        }
    });
    
    // Inicializar el sistema
    cargarDatosFiltros();
    inicializarDataTable();
});
</script>

<style>
/* Estilos específicos para esta página */
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

/* Estilos para botones de exportación */
.btn-outline-primary {
    border-color: #d94f00;
    color: #d94f00;
}

.btn-outline-primary:hover {
    background-color: #d94f00;
    border-color: #d94f00;
    color: white;
}

/* Estilos para los botones de exportación en filtros */
.btn-group.mt-4 {
    display: flex;
    justify-content: flex-end;
    gap: 5px;
}

.btn-group.mt-4 .btn {
    padding: 8px 12px;
    font-size: 14px;
}

/* Estilos para la tabla */
.table th {
    background-color: #d94f00;
    color: white;
    border-color: #b53d00;
    text-align: center;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(217, 79, 0, 0.05);
}

.table-hover tbody tr:hover {
    background-color: rgba(217, 79, 0, 0.1);
}

/* Estilos para DataTables */
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

/* Estilos para botones dentro de la tabla */
.table .btn-sm {
    padding: 4px 8px;
    font-size: 12px;
    margin: 2px;
}

.table .btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.table .btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.table .btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
}

.table .btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.table .btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.table .btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.table .btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.table .btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.table .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.table .btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

/* Responsive */
@media (max-width: 768px) {
    .badge { font-size: 0.75em !important; }
    
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
        margin-bottom: 10px;
    }
    
    .btn-group.mt-4 {
        width: 100%;
        justify-content: center;
        margin-top: 10px;
    }
    
    .btn-group.mt-4 .btn {
        flex: 1;
        margin-bottom: 5px;
    }
    
    .dataTables_length,
    .dataTables_filter {
        text-align: center;
    }
    
    .table .btn-sm {
        padding: 2px 4px;
        font-size: 10px;
        margin: 1px;
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
    .btn-nuevo,
    .thumbnail-image,
    .view-photo-link,
    .btn-editar,
    .btn-cambiar-estatus,
    .btn-eliminar,
    .btn-info {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-body {
        padding: 0 !important;
    }
    
    .table th {
        background-color: #f8f9fa !important;
        color: #000 !important;
        border: 1px solid #dee2e6 !important;
    }
    
    body {
        margin: 0.5cm !important;
    }
    
    .table .btn {
        display: none !important;
    }
    
    .table td:last-child,
    .table th:last-child {
        display: none !important;
    }
    
    a[href*="GenerarDoc"] {
        text-decoration: none !important;
        color: #000 !important;
    }
}
</style>

<?php
include '../templates/footer.php';
?>