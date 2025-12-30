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
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">Filtros de Búsqueda</h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <button id="btn-recargar-movimientos" class="btn btn-sm btn-light">
                                <i class="fas fa-sync-alt"></i> Recargar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-filter"></i> Filtros Avanzados</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="filtros-form">
                                        <div class="row">
                                            <!-- Filtro por Fecha -->
                                            <div class="col-md-4">
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
                                            
                                            <!-- Rango de fechas personalizado (oculto inicialmente) -->
                                            <div class="col-md-4" id="rango-fechas-container" style="display: none;">
                                                <div class="form-group">
                                                    <label>Desde:</label>
                                                    <input type="date" id="fecha-inicio" class="form-control" 
                                                           value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-4" id="rango-fechas-hasta-container" style="display: none;">
                                                <div class="form-group">
                                                    <label>Hasta:</label>
                                                    <input type="date" id="fecha-fin" class="form-control" 
                                                           value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            
                                            <!-- Filtro por Personal -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Personal:</label>
                                                    <select id="filtro-personal" class="form-control select2" style="width: 100%;">
                                                        <option value="">Todos</option>
                                                        <?php
                                                        // Aquí puedes cargar dinámicamente el personal desde la base de datos
                                                        // Ejemplo:
                                                        // $personal = obtenerPersonal();
                                                        // foreach($personal as $p) {
                                                        //     echo '<option value="' . $p['id'] . '">' . $p['nombre'] . ' - ' . $p['id_personal'] . '</option>';
                                                        // }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <!-- Filtro por Ubicación -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Ubicación:</label>
                                                    <select id="filtro-ubicacion" class="form-control select2" style="width: 100%;">
                                                        <option value="">Todas</option>
                                                        <?php
                                                        // Aquí puedes cargar dinámicamente las ubicaciones desde la base de datos
                                                        // Ejemplo:
                                                        // $ubicaciones = obtenerUbicaciones();
                                                        // foreach($ubicaciones as $u) {
                                                        //     echo '<option value="' . $u['id'] . '">' . $u['nombre'] . '</option>';
                                                        // }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <!-- Filtro por Tipo de Movimiento -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Tipo de Movimiento:</label>
                                                    <select id="filtro-tipo" class="form-control">
                                                        <option value="">Todos</option>
                                                        <option value="entrada">Entradas</option>
                                                        <option value="salida">Salidas</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <!-- ID Personal (búsqueda específica) -->
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>ID Personal:</label>
                                                    <input type="text" id="filtro-id-personal" class="form-control" 
                                                           placeholder="Buscar por ID específico">
                                                </div>
                                            </div>
                                            
                                            <!-- Botones de acción -->
                                            <div class="col-md-12">
                                                <div class="form-group text-right">
                                                    <button type="button" id="btn-aplicar-filtros" class="btn btn-primary">
                                                        <i class="fas fa-search"></i> Aplicar Filtros
                                                    </button>
                                                    <button type="button" id="btn-limpiar-filtros" class="btn btn-secondary">
                                                        <i class="fas fa-broom"></i> Limpiar Filtros
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Loading y resultados -->
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
    // Inicializar Select2 si está disponible
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Seleccione una opción'
        });
    }
    
    // Mostrar/ocultar rango de fechas según selección
    $('#filtro-fecha').change(function() {
        if ($(this).val() === 'personalizado') {
            $('#rango-fechas-container').show();
            $('#rango-fechas-hasta-container').show();
        } else {
            $('#rango-fechas-container').hide();
            $('#rango-fechas-hasta-container').hide();
        }
    });
    
    // Función para cargar movimientos con filtros
    function cargarMovimientos() {
        $('#loading').show();
        $('#movimientos-container').empty();
        
        // Obtener valores de los filtros
        var filtroFecha = $('#filtro-fecha').val();
        var fechaInicio = $('#fecha-inicio').val();
        var fechaFin = $('#fecha-fin').val();
        var filtroPersonal = $('#filtro-personal').val();
        var filtroUbicacion = $('#filtro-ubicacion').val();
        var filtroTipo = $('#filtro-tipo').val();
        var filtroIdPersonal = $('#filtro-id-personal').val();
        
        // Parámetros para la petición AJAX
        var params = {
            filtro_fecha: filtroFecha,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            id_personal: filtroPersonal,
            id_ubicacion: filtroUbicacion,
            tipo_movimiento: filtroTipo,
            id_personal_especifico: filtroIdPersonal
        };
        
        $.ajax({
            url: 'Controlador/ajax_movimientos.php',
            type: 'GET',
            data: params,
            dataType: 'html',
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(response) {
                $('#movimientos-container').html(response);
                try {
                    inicializarDataTable();
                } catch (error) {
                    console.error('Error al inicializar DataTable:', error);
                }
                $('#loading').hide();
            },
            error: function(xhr, status, error) {
                $('#movimientos-container').html(
                    '<div class="alert alert-danger">Error al cargar los movimientos: ' + error + '</div>'
                );
                $('#loading').hide();
            },
            complete: function() {
                $('#loading').hide();
            }
        });
    }
    
    // Inicializar DataTable
    function inicializarDataTable() {
        if ($('#dataTableMovimientos').length) {
            if ($.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                $('#dataTableMovimientos').DataTable().destroy();
                $('#dataTableMovimientos').empty();
            }
            
            $('#dataTableMovimientos').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                },
                "responsive": true,
                "autoWidth": false,
                "order": [[0, "desc"]],
                "pageLength": 25,
                "initComplete": function(settings, json) {
                    console.log('DataTable inicializado correctamente');
                    // Agregar información de filtros aplicados
                    var filtrosInfo = obtenerInfoFiltros();
                    if (filtrosInfo) {
                        $('#dataTableMovimientos_wrapper').prepend(
                            '<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-info-circle"></i> Filtros aplicados: ' + filtrosInfo +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                            '</div>'
                        );
                    }
                },
                "drawCallback": function(settings) {
                    $('#loading').hide();
                }
            });
        } else {
            console.warn('La tabla #dataTableMovimientos no existe en el HTML cargado');
            $('#loading').hide();
        }
    }
    
    function obtenerInfoFiltros() {
        var info = [];
        
        var filtroFecha = $('#filtro-fecha').val();
        if (filtroFecha && filtroFecha !== 'hoy') {
            if (filtroFecha === 'personalizado') {
                var fechaInicio = $('#fecha-inicio').val();
                var fechaFin = $('#fecha-fin').val();
                if (fechaInicio && fechaFin) {
                    info.push('Fecha: ' + fechaInicio + ' a ' + fechaFin);
                }
            } else {
                var textos = {
                    'ayer': 'Ayer',
                    'semana': 'Esta semana',
                    'mes': 'Este mes'
                };
                info.push('Fecha: ' + textos[filtroFecha]);
            }
        }
        
        var filtroTipo = $('#filtro-tipo').val();
        if (filtroTipo) {
            info.push('Tipo: ' + (filtroTipo === 'entrada' ? 'Entradas' : 'Salidas'));
        }
        
        var filtroIdPersonal = $('#filtro-id-personal').val();
        if (filtroIdPersonal) {
            info.push('ID Personal: ' + filtroIdPersonal);
        }
        
        var filtroPersonal = $('#filtro-personal option:selected').text();
        if ($('#filtro-personal').val()) {
            info.push('Personal: ' + filtroPersonal);
        }
        
        var filtroUbicacion = $('#filtro-ubicacion option:selected').text();
        if ($('#filtro-ubicacion').val()) {
            info.push('Ubicación: ' + filtroUbicacion);
        }
        
        return info.length > 0 ? info.join(' | ') : null;
    }
    
    $('#btn-aplicar-filtros').click(function() {
        cargarMovimientos();
    });
    
    $('#btn-limpiar-filtros').click(function() {
        $('#filtros-form')[0].reset();
        $('#filtro-fecha').val('hoy').trigger('change');
        $('#rango-fechas-container').hide();
        $('#rango-fechas-hasta-container').hide();
        if ($.fn.select2) {
            $('.select2').val('').trigger('change');
        }
        cargarMovimientos();
    });
    
    // Permitir búsqueda por Enter en ID Personal
    $('#filtro-id-personal').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            cargarMovimientos();
        }
    });
    
    // Cargar movimientos inicialmente
    cargarMovimientos();
    
    // Función para cargar detalles del movimiento
    function cargarDetalleMovimiento(tipo, idMov) {
        $.ajax({
            url: 'Controlador/ajax_detalle_movimiento.php',
            type: 'GET',
            data: {
                tipo: tipo,
                idMov: idMov
            },
            dataType: 'html',
            beforeSend: function() {
                $('#modal-container').html('<div class="text-center p-4"><div class="spinner-border text-primary"></div></div>');
            },
            success: function(response) {
                $('#modal-container').html(response);
                $('.modal').modal('show');
            },
            error: function(xhr, status, error) {
                $('#modal-container').html(
                    '<div class="alert alert-danger">Error al cargar los detalles: ' + error + '</div>'
                );
            }
        });
    }
    
    // Eventos para ver detalles
    $(document).on('click', '.btn-ver-entrada', function(e) {
        e.preventDefault();
        var idMov = $(this).data('id');
        cargarDetalleMovimiento('entrada', idMov);
    });
    
    $(document).on('click', '.btn-ver-salida', function(e) {
        e.preventDefault();
        var idMov = $(this).data('id');
        cargarDetalleMovimiento('salida', idMov);
    });
    
    // Cerrar modales
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        $('#modal-container').empty();
    });
    
    // Recargar movimientos
    $(document).on('click', '#btn-recargar-movimientos', function() {
        cargarMovimientos();
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
.badge-success { 
    background-color: #28a745; 
    color: white; 
}
.badge-warning { 
    background-color: #ffc107; 
    color: #212529; 
}
.badge-info { 
    background-color: #17a2b8; 
    color: white; 
}
.badge-secondary { 
    background-color: #6c757d; 
    color: white; 
}

.spinner-border { 
    width: 3rem; 
    height: 3rem; 
}

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

.card-primary.card-outline {
    border-top: 3px solid #007bff;
}

.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px) !important;
}

@media (max-width: 768px) {
    .badge { 
        font-size: 0.75em !important; 
        min-width: 70px !important; 
    }
    
    .row.mb-4 .col-md-4 {
        margin-bottom: 15px;
    }
    
    .form-group.text-right {
        text-align: left !important;
    }
}
</style>