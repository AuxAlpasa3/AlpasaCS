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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">ID Personal:</label>
                                <input type="text" id="filtro-id-personal" class="form-control form-control-lg" placeholder="Buscar por ID...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Personal:</label>
                                <select id="filtro-personal" class="form-control form-control-lg select2-personal" style="width: 100%;">
                                    <option value="">Todo el personal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Ubicación:</label>
                                <select id="filtro-ubicacion" class="form-control form-control-lg select2-ubicacion" style="width: 100%;">
                                    <option value="">Todas las ubicaciones</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Fecha:</label>
                                <select id="filtro-fecha" class="form-control form-control-lg">
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
                                <label class="form-label">Tipo:</label>
                                <select id="filtro-tipo" class="form-control form-control-lg">
                                    <option value="">Todos</option>
                                    <option value="entrada">Entradas</option>
                                    <option value="salida">Salidas</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
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
                    
                    <div id="resultados-movimientos">
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-exchange-alt fa-3x mb-3"></i>
                            <h4>Seleccione filtros y presione "Buscar"</h4>
                            <p>Los movimientos aparecerán aquí</p>
                        </div>
                    </div>
                    
                    <!-- Contenedor de paginación -->
                    <div id="pagination-container" class="mt-4" style="display: none;">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="dataTables_info" id="pagination-info">
                                    Mostrando 0 a 0 de 0 registros
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="pagination-controls">
                                    <ul class="pagination justify-content-end">
                                        <li class="paginate_button page-item previous disabled" id="pagination-prev">
                                            <a href="#" class="page-link" data-page="prev">
                                                <i class="fas fa-chevron-left"></i> Anterior
                                            </a>
                                        </li>
                                        <li class="paginate_button page-item next disabled" id="pagination-next">
                                            <a href="#" class="page-link" data-page="next">
                                                Siguiente <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="text-center" id="pagination-numbers"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selector de registros por página -->
                    <div class="row mt-3" id="records-per-page-container" style="display: none;">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center justify-content-end">
                                <label class="mr-2 mb-0 font-weight-bold">Mostrar:</label>
                                <select id="records-per-page" class="form-control form-control-sm" style="width: auto;">
                                    <option value="10">10 registros</option>
                                    <option value="25">25 registros</option>
                                    <option value="50">50 registros</option>
                                    <option value="100">100 registros</option>
                                </select>
                            </div>
                        </div>
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    // Variables de paginación
    var paginacionActual = 1;
    var registrosPorPagina = 10;
    var totalRegistros = 0;
    var totalPaginas = 0;
    var filtrosExpandidos = true;
    
    // Función para alternar filtros
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
    
    // Cargar estado de filtros guardado
    var filtrosGuardados = localStorage.getItem('filtrosExpandidos');
    if (filtrosGuardados === 'false') {
        filtrosExpandidos = false;
        $('#filtrosBody').hide();
        $('#filtrosHeader .toggle-icon').html('<i class="fas fa-chevron-down"></i>');
    }
    
    $('#filtrosHeader').click(function() {
        toggleFiltros();
    });
    
    // Función para mostrar notificaciones
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
    
    // Función para cargar datos de filtros
    function cargarDatosFiltros() {
        // Inicializar Select2
        $('#filtro-fecha, #filtro-tipo').select2({
            theme: 'custom-theme',
            width: '100%',
            dropdownCssClass: 'select2-dropdown-enhanced',
            selectionCssClass: 'select2-selection-enhanced',
            language: 'es',
            minimumResultsForSearch: -1
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
                    select.append('<option value="">Todo el personal</option>');
                    
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
                        language: 'es'
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
                    select.append('<option value="">Todas las ubicaciones</option>');
                    
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
                        language: 'es'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando ubicaciones:', error);
            }
        });
    }
    
    // Función para cargar movimientos con paginación
    function cargarMovimientos(pagina = 1) {
        var filtros = {
            filtro_fecha: $('#filtro-fecha').val(),
            fecha_inicio: $('#fecha-inicio').val(),
            fecha_fin: $('#fecha-fin').val(),
            id_personal: $('#filtro-personal').val(),
            id_ubicacion: $('#filtro-ubicacion').val(),
            tipo_movimiento: $('#filtro-tipo').val(),
            id_personal_especifico: $('#filtro-id-personal').val(),
            pagina: pagina,
            por_pagina: registrosPorPagina
        };
        
        $('#loading').show();
        $('#resultados-movimientos').hide();
        $('#pagination-container').hide();
        $('#records-per-page-container').hide();
        
        $.ajax({
            url: 'Controlador/ajax_movimientos.php',
            type: 'GET',
            data: filtros,
            success: function(response) {
                $('#loading').hide();
                $('#resultados-movimientos').html(response).show();
                
                // Obtener datos de paginación de los campos ocultos
                totalRegistros = parseInt($('#pagination-total').val()) || 0;
                paginacionActual = parseInt($('#pagination-current').val()) || 1;
                registrosPorPagina = parseInt($('#pagination-per-page').val()) || 10;
                totalPaginas = parseInt($('#pagination-total-pages').val()) || 0;
                
                if (totalRegistros > 0) {
                    actualizarPaginacion();
                    $('#pagination-container').show();
                    $('#records-per-page-container').show();
                }
                
                initEvents();
                showNotification('Movimientos cargados correctamente', 'success');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                $('#resultados-movimientos').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Error al cargar los movimientos: ${error}
                    </div>
                `).show();
                showNotification('Error al cargar los movimientos', 'error');
            }
        });
    }
    
    // Función para actualizar la paginación
    function actualizarPaginacion() {
        // Calcular inicio y fin
        var inicio = ((paginacionActual - 1) * registrosPorPagina) + 1;
        var fin = Math.min(paginacionActual * registrosPorPagina, totalRegistros);
        
        // Actualizar información
        $('#pagination-info').html(`Mostrando ${inicio} a ${fin} de ${totalRegistros} registros`);
        
        // Actualizar botones anterior/siguiente
        if (paginacionActual <= 1) {
            $('#pagination-prev').addClass('disabled');
        } else {
            $('#pagination-prev').removeClass('disabled');
        }
        
        if (paginacionActual >= totalPaginas) {
            $('#pagination-next').addClass('disabled');
        } else {
            $('#pagination-next').removeClass('disabled');
        }
        
        // Generar números de página
        var numerosHtml = '<ul class="pagination justify-content-center">';
        
        // Mostrar siempre primera página
        if (paginacionActual > 3) {
            numerosHtml += `<li class="paginate_button page-item"><a href="#" class="page-link" data-page="1">1</a></li>`;
            if (paginacionActual > 4) {
                numerosHtml += `<li class="paginate_button page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Páginas alrededor de la actual
        for (var i = Math.max(1, paginacionActual - 2); i <= Math.min(totalPaginas, paginacionActual + 2); i++) {
            if (i >= 1 && i <= totalPaginas) {
                numerosHtml += `<li class="paginate_button page-item ${i === paginacionActual ? 'active' : ''}">
                    <a href="#" class="page-link" data-page="${i}">${i}</a>
                </li>`;
            }
        }
        
        // Mostrar siempre última página
        if (paginacionActual < totalPaginas - 2) {
            if (paginacionActual < totalPaginas - 3) {
                numerosHtml += `<li class="paginate_button page-item disabled"><span class="page-link">...</span></li>`;
            }
            numerosHtml += `<li class="paginate_button page-item"><a href="#" class="page-link" data-page="${totalPaginas}">${totalPaginas}</a></li>`;
        }
        
        numerosHtml += '</ul>';
        $('#pagination-numbers').html(numerosHtml);
    }
    
    // Función para inicializar eventos de la tabla
    function initEvents() {
        $(document).off('click', '.btn-ver-entrada, .btn-ver-salida').on('click', '.btn-ver-entrada, .btn-ver-salida', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var idMov = $(this).data('id');
            var tipo = $(this).hasClass('btn-ver-entrada') ? 'entrada' : 'salida';
            
            if (idMov) {
                cargarDetalleMovimiento(idMov, tipo);
            }
        });
    }
    
    // Función para cargar detalle de movimiento
    function cargarDetalleMovimiento(idMov, tipo) {
        $.ajax({
            url: 'Modales/Detalle_Movimiento.php',
            type: 'GET',
            data: {
                idMov: idMov,
                tipo: tipo
            },
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(response) {
                $('#loading').hide();
                $('#modal-container').html(response);
                if (tipo === 'entrada') {
                    $('#DetalleEntrada').modal('show');
                } else {
                    $('#DetalleSalida').modal('show');
                }
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el detalle del movimiento', 'danger');
            }
        });
    }
    
    // Evento para cambio de filtro de fecha
    $('#filtro-fecha').change(function() {
        if ($(this).val() === 'personalizado') {
            $('#rango-fechas-container').show();
            $('#rango-fechas-hasta-container').show();
        } else {
            $('#rango-fechas-container').hide();
            $('#rango-fechas-hasta-container').hide();
        }
    });
    
    // Eventos de paginación
    $(document).on('click', '#pagination-numbers .page-link', function(e) {
        e.preventDefault();
        var pagina = $(this).data('page');
        if (pagina && !$(this).parent().hasClass('active')) {
            paginacionActual = pagina;
            cargarMovimientos(paginacionActual);
        }
    });
    
    $(document).on('click', '#pagination-prev .page-link', function(e) {
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled') && paginacionActual > 1) {
            paginacionActual--;
            cargarMovimientos(paginacionActual);
        }
    });
    
    $(document).on('click', '#pagination-next .page-link', function(e) {
        e.preventDefault();
        if (!$(this).parent().hasClass('disabled') && paginacionActual < totalPaginas) {
            paginacionActual++;
            cargarMovimientos(paginacionActual);
        }
    });
    
    // Evento para cambiar registros por página
    $('#records-per-page').change(function() {
        registrosPorPagina = parseInt($(this).val());
        paginacionActual = 1;
        cargarMovimientos(paginacionActual);
    });
    
    // Evento para aplicar filtros
    $('#btn-aplicar-filtros').click(function() {
        paginacionActual = 1;
        cargarMovimientos(1);
    });
    
    // Evento para limpiar filtros
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-fecha').val('hoy').trigger('change');
        $('#fecha-inicio').val('<?php echo date('Y-m-d'); ?>');
        $('#fecha-fin').val('<?php echo date('Y-m-d'); ?>');
        $('#filtro-personal').val(null).trigger('change');
        $('#filtro-ubicacion').val(null).trigger('change');
        $('#filtro-tipo').val('').trigger('change');
        $('#filtro-id-personal').val('');
        
        paginacionActual = 1;
        showNotification('Filtros limpiados', 'info');
    });
    
    // Evento para exportar Excel
    $('#btn-export-excel').click(function(e) {
        e.preventDefault();
        
        if ($('#resultados-movimientos').find('tbody tr').length === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        var filtros = {
            filtro_fecha: $('#filtro-fecha').val(),
            fecha_inicio: $('#fecha-inicio').val(),
            fecha_fin: $('#fecha-fin').val(),
            id_personal: $('#filtro-personal').val(),
            id_ubicacion: $('#filtro-ubicacion').val(),
            tipo_movimiento: $('#filtro-tipo').val(),
            id_personal_especifico: $('#filtro-id-personal').val()
        };
        
        var params = new URLSearchParams();
        Object.keys(filtros).forEach(key => {
            if (filtros[key]) params.append(key, filtros[key]);
        });
        
        showNotification('Generando archivo Excel...', 'info');
        window.location.href = 'Controlador/exportar_movimientos_excel.php?' + params.toString();
    });
    
    // Evento para exportar PDF
    $('#btn-export-pdf').click(function(e) {
        e.preventDefault();
        
        if ($('#resultados-movimientos').find('tbody tr').length === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        var filtros = {
            filtro_fecha: $('#filtro-fecha').val(),
            fecha_inicio: $('#fecha-inicio').val(),
            fecha_fin: $('#fecha-fin').val(),
            id_personal: $('#filtro-personal').val(),
            id_ubicacion: $('#filtro-ubicacion').val(),
            tipo_movimiento: $('#filtro-tipo').val(),
            id_personal_especifico: $('#filtro-id-personal').val()
        };
        
        var params = new URLSearchParams();
        Object.keys(filtros).forEach(key => {
            if (filtros[key]) params.append(key, filtros[key]);
        });
        
        showNotification('Generando archivo PDF...', 'info');
        window.location.href = 'Controlador/exportar_movimientos_pdf.php?' + params.toString();
    });
    
    // Evento para imprimir
    $('#btn-print').click(function(e) {
        e.preventDefault();
        
        if ($('#resultados-movimientos').find('tbody tr').length === 0) {
            showNotification('No hay datos para imprimir', 'warning');
            return;
        }
        
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Reporte de Movimientos</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
        printWindow.document.write('h1 { color: #d94f00; text-align: center; }');
        printWindow.document.write('.fecha { text-align: center; color: #666; margin-bottom: 20px; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
        printWindow.document.write('th { background-color: #d94f00; color: white; padding: 10px; text-align: center; }');
        printWindow.document.write('td { padding: 8px; border: 1px solid #ddd; text-align: center; }');
        printWindow.document.write('tr:nth-child(even) { background-color: #f9f9f9; }');
        printWindow.document.write('.resumen { margin-top: 20px; padding: 10px; background-color: #f5f5f5; border-radius: 5px; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        
        printWindow.document.write('<h1>Reporte de Movimientos</h1>');
        printWindow.document.write('<div class="fecha">Generado: ' + new Date().toLocaleString() + '</div>');
        
        var table = $('#dataTableMovimientos').clone();
        table.find('.btn, .btn-ver-entrada, .btn-ver-salida').each(function() {
            $(this).replaceWith($(this).text().replace('Ver ', ''));
        });
        
        printWindow.document.write(table[0].outerHTML);
        
        var totalRows = $('#dataTableMovimientos tbody tr').length;
        var totalEntradas = $('#dataTableMovimientos tbody tr').filter(function() {
            return $(this).find('.btn-ver-entrada').length > 0;
        }).length;
        var totalSalidas = $('#dataTableMovimientos tbody tr').filter(function() {
            return $(this).find('.btn-ver-salida').length > 0;
        }).length;
        
        printWindow.document.write('<div class="resumen">');
        printWindow.document.write('<h3>Resumen:</h3>');
        printWindow.document.write('<p>Total de registros: ' + totalRows + '</p>');
        printWindow.document.write('<p>Total de entradas: ' + totalEntradas + '</p>');
        printWindow.document.write('<p>Total de salidas: ' + totalSalidas + '</p>');
        printWindow.document.write('</div>');
        
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
    
    // Evento para recargar
    $('#btn-refresh').click(function(e) {
        e.preventDefault();
        paginacionActual = 1;
        cargarMovimientos(1);
    });
    
    // Evento para buscar con Enter
    $('#filtro-id-personal').keypress(function(e) {
        if (e.which == 13) {
            paginacionActual = 1;
            cargarMovimientos(1);
        }
    });
    
    // Manejo de modales
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        $('#modal-container').empty();
    });
    
    // Cargar datos iniciales
    cargarDatosFiltros();
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

/* Badges */
.badge { 
    padding: 4px 8px; 
    border-radius: 12px; 
    font-size: 12px; 
    font-weight: 600;
}

.badge-success { background-color: var(--success-color); color: white; }
.badge-danger { background-color: var(--danger-color); color: white; }
.badge-warning { background-color: var(--warning-color); color: #212529; }
.badge-info { background-color: var(--info-color); color: white; }
.badge-secondary { background-color: var(--secondary-color); color: white; }
.badge-primary { background-color: var(--primary-orange); color: white; }
.badge-dark { background-color: var(--dark-color); color: white; }

/* Loading spinner */
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

/* Select2 personalizado */
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

/* Paginación */
.pagination {
    margin: 0;
    flex-wrap: wrap;
}

.pagination .page-link {
    color: var(--primary-orange);
    border: 1px solid #dee2e6;
    padding: 8px 12px;
    margin: 0 2px;
    border-radius: 4px;
    cursor: pointer;
}

.pagination .page-link:hover {
    background-color: var(--primary-orange);
    border-color: var(--primary-orange);
    color: white;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-orange);
    border-color: var(--primary-orange);
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    cursor: not-allowed;
    background-color: #fff;
    border-color: #dee2e6;
}

#pagination-info {
    font-size: 14px;
    color: #6c757d;
    padding: 8px 0;
}

#records-per-page {
    border: 2px solid var(--border-color);
    border-radius: 6px;
    padding: 6px 12px;
    cursor: pointer;
}

#records-per-page:focus {
    border-color: var(--primary-orange);
    outline: none;
    box-shadow: 0 0 0 3px rgba(217, 79, 0, 0.1);
}

/* Botones */
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

/* Header de filtros */
#filtrosHeader {
    transition: background-color 0.2s;
}

#filtrosHeader:hover {
    background-color: var(--primary-orange-dark) !important;
}

#filtrosHeader .toggle-icon {
    transition: transform 0.2s;
}

/* Tabla */
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

#dataTableMovimientos tbody tr:hover {
    background-color: var(--table-hover);
}

/* Formularios */
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

/* Responsive */
@media (max-width: 1200px) {
    .btn-group .btn {
        padding: 6px 8px !important;
        font-size: 12px !important;
        margin: 2px !important;
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
    
    .pagination .page-link {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    #pagination-info {
        text-align: center;
        margin-bottom: 10px;
    }
    
    #records-per-page-container .col-md-12 {
        text-align: center !important;
    }
    
    #records-per-page-container .d-flex {
        justify-content: center !important;
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
    
    .pagination {
        justify-content: center;
    }
    
    .pagination .page-link {
        padding: 4px 8px;
        font-size: 11px;
    }
}

/* Impresión */
@media print {
    .btn-group,
    .card-header,
    .form-group,
    #loading,
    #pagination-container,
    #records-per-page-container,
    .btn-ver-entrada,
    .btn-ver-salida,
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
}

/* Z-index modales */
.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
}

#photoModal {
    z-index: 99999 !important;
}

.modal.fade.show {
    z-index: 99999 !important;
}

.modal-backdrop.show {
    z-index: 99998 !important;
}
</style>