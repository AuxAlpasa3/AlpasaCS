<?php
include_once "../templates/head.php";
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Registro de Movimientos</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00">
                   
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Fecha:</label>
                                <select id="filtro-fecha" class="form-control">
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
                                <input type="date" id="fecha-inicio" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-2" id="rango-fechas-hasta-container" style="display: none;">
                            <div class="form-group">
                                <label>Hasta:</label>
                                <input type="date" id="fecha-fin" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Personal:</label>
                                <select id="filtro-personal" class="form-control">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Ubicación:</label>
                                <select id="filtro-ubicacion" class="form-control">
                                    <option value="">Todas</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Tipo:</label>
                                <select id="filtro-tipo" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="entrada">Entradas</option>
                                    <option value="salida">Salidas</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="btn-aplicar-filtros" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>ID Personal:</label>
                                <input type="text" id="filtro-id-personal" class="form-control" placeholder="Buscar por ID...">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" id="btn-limpiar-filtros" class="btn btn-secondary btn-block">
                                    <i class="fas fa-broom"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando movimientos...</p>
                    </div>
                    
                    <div id="movimientos-container">
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

<script type="text/javascript">
$(document).ready(function() {
    // Cargar opciones en los selects
    cargarPersonal();
    cargarUbicaciones();
    
    // Mostrar/ocultar rango de fechas
    $('#filtro-fecha').change(function() {
        if ($(this).val() === 'personalizado') {
            $('#rango-fechas-container').show();
            $('#rango-fechas-hasta-container').show();
        } else {
            $('#rango-fechas-container').hide();
            $('#rango-fechas-hasta-container').hide();
        }
    });
    
    // Función para cargar personal
    function cargarPersonal() {
        $.ajax({
            url: 'Controlador/ajax_get_personal.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-personal');
                    select.empty();
                    select.append('<option value="">Todos</option>');
                    
                    $.each(data, function(index, item) {
                        select.append('<option value="' + item.id + '">' + item.nombre + ' (ID: ' + item.codigo + ')</option>');
                    });
                }
            },
            error: function() {
                console.error('Error al cargar personal');
            }
        });
    }
    
    // Función para cargar ubicaciones
    function cargarUbicaciones() {
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
                        select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                    });
                }
            },
            error: function() {
                console.error('Error al cargar ubicaciones');
            }
        });
    }
    
    // Función para cargar movimientos
    function cargarMovimientos() {
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
        
        $.ajax({
            url: 'Controlador/ajax_movimientos.php',
            type: 'GET',
            data: params,
            dataType: 'html',
            success: function(response) {
                $('#movimientos-container').html(response);
                inicializarDataTable();
                $('#loading').hide();
            },
            error: function(xhr, status, error) {
                $('#movimientos-container').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                $('#loading').hide();
            }
        });
    }
    
    // Inicializar DataTable - VERSIÓN MEJORADA
function inicializarDataTable() {
    console.log('Inicializando DataTable...');
    
    var $table = $('#dataTableMovimientos');
    if (!$table.length) {
        console.warn('La tabla no existe');
        $('#loading').hide();
        return;
    }
    
    // Verificar si hay datos (filas con más de una celda)
    var hasData = false;
    $table.find('tbody tr').each(function() {
        var tdCount = $(this).find('td').length;
        console.log('Filas encontradas con ' + tdCount + ' columnas');
        if (tdCount > 1) { // Si tiene más de 1 celda (8 columnas normales)
            hasData = true;
        }
    });
    
    // Verificar estructura
    var thCount = $table.find('thead tr th').length;
    console.log('Columnas en thead: ' + thCount + ', ¿Tiene datos?: ' + hasData);
    
    // Esperar a que el DOM se actualice
    setTimeout(function() {
        // Destruir DataTable si ya existe
        if ($.fn.DataTable.isDataTable('#dataTableMovimientos')) {
            $('#dataTableMovimientos').DataTable().destroy();
            $table.removeClass('dataTable no-footer');
        }
        
        try {
            if (hasData) {
                // INICIALIZAR CON DATOS
                $('#dataTableMovimientos').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json",
                        "emptyTable": "No hay datos disponibles en la tabla"
                    },
                    "responsive": true,
                    "autoWidth": false,
                    "order": [[0, "desc"]],
                    "pageLength": 25,
                    "initComplete": function(settings, json) {
                        console.log('DataTable inicializado con datos');
                        $('#loading').hide();
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
                // SIN DATOS - Solo mostrar mensaje, NO inicializar DataTable
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
            }
        } catch (error) {
            console.error('Error al inicializar DataTable:', error);
            $('#loading').hide();
            
            $table.addClass('table table-bordered table-striped');
        }
    }, 100);
}
    
    $('#btn-aplicar-filtros').click(function() {
        cargarMovimientos();
    });
    
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-fecha').val('hoy');
        $('#rango-fechas-container').hide();
        $('#rango-fechas-hasta-container').hide();
        $('#filtro-personal').val('');
        $('#filtro-ubicacion').val('');
        $('#filtro-tipo').val('');
        $('#filtro-id-personal').val('');
        cargarMovimientos();
    });
    
    $('#filtro-id-personal').keypress(function(e) {
        if (e.which == 13) {
            cargarMovimientos();
        }
    });
    
    // Cargar movimientos al inicio
    cargarMovimientos();
    
    // Función para detalles
    function cargarDetalleMovimiento(tipo, idMov) {
        $.ajax({
            url: 'Controlador/ajax_detalle_movimiento.php',
            type: 'GET',
            data: { tipo: tipo, idMov: idMov },
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
    
    // Eventos para detalles
    $(document).on('click', '.btn-ver-entrada', function(e) {
        e.preventDefault();
        cargarDetalleMovimiento('entrada', $(this).data('id'));
    });
    
    $(document).on('click', '.btn-ver-salida', function(e) {
        e.preventDefault();
        cargarDetalleMovimiento('salida', $(this).data('id'));
    });
    
    // Cerrar modal
    $(document).on('click', '[data-dismiss="modal"], .btn-close', function() {
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
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.badge-primary { background-color: #007bff; color: white; }

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

@media (max-width: 768px) {
    .badge { font-size: 0.75em !important; }
}
</style>