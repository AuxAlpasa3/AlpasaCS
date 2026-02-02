<?php
include_once "../templates/head.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                   <h1 class="m-0 fw-bold" style="color: #d94f00">Registro de Movimientos</h1>
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
                                <label class="form-label">Fecha:</label>
                                <select id="filtro-fecha" class="form-control form-control-lg" style="width: 100%;">
                                    <option value="hoy">Hoy</option>
                                    <option value="ayer">Ayer</option>
                                    <option value="semana">Esta semana</option>
                                    <option value="mes">Este mes</option>
                                    <option value="personalizado">Personalizado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2" id="rango-fechas-container" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Desde:</label>
                                <input type="date" id="fecha-inicio" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-2" id="rango-fechas-hasta-container" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Hasta:</label>
                                <input type="date" id="fecha-fin" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Personal:</label>
                                <select id="filtro-personal" class="form-control form-control-lg select2-personal" style="width: 100%;">
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Ubicación:</label>
                                <select id="filtro-ubicacion" class="form-control form-control-lg select2-ubicacion" style="width: 100%;">
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Tipo:</label>
                                <select id="filtro-tipo" class="form-control form-control-lg select2-tipo" style="width: 100%;">
                                    <option value="">Todos</option>
                                    <option value="entrada">Entradas</option>
                                    <option value="salida">Salidas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">ID Personal:</label>
                                <input type="text" id="filtro-id-personal" class="form-control form-control-lg" placeholder="Buscar por ID...">
                            </div>
                        </div>
                        
                        <div class="col-md-9">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-exchange-alt mr-2"></i>Lista de Movimientos
                    </h3>
                </div>
                <div class="card-body">
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-primary">Cargando movimientos...</p>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTableMovimientos">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>ID Personal</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Ubicación</th>
                                    <th>Acción</th>
                                    <th>Detalles</th>
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
        // Configurar Select2 para fecha
        $('#filtro-fecha').select2({
            theme: 'custom-theme',
            width: '100%',
            dropdownCssClass: 'select2-dropdown-enhanced',
            selectionCssClass: 'select2-selection-enhanced',
            language: 'es'
        });
        
        // Configurar Select2 para tipo
        $('#filtro-tipo').select2({
            theme: 'custom-theme',
            placeholder: 'Todos los tipos',
            allowClear: true,
            width: '100%',
            dropdownCssClass: 'select2-dropdown-enhanced',
            selectionCssClass: 'select2-selection-enhanced',
            language: 'es'
        });
        
        // Cargar personal
        $.ajax({
            url: 'Controlador/ajax_get_personal.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-personal');
                    select.empty();
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            var texto = item.nombre;
                            if (item.codigo) {
                                texto += ' (ID: ' + item.codigo + ')';
                            }
                            select.append('<option value="' + item.id + '">' + texto + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'custom-theme',
                        placeholder: 'Todo el personal',
                        allowClear: true,
                        width: '100%',
                        dropdownCssClass: 'select2-dropdown-enhanced',
                        selectionCssClass: 'select2-selection-enhanced',
                        language: 'es',
                        closeOnSelect: false
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando personal:', error);
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
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'custom-theme',
                        placeholder: 'Todas las ubicaciones',
                        allowClear: true,
                        width: '100%',
                        dropdownCssClass: 'select2-dropdown-enhanced',
                        selectionCssClass: 'select2-selection-enhanced',
                        language: 'es',
                        closeOnSelect: false
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando ubicaciones:', error);
            }
        });
    }
    
    function inicializarDataTable() {
        if ($.fn.DataTable.isDataTable('#dataTableMovimientos')) {
            if (dataTable) {
                dataTable.destroy();
            }
            $('#dataTableMovimientos tbody').empty();
        }
        
        dataTable = $('#dataTableMovimientos').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "Controlador/Obtener_Movimientos.php",
                type: "POST",
                data: function(d) {
                    d.filtro_fecha = $('#filtro-fecha').val();
                    d.fecha_inicio = $('#fecha-inicio').val();
                    d.fecha_fin = $('#fecha-fin').val();
                    d.id_personal = $('#filtro-personal').val();
                    d.id_ubicacion = $('#filtro-ubicacion').val();
                    d.tipo_movimiento = $('#filtro-tipo').val();
                    d.id_personal_especifico = $('#filtro-id-personal').val();
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
                    data: "Fecha",
                    className: "text-center",
                    orderable: true,
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString('es-MX') : 'N/A';
                    }
                },
                { 
                    data: "Hora",
                    className: "text-center",
                    orderable: true
                },
                { 
                    data: "IDPersonal",
                    className: "text-center",
                    orderable: true
                },
                { 
                    data: "Nombre",
                    orderable: true
                },
                { 
                    data: "Tipo",
                    className: "text-center",
                    orderable: true,
                    render: function(data, type, row) {
                        var badgeClass = data === 'entrada' ? 'badge-success' : 'badge-danger';
                        var tipoTexto = data === 'entrada' ? 'Entrada' : 'Salida';
                        return `<span class="badge ${badgeClass}">${tipoTexto}</span>`;
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
                    data: "Accion",
                    className: "text-center",
                    orderable: true,
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
                        var idMovimiento = row.IdMovimiento || '';
                        var tipo = row.Tipo || '';
                        var categoria = row.Categoria || 'Personal';
                        
                        if (!idMovimiento) return '';
                        
                        return `
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-info btn-ver-detalles" 
                                        data-id="${idMovimiento}"
                                        data-tipo="${tipo}"
                                        data-categoria="${categoria}"
                                        title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
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
            pageLength: 25,
            searching: false,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn-outline-primary',
                    title: 'Movimientos_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn-outline-primary',
                    title: 'Movimientos_' + new Date().toISOString().split('T')[0],
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(doc) {
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 9;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.styles.tableHeader.fillColor = '#d94f00';
                        doc.styles.tableHeader.color = '#ffffff';
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    className: 'btn-outline-primary',
                    title: 'Movimientos_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(win) {
                        $(win.document.body).find('h1').css({
                            'color': '#d94f00',
                            'text-align': 'center',
                            'margin-bottom': '20px'
                        });
                        
                        $(win.document.body).find('table').addClass('table table-bordered table-striped');
                        $(win.document.body).find('thead th').css({
                            'background-color': '#d94f00',
                            'color': 'white',
                            'padding': '8px',
                            'text-align': 'center'
                        });
                    }
                }
            ],
            initComplete: function(settings, json) {
                $('#loading').hide();
                initEvents();
                showNotification('Movimientos cargados correctamente', 'success');
            },
            drawCallback: function(settings) {
                initEvents();
            }
        });
    }
    
    function cargarDetalleMovimiento(idMov, tipo, categoria) {
        $.ajax({
            url: 'Modales/Detalle_Movimiento.php',
            type: 'GET',
            data: {
                idMov: idMov,
                tipo: tipo,
                categoria: categoria
            },
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(response) {
                $('#loading').hide();
                $('#modal-container').html(response);
                $('#DetalleMovimiento').modal('show');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el detalle del movimiento', 'danger');
            }
        });
    }
    
    function initEvents() {
        $(document).off('click', '.btn-ver-detalles').on('click', '.btn-ver-detalles', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var idMov = $(this).data('id');
            var tipo = $(this).data('tipo');
            var categoria = $(this).data('categoria');
            
            if (idMov) {
                cargarDetalleMovimiento(idMov, tipo, categoria);
            }
        });
    }
    
    $('#filtro-fecha').change(function() {
        if ($(this).val() === 'personalizado') {
            $('#rango-fechas-container').show();
            $('#rango-fechas-hasta-container').show();
        } else {
            $('#rango-fechas-container').hide();
            $('#rango-fechas-hasta-container').hide();
        }
    });
    
    $('#btn-aplicar-filtros').click(function() {
        if (dataTable) {
            dataTable.ajax.reload();
        }
    });
    
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-fecha').val('hoy').trigger('change');
        $('#fecha-inicio').val('<?php echo date('Y-m-d'); ?>');
        $('#fecha-fin').val('<?php echo date('Y-m-d'); ?>');
        $('#filtro-personal').val(null).trigger('change');
        $('#filtro-ubicacion').val(null).trigger('change');
        $('#filtro-tipo').val('').trigger('change');
        $('#filtro-id-personal').val('');
        
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
        
        dataTable.button('.buttons-excel').trigger();
        showNotification('Generando archivo Excel...', 'info');
    });
    
    $('#btn-export-pdf').click(function(e) {
        e.preventDefault();
        
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        dataTable.button('.buttons-pdf').trigger();
        showNotification('Generando archivo PDF...', 'info');
    });
    
    $('#btn-print').click(function(e) {
        e.preventDefault();
        
        if (!dataTable || dataTable.rows().count() === 0) {
            showNotification('No hay datos para imprimir', 'warning');
            return;
        }
        
        dataTable.button('.buttons-print').trigger();
        showNotification('Preparando impresión...', 'info');
    });
    
    $('#btn-refresh').click(function(e) {
        e.preventDefault();
        if (dataTable) {
            dataTable.ajax.reload(null, false);
            showNotification('Tabla recargada correctamente', 'success');
        }
    });
    
    $('#filtro-id-personal').keypress(function(e) {
        if (e.which == 13) {
            if (dataTable) {
                dataTable.ajax.reload();
            }
        }
    });
    
    cargarDatosFiltros();
    inicializarDataTable();
    
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        $('#modal-container').empty();
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

/* Estilos mejorados para Select2 */
.select2-container--custom-theme {
    width: 100% !important;
}

.select2-container--custom-theme .select2-selection--single {
    min-height: 48px;
    border: 2px solid #e9ecef !important;
    border-radius: 8px !important;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.select2-container--custom-theme .select2-selection--single .select2-selection__rendered {
    line-height: 44px;
    padding-left: 12px;
    color: #495057;
    font-size: 14px;
}

.select2-container--custom-theme.select2-container--focus .select2-selection--single {
    border-color: #d94f00 !important;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(217, 79, 0, 0.1);
}

.select2-dropdown.select2-dropdown-enhanced {
    border: 2px solid #d94f00;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-top: 4px;
    z-index: 1060 !important;
}

.select2-container--custom-theme .select2-selection__arrow {
    height: 46px;
    right: 8px;
    width: 20px;
}

.select2-container--custom-theme .select2-selection__arrow b {
    border-color: #6c757d transparent transparent transparent;
    border-width: 6px 6px 0 6px;
}

.select2-container--custom-theme.select2-container--open .select2-selection__arrow b {
    border-color: transparent transparent #6c757d transparent;
    border-width: 0 6px 6px 6px;
}

.select2-container--custom-theme .select2-results__option {
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.2s;
}

.select2-container--custom-theme .select2-results__option--highlighted[aria-selected] {
    background-color: #d94f00;
    color: white;
}

.select2-container--custom-theme .select2-results__option[aria-selected=true] {
    background-color: rgba(217, 79, 0, 0.1);
    color: #d94f00;
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

#filtrosHeader {
    transition: all 0.3s;
}

#filtrosHeader:hover {
    background-color: #b53d00 !important;
}

#filtrosHeader .toggle-icon {
    transition: transform 0.3s;
}

.table th {
    background-color: #d94f00;
    color: white;
    border-color: #b53d00;
    text-align: center;
    vertical-align: middle;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(217, 79, 0, 0.05);
}

#dataTableMovimientos tbody tr {
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

/* Estilos para etiquetas de formulario */
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
    border: 2px solid #e9ecef;
    transition: all 0.3s;
}

.form-control-lg:focus {
    border-color: #d94f00;
    box-shadow: 0 0 0 3px rgba(217, 79, 0, 0.1);
    outline: none;
}

.btn-lg {
    padding: 10px 20px;
    font-size: 15px;
    border-radius: 8px;
    font-weight: 600;
}

/* Estilos responsivos */
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
    
    .form-control-lg {
        min-height: 38px;
        padding: 6px 10px;
        font-size: 13px;
    }
    
    .btn-lg {
        padding: 8px 16px;
        font-size: 14px;
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
    .btn-ver-detalles,
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

@keyframes select2Pulse {
    0% { box-shadow: 0 0 0 0 rgba(217, 79, 0, 0.4); }
    70% { box-shadow: 0 0 0 6px rgba(217, 79, 0, 0); }
    100% { box-shadow: 0 0 0 0 rgba(217, 79, 0, 0); }
}

.select2-container--custom-theme.select2-container--focus .select2-selection--single {
    animation: select2Pulse 1.5s infinite;
}
</style>