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
            <div class="card card-primary">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-exchange-alt mr-2"></i>Filtros de Búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Fecha:</label>
                                <select id="filtro-fecha" class="form-control" style="border-color: #d94f00;">
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
                                <label>Desde:</label>
                                <input type="date" id="fecha-inicio" class="form-control" style="border-color: #d94f00;" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-2" id="rango-fechas-hasta-container" style="display: none;">
                            <div class="form-group">
                                <label>Hasta:</label>
                                <input type="date" id="fecha-fin" class="form-control" style="border-color: #d94f00;" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <!-- Personal con Select2 -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Personal:</label>
                                <select id="filtro-personal" class="form-control select2-personal" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Ubicación con Select2 -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Ubicación:</label>
                                <select id="filtro-ubicacion" class="form-control select2-ubicacion" style="border-color: #d94f00; width: 100%;">
                                    <option value="">Todas</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tipo:</label>
                                <select id="filtro-tipo" class="form-control" style="border-color: #d94f00;">
                                    <option value="">Todos</option>
                                    <option value="entrada">Entradas</option>
                                    <option value="salida">Salidas</option>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ID Personal:</label>
                                <input type="text" id="filtro-id-personal" class="form-control" style="border-color: #d94f00;" placeholder="Buscar por ID...">
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
                            </div>
                        </div>
                    </div>
                    
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-primary">Cargando movimientos...</p>
                    </div>
                    
                    <div id="movimientos-container">
                        <!-- Aquí se cargarán los movimientos -->
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM completamente cargado, inicializando sistema...');
    
    $(document).ready(function() {
        console.log('jQuery listo, versión:', $.fn.jquery);
        
        if (typeof $.fn.select2 === 'undefined') {
            console.error('Select2 no está disponible!');
            alert('Error: Select2 no se cargó correctamente. Recarga la página.');
            return;
        }
        
        console.log('Select2 disponible, versión:', $.fn.select2.version);
        
        // Variables globales
        let dataTable = null;
        let currentData = [];
        
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
        
        // Función para cargar datos iniciales
        function cargarDatosIniciales() {
            console.log('Cargando datos iniciales...');
            
            // Cargar personal
            $.ajax({
                url: 'Controlador/ajax_get_personal.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Respuesta de personal:', data);
                    
                    if (!data.error && Array.isArray(data)) {
                        var select = $('#filtro-personal');
                        select.empty();
                        select.append('<option value="">Todos</option>');
                        
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
                            theme: 'bootstrap-5',
                            language: 'es',
                            placeholder: 'Seleccionar personal...',
                            allowClear: true,
                            width: '100%'
                        });
                        
                        console.log('Select2 personal inicializado con ' + data.length + ' registros');
                    } else if (data.error) {
                        console.error('Error en datos:', data.error);
                        $('#filtro-personal').html('<option value="">Error: ' + data.error + '</option>');
                        showNotification('Error al cargar personal: ' + data.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX al cargar personal:', error);
                    $('#filtro-personal').html('<option value="">Error al cargar</option>');
                    
                    $('#filtro-personal').select2({
                        theme: 'bootstrap-5',
                        language: 'es',
                        placeholder: 'Error al cargar datos',
                        allowClear: true,
                        width: '100%'
                    });
                    showNotification('Error al cargar la lista de personal', 'error');
                }
            });
            
            // Cargar ubicaciones
            $.ajax({
                url: 'Controlador/ajax_get_ubicaciones.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Respuesta de ubicaciones:', data);
                    
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
                            theme: 'bootstrap-5',
                            language: 'es',
                            placeholder: 'Seleccionar ubicación...',
                            allowClear: true,
                            width: '100%'
                        });
                        
                        console.log('Select2 ubicaciones inicializado con ' + data.length + ' registros');
                    } else if (data.error) {
                        console.error('Error en datos:', data.error);
                        $('#filtro-ubicacion').html('<option value="">Error: ' + data.error + '</option>');
                        showNotification('Error al cargar ubicaciones: ' + data.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX al cargar ubicaciones:', error);
                    $('#filtro-ubicacion').html('<option value="">Error al cargar</option>');
                    
                    $('#filtro-ubicacion').select2({
                        theme: 'bootstrap-5',
                        language: 'es',
                        placeholder: 'Error al cargar datos',
                        allowClear: true,
                        width: '100%'
                    });
                    showNotification('Error al cargar la lista de ubicaciones', 'error');
                }
            });
        }
        
        // Función para cargar movimientos
        function cargarMovimientos() {
            console.log('Cargando movimientos con filtros...');
            $('#loading').show();
            $('#movimientos-container').empty();
            
            var params = {
                filtro_fecha: $('#filtro-fecha').val(),
                fecha_inicio: $('#fecha-inicio').val(),
                fecha_fin: $('#fecha-fin').val(),
                id_personal: $('#filtro-personal').val(),
                id_ubicacion: $('#filtro-ubicacion').val(),
                tipo_movimiento: $('#filtro-tipo').val(),
                id_personal_especifico: $('#filtro-id-personal').val()
            };
            
            console.log('Parámetros de filtro:', params);
            
            $.ajax({
                url: 'Controlador/ajax_movimientos.php',
                type: 'GET',
                data: params,
                dataType: 'html',
                success: function(response) {
                    $('#movimientos-container').html(response);
                    inicializarDataTable();
                    $('#loading').hide();
                    showNotification('Movimientos cargados correctamente', 'success');
                },
                error: function(xhr, status, error) {
                    $('#movimientos-container').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                    $('#loading').hide();
                    console.error('Error al cargar movimientos:', error);
                    showNotification('Error al cargar movimientos: ' + error, 'error');
                }
            });
        }
        
        function inicializarDataTable() {
            console.log('Inicializando DataTable SIN botones...');
            
            var $table = $('#dataTableMovimientos');
            if (!$table.length) {
                console.warn('La tabla no existe');
                $('#loading').hide();
                showNotification('No hay datos para mostrar con los filtros aplicados', 'warning');
                return;
            }
            
            var hasData = false;
            $table.find('tbody tr').each(function() {
                var tdCount = $(this).find('td').length;
                if (tdCount > 1) {
                    hasData = true;
                }
            });
            
            console.log('Tabla tiene datos?', hasData);
            
            if ($.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                if (dataTable) {
                    dataTable.destroy();
                }
                $table.removeClass('dataTable no-footer');
                $table.find('thead').removeAttr('style');
            }
            
            setTimeout(function() {
                try {
                    if (hasData) {
                        dataTable = $('#dataTableMovimientos').DataTable({
                            "language": {
                                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json",
                                "emptyTable": "No hay datos disponibles en la tabla",
                                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                                "lengthMenu": "Mostrar _MENU_ registros",
                                "loadingRecords": "Cargando...",
                                "processing": "Procesando...",
                                "search": "Buscar:",
                                "zeroRecords": "No se encontraron registros coincidentes",
                                "paginate": {
                                    "first": "Primero",
                                    "last": "Último",
                                    "next": "Siguiente",
                                    "previous": "Anterior"
                                }
                            },
                            "responsive": true,
                            "autoWidth": false,
                            "order": [[0, "desc"]],
                            "pageLength": 25,
                            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
                            "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                                   "<'row'<'col-sm-12'tr>>" +
                                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                            "initComplete": function(settings, json) {
                                console.log('DataTable inicializado correctamente SIN botones');
                                $('#loading').hide();
                                
                                currentData = dataTable.data().toArray();
                                
                                showNotification('Tabla cargada correctamente. Usa los botones de exportación en la parte superior.', 'success');
                            },
                            "drawCallback": function(settings) {
                                $('#loading').hide();
                            },
                            "columns": [
                                { "width": "10%" },
                                { "width": "20%" },
                                { "width": "10%" },
                                { "width": "10%" },
                                { "width": "15%" },
                                { "width": "10%" },
                                { "width": "15%" },
                                { "width": "10%" }
                            ]
                        });
                        
                    } else {
                        console.log('Tabla sin datos, mostrando mensaje estático');
                        $('#loading').hide();
                        
                        $table.addClass('table table-bordered table-striped');
                        
                        var $messageRow = $table.find('tbody tr');
                        if ($messageRow.length) {
                            $messageRow.find('td').html(
                                '<div class="text-center py-4">' +
                                '<i class="fas fa-database fa-3x text-muted mb-3"></i>' +
                                '<h5 class="text-muted">No se encontraron movimientos</h5>' +
                                '<p class="small text-muted">Intenta con otros filtros</p>' +
                                '</div>'
                            );
                        }
                        showNotification('No se encontraron movimientos con los filtros aplicados', 'warning');
                    }
                } catch (error) {
                    console.error('Error al inicializar DataTable:', error);
                    $('#loading').hide();
                    $table.addClass('table table-bordered table-striped');
                    showNotification('Error al inicializar la tabla de datos: ' + error.message, 'error');
                }
            }, 100);
        }
        
        function exportarExcelDataTable() {
            if (!dataTable || !$.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                showNotification('La tabla no está inicializada', 'warning');
                return;
            }
            
            if (dataTable.rows().count() === 0) {
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
                        title: 'Movimientos_' + new Date().toISOString().split('T')[0],
                        exportOptions: {
                            columns: ':visible',
                            modifier: {
                                page: 'all'
                            }
                        },
                        filename: 'movimientos_' + new Date().toISOString().split('T')[0]
                    },
                    dt: dataTable
                }
            );
            
            setTimeout(() => {
                showNotification('Archivo Excel generado correctamente', 'success');
            }, 1000);
        }
        
        // Función para exportar a PDF usando DataTables pero de forma manual
        function exportarPDFDataTable() {
            if (!dataTable || !$.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                showNotification('La tabla no está inicializada', 'warning');
                return;
            }
            
            if (dataTable.rows().count() === 0) {
                showNotification('No hay datos para exportar', 'warning');
                return;
            }
            
            showNotification('Generando archivo PDF...', 'info');
            
            // Crear un DataTable temporal solo para exportar
            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(
                { 
                    node: $('#btn-export-pdf')[0],
                    conf: {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        title: 'Movimientos_' + new Date().toISOString().split('T')[0],
                        exportOptions: {
                            columns: ':visible',
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
                            
                            // Agregar fecha al documento
                            doc.content.splice(1, 0, {
                                text: 'Fecha: ' + new Date().toLocaleDateString() + 
                                      ' • Hora: ' + new Date().toLocaleTimeString(),
                                fontSize: 10,
                                alignment: 'right',
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
        
        // Función para imprimir usando DataTables pero de forma manual
        function imprimirDataTable() {
            if (!dataTable || !$.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                showNotification('La tabla no está inicializada', 'warning');
                return;
            }
            
            if (dataTable.rows().count() === 0) {
                showNotification('No hay datos para imprimir', 'warning');
                return;
            }
            
            showNotification('Preparando impresión...', 'info');
            
            // Crear un DataTable temporal solo para imprimir
            $.fn.dataTable.ext.buttons.print.action.call(
                { 
                    node: $('#btn-print')[0],
                    conf: {
                        extend: 'print',
                        text: 'Imprimir',
                        title: 'Movimientos_' + new Date().toISOString().split('T')[0],
                        exportOptions: {
                            columns: ':visible',
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
                            
                            // Agregar encabezado con fecha
                            $(win.document.body).prepend(
                                '<div style="text-align: center; margin-bottom: 20px;">' +
                                '<h1 style="color: #d94f00;">Reporte de Movimientos</h1>' +
                                '<p><strong>Fecha:</strong> ' + new Date().toLocaleDateString() + '</p>' +
                                '<p><strong>Hora:</strong> ' + new Date().toLocaleTimeString() + '</p>' +
                                '</div>'
                            );
                            
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
                                'Generado el: ' + new Date().toLocaleString() +
                                '</div>'
                            );
                        }
                    },
                    dt: dataTable
                }
            );
        }
        
        // Event Listeners para botones de exportación en los filtros
        $('#btn-export-excel').click(function(e) {
            e.preventDefault();
            
            if (dataTable && $.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                // Usar la función de exportación de DataTable
                exportarExcelDataTable();
            } else if (currentData && currentData.length > 0) {
                // Función de respaldo si no hay DataTable
                exportarExcelManual();
            } else {
                showNotification('No hay datos para exportar', 'warning');
            }
        });
        
        $('#btn-export-pdf').click(function(e) {
            e.preventDefault();
            
            if (dataTable && $.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                // Usar la función de exportación de DataTable
                exportarPDFDataTable();
            } else if (currentData && currentData.length > 0) {
                // Función de respaldo si no hay DataTable
                exportarPDFManual();
            } else {
                showNotification('No hay datos para exportar', 'warning');
            }
        });
        
        $('#btn-print').click(function(e) {
            e.preventDefault();
            
            if (dataTable && $.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                // Usar la función de impresión de DataTable
                imprimirDataTable();
            } else if (currentData && currentData.length > 0) {
                // Función de respaldo si no hay DataTable
                imprimirManual();
            } else {
                showNotification('No hay datos para imprimir', 'warning');
            }
        });
        
        // Funciones de respaldo manual (si no hay DataTable)
        function exportarExcelManual() {
            const table = $('#dataTableMovimientos');
            if (!table.length || table.find('tbody tr').length === 0) {
                showNotification('No hay datos para exportar', 'warning');
                return;
            }
            
            showNotification('Generando archivo Excel...', 'info');
            
            // Crear una tabla HTML para exportar
            const tableHtml = table.clone();
            tableHtml.find('th:last, td:last').remove(); // Remover columna de acciones si existe
            
            const htmlContent = `
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        table { border-collapse: collapse; width: 100%; }
                        th { background-color: #d94f00; color: white; font-weight: bold; padding: 8px; border: 1px solid #ddd; }
                        td { padding: 6px; border: 1px solid #ddd; }
                    </style>
                </head>
                <body>
                    <h2>Reporte de Movimientos</h2>
                    <p><strong>Fecha:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Hora:</strong> ${new Date().toLocaleTimeString()}</p>
                    ${tableHtml[0].outerHTML}
                </body>
                </html>
            `;
            
            // Descargar como archivo
            const blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'movimientos_' + new Date().toISOString().split('T')[0] + '.xls';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            
            showNotification('Archivo Excel generado correctamente', 'success');
        }
        
        function exportarPDFManual() {
            const table = $('#dataTableMovimientos');
            if (!table.length || table.find('tbody tr').length === 0) {
                showNotification('No hay datos para exportar', 'warning');
                return;
            }
            
            showNotification('Generando archivo PDF...', 'info');
            
            // Abrir ventana para imprimir/guardar como PDF
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Movimientos</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #d94f00; text-align: center; }
                        .header-info { text-align: center; margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th { background-color: #d94f00; color: white; padding: 10px; border: 1px solid #ddd; text-align: center; }
                        td { padding: 8px; border: 1px solid #ddd; }
                        @media print {
                            @page { margin: 0.5cm; }
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    <h1>Reporte de Movimientos</h1>
                    <div class="header-info">
                        <p><strong>Fecha:</strong> ${new Date().toLocaleDateString()}</p>
                        <p><strong>Hora:</strong> ${new Date().toLocaleTimeString()}</p>
                    </div>
                    ${table[0].outerHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            
            showNotification('Archivo PDF listo para imprimir o guardar', 'success');
        }
        
        function imprimirManual() {
            const table = $('#dataTableMovimientos');
            if (!table.length || table.find('tbody tr').length === 0) {
                showNotification('No hay datos para imprimir', 'warning');
                return;
            }
            
            // Abrir ventana para imprimir
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Movimientos</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        h1 { color: #d94f00; text-align: center; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th { background-color: #d94f00; color: white; padding: 10px; text-align: left; }
                        td { padding: 8px; border-bottom: 1px solid #ddd; }
                    </style>
                </head>
                <body>
                    <h1>Reporte de Movimientos</h1>
                    <p><strong>Fecha:</strong> ${new Date().toLocaleDateString()}</p>
                    <p><strong>Hora:</strong> ${new Date().toLocaleTimeString()}</p>
                    ${table[0].outerHTML}
                    <script>
                        window.onload = function() { window.print(); window.close(); }
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
        
        // Event Listeners para filtros
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
            cargarMovimientos();
        });
        
        $('#btn-limpiar-filtros').click(function() {
            $('#filtro-fecha').val('hoy');
            $('#rango-fechas-container').hide();
            $('#rango-fechas-hasta-container').hide();
            
            $('#filtro-personal').val('').trigger('change');
            $('#filtro-ubicacion').val('').trigger('change');
            
            $('#filtro-tipo').val('');
            $('#filtro-id-personal').val('');
            
            cargarMovimientos();
        });
        
        $('#filtro-id-personal').keypress(function(e) {
            if (e.which == 13) {
                cargarMovimientos();
            }
        });
        
        // Función para cargar detalles de movimiento
        function cargarDetalleMovimiento(tipo, idMov, categoria) {
            $.ajax({
                url: 'Controlador/ajax_detalle_movimiento.php',
                type: 'GET',
                data: { tipo: tipo, idMov: idMov, categoria: categoria },
                dataType: 'html',
                beforeSend: function() {
                    $('#modal-container').html('<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>');
                },
                success: function(response) {
                    $('#modal-container').html(response);
                    $('.modal').modal('show');
                },
                error: function() {
                    $('#modal-container').html('<div class="alert alert-danger">Error al cargar detalles</div>');
                }
            });
        }
        
        // Delegación de eventos para botones de detalles
        $(document).on('click', '.btn-ver-entrada', function(e) {
            e.preventDefault();
            cargarDetalleMovimiento('entrada', $(this).data('id'), 'Personal');
        });
        
        $(document).on('click', '.btn-ver-salida', function(e) {
            e.preventDefault();
            cargarDetalleMovimiento('salida', $(this).data('id'), 'Personal');
        });
        
        // Cerrar modales
        $(document).on('click', '[data-dismiss="modal"], .btn-close', function() {
            $('.modal').modal('hide');
        });
        
        $(document).on('hidden.bs.modal', '.modal', function() {
            $('#modal-container').empty();
        });
        
        // Cargar datos iniciales y movimientos
        cargarDatosIniciales();
        
        // Cargar movimientos después de 1 segundo
        setTimeout(function() {
            cargarMovimientos();
        }, 1000);
        
        console.log('Sistema completamente inicializado');
    });
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

.select2-container--bootstrap-5 {
    width: 100% !important;
}

.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
    padding-left: 12px;
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

/* Estilos para DataTables SIN botones */
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

/* NO habrá estilos para .dt-buttones porque ya no existen */

/* Responsive */
@media (max-width: 768px) {
    .badge { font-size: 0.75em !important; }
    
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 42px;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single {
        height: 42px;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
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
    .btn-ver-entrada,
    .btn-ver-salida {
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
}
</style>