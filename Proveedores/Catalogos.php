<?php
include_once "../templates/head.php";
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 fw-bold" style="color: #d94f00">Control de Proveedores</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="gestion_proveedores.php" class="btn btn-primary" 
                    style="background-color: #d94f00; border-color: #d94f00;">
                        <i class="fas fa-users-cog"></i> Gestionar Proveedores
                    </a>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary mb-4">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-clock mr-2"></i>Registro Rápido de Visita
                    </h3>
                </div>
                <div class="card-body">
                    <form id="formRegistroRapido">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Proveedor:</label>
                                    <select id="proveedor-rapido" name="IdProveedor" class="form-control select2-proveedor" 
                                            style="width: 100%;" required>
                                        <option value="">Buscar proveedor...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Personal del Proveedor:</label>
                                    <select id="personal-proveedor" name="IdProveedorPersonal[]" 
                                            class="form-control select2-personal-proveedor" 
                                            style="width: 100%;" multiple required>
                                        <option value="">Seleccionar personal...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Área:</label>
                                    <select id="area-rapido" name="IdDepartamento" class="form-control select2-area" 
                                            style="width: 100%;" required>
                                        <option value="">Seleccionar área</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Fecha:</label>
                                    <input type="date" id="fecha-rapido" name="FechaVisita" 
                                           class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Hora:</label>
                                    <input type="time" id="hora-rapido" name="HoraVisita" 
                                           class="form-control" value="<?php echo date('H:i'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-1 align-self-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block" 
                                            style="background-color: #d94f00; border-color: #d94f00;">
                                        <i class="fas fa-qrcode"></i> QR
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Vehículo:</label>
                                    <select id="vehiculo-rapido" name="IdVehiculo" class="form-control select2-vehiculo" 
                                            style="width: 100%;">
                                        <option value="">Sin vehículo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Motivo:</label>
                                    <input type="text" id="motivo-rapido" name="Motivo" 
                                           class="form-control" placeholder="Descripción del motivo de la visita..." required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Proveedor:</label>
                                <select id="filtro-proveedor" class="form-control select2-proveedor-filtro" style="width: 100%;">
                                    <option value="">Todos los proveedores</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Área:</label>
                                <select id="filtro-area" class="form-control select2-area-filtro" style="width: 100%;">
                                    <option value="">Todas las áreas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">Fecha:</label>
                                <select id="filtro-fecha" class="form-control" style="width: 100%;">
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
                                <input type="date" id="fecha-inicio" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-2" id="rango-fechas-hasta-container" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Hasta:</label>
                                <input type="date" id="fecha-fin" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Estatus:</label>
                                <select id="filtro-estatus" class="form-control" style="width: 100%;">
                                    <option value="">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="activo">Activo</option>
                                    <option value="completado">Completado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Personal:</label>
                                <select id="filtro-personal" class="form-control select2-personal-filtro" style="width: 100%;">
                                    <option value="">Todo el personal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">QR Code:</label>
                                <input type="text" id="filtro-qr" class="form-control" placeholder="Buscar por QR...">
                            </div>
                        </div>
                        <div class="col-md-3 text-right mt-4">
                            <div class="form-group">
                                <button type="button" id="btn-aplicar-filtros" class="btn btn-primary" 
                                        style="background-color: #d94f00; border-color: #d94f00;">
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
                                <button type="button" class="btn btn-outline-primary" id="btn-nuevo-proveedor">
                                    <i class="fas fa-plus mr-1"></i> Nuevo Proveedor
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-nuevo-personal">
                                    <i class="fas fa-user-plus mr-1"></i> Nuevo Personal
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-ver-qr-activo">
                                    <i class="fas fa-qrcode mr-1"></i> QR Activo
                                </button>
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
                        <i class="fas fa-truck mr-2"></i>Visitas de Proveedores
                    </h3>
                </div>
                <div class="card-body">
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-primary">Cargando visitas...</p>
                    </div>
                    <div id="resultados-visitas" class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTableVisitas">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>QR</th>
                                    <th>Proveedor</th>
                                    <th>Personal</th>
                                    <th>Área</th>
                                    <th>Fecha/Hora</th>
                                    <th>Motivo</th>
                                    <th>Vehículo</th>
                                    <th>Estatus</th>
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
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title">Código QR de Acceso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer" style="padding: 20px; background: white; border-radius: 10px; display: inline-block;"></div>
                <div class="mt-3" id="qrInfo"></div>
                <div class="text-muted small mt-2" id="qrExpiration"></div>
                <div class="mt-4">
                    <button type="button" class="btn btn-primary mr-2" id="btn-enviar-qr">
                        <i class="fas fa-paper-plane mr-1"></i> Enviar
                    </button>
                    <button type="button" class="btn btn-success mr-2" id="btn-descargar-qr">
                        <i class="fas fa-download mr-1"></i> Descargar
                    </button>
                    <button type="button" class="btn btn-info" id="btn-imprimir-qr">
                        <i class="fas fa-print mr-1"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modal-container"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    // Variables globales
    var filtrosExpandidos = true;
    var currentQRVisitId = null;
    var qrGenerator = null;
    var currentProveedorId = null;
    var loadingVisitas = false;
    
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
    
    // Cargar estado de filtros
    var filtrosGuardados = localStorage.getItem('filtrosExpandidos');
    if (filtrosGuardados === 'false') {
        filtrosExpandidos = false;
        $('#filtrosBody').hide();
        $('#filtrosHeader .toggle-icon').html('<i class="fas fa-chevron-down"></i>');
    }
    
    // Evento toggle filtros
    $('#filtrosHeader').click(toggleFiltros);
    
    // Función mejorada de notificaciones
    function showNotification(message, type = 'success', duration = 5000) {
        const alertClass = type === 'success' ? 'alert-success' : 
                         type === 'error' ? 'alert-danger' : 
                         type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const icon = type === 'success' ? 'fa-check-circle' : 
                    type === 'error' ? 'fa-times-circle' : 
                    type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show notification-toast" role="alert" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                <div class="d-flex align-items-center">
                    <i class="fas ${icon} mr-2 fa-lg"></i>
                    <span>${message}</span>
                    <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        `;
        
        $('.notification-toast').remove();
        $('body').append(alertHtml);
        
        setTimeout(() => {
            $('.notification-toast').fadeOut(300, function() {
                $(this).remove();
            });
        }, duration);
    }
    
    // Función para cargar datos en selects
    function cargarDatosSelects() {
        // Cargar proveedores
        $.ajax({
            url: 'Controlador/ajax_get_proveedores.php',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(data) {
                if (Array.isArray(data) && data.length > 0) {
                    var selectRapido = $('#proveedor-rapido');
                    var selectFiltro = $('#filtro-proveedor');
                    selectRapido.empty().append('<option value="">Buscar proveedor...</option>');
                    selectFiltro.empty().append('<option value="">Todos los proveedores</option>');
                    
                    $.each(data, function(index, item) {
                        var option = '<option value="' + item.IdProveedor + '">' + 
                                   item.NombreProveedor + (item.Email ? ' - ' + item.Email : '') + '</option>';
                        selectRapido.append(option);
                        selectFiltro.append(option);
                    });
                    
                    inicializarSelect2();
                } else {
                    console.warn('No se encontraron proveedores');
                    showNotification('No hay proveedores registrados', 'warning');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar proveedores:', error);
                showNotification('Error al cargar proveedores', 'error');
            }
        });
        
        // Cargar áreas
        $.ajax({
            url: 'Controlador/ajax_get_areas.php',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(data) {
                if (Array.isArray(data) && data.length > 0) {
                    var selectRapido = $('#area-rapido');
                    var selectFiltro = $('#filtro-area');
                    selectRapido.empty().append('<option value="">Seleccionar área</option>');
                    selectFiltro.empty().append('<option value="">Todas las áreas</option>');
                    
                    $.each(data, function(index, item) {
                        var option = '<option value="' + item.IdDepartamento + '">' + item.NombreArea + '</option>';
                        selectRapido.append(option);
                        selectFiltro.append(option);
                    });
                    
                    inicializarSelect2();
                } else {
                    console.warn('No se encontraron áreas');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar áreas:', error);
            }
        });
        
        // Cargar vehículos
        $.ajax({
            url: 'Controlador/ajax_get_vehiculos.php',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(data) {
                if (Array.isArray(data) && data.length > 0) {
                    var select = $('#vehiculo-rapido');
                    select.empty().append('<option value="">Sin vehículo</option>');
                    
                    $.each(data, function(index, item) {
                        var texto = item.Marca + ' ' + item.Modelo;
                        if (item.Placas) {
                            texto += ' (' + item.Placas + ')';
                        }
                        select.append('<option value="' + item.IdVehiculo + '">' + texto + '</option>');
                    });
                    
                    inicializarSelect2();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar vehículos:', error);
            }
        });
    }
    
    // Función para inicializar Select2
    function inicializarSelect2() {
        $('.select2-proveedor, .select2-proveedor-filtro, .select2-area, .select2-area-filtro, .select2-vehiculo, .select2-personal-filtro').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    theme: 'custom-theme',
                    placeholder: $(this).data('placeholder') || 'Seleccionar...',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }
    
    // Evento cambio de proveedor
    $('#proveedor-rapido').on('change', function() {
        var proveedorId = $(this).val();
        currentProveedorId = proveedorId;
        
        if (proveedorId) {
            cargarPersonalProveedor(proveedorId);
        } else {
            $('#personal-proveedor').empty().append('<option value="">Seleccionar personal...</option>').trigger('change');
        }
    });
    
    // Función para cargar personal de proveedor
    function cargarPersonalProveedor(proveedorId) {
        if (!proveedorId) return;
        
        var select = $('#personal-proveedor');
        select.empty().append('<option value="">Cargando personal...</option>');
        select.prop('disabled', true);
        
        $.ajax({
            url: 'Controlador/ajax_get_personal_proveedor.php',
            type: 'GET',
            data: { IdProveedor: proveedorId },
            dataType: 'json',
            timeout: 10000,
            success: function(data) {
                select.empty();
                
                if (Array.isArray(data) && data.length > 0) {
                    select.append('<option value="">Seleccionar personal...</option>');
                    $.each(data, function(index, item) {
                        var nombreCompleto = item.Nombre;
                        if (item.ApPaterno) nombreCompleto += ' ' + item.ApPaterno;
                        if (item.ApMaterno) nombreCompleto += ' ' + item.ApMaterno;
                        
                        select.append('<option value="' + item.IdProveedorPersonal + '">' + nombreCompleto + '</option>');
                    });
                } else {
                    select.append('<option value="">No hay personal registrado</option>');
                }
                
                select.prop('disabled', false);
                select.trigger('change');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar personal:', error);
                select.empty().append('<option value="">Error al cargar personal</option>');
                select.prop('disabled', false);
                showNotification('Error al cargar personal del proveedor', 'error');
            }
        });
    }
    
    // Función para cargar personal para filtro
    function cargarPersonalFiltro() {
        $.ajax({
            url: 'Controlador/ajax_get_personal_todos.php',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(data) {
                var select = $('#filtro-personal');
                select.empty().append('<option value="">Todo el personal</option>');
                
                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function(index, item) {
                        var nombreCompleto = item.Nombre;
                        if (item.ApPaterno) nombreCompleto += ' ' + item.ApPaterno;
                        if (item.ApMaterno) nombreCompleto += ' ' + item.ApMaterno;
                        
                        var texto = nombreCompleto;
                        if (item.NombreProveedor) texto += ' - ' + item.NombreProveedor;
                        
                        select.append('<option value="' + item.IdProveedorPersonal + '">' + texto + '</option>');
                    });
                }
                
                select.trigger('change');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar personal para filtro:', error);
            }
        });
    }
    
    // Inicializar Select2 para personal de proveedor
    $('.select2-personal-proveedor').select2({
        theme: 'custom-theme',
        placeholder: 'Seleccionar personal...',
        allowClear: true,
        width: '100%'
    });
    
    // Evento submit del formulario
    $('#formRegistroRapido').on('submit', function(e) {
        e.preventDefault();
        
        var personalSeleccionado = $('#personal-proveedor').val();
        
        if (!personalSeleccionado || personalSeleccionado.length === 0) {
            showNotification('Debe seleccionar al menos un personal', 'warning');
            return;
        }
        
        var proveedorId = $('#proveedor-rapido').val();
        if (!proveedorId) {
            showNotification('Debe seleccionar un proveedor', 'warning');
            return;
        }
        
        var areaId = $('#area-rapido').val();
        if (!areaId) {
            showNotification('Debe seleccionar un área', 'warning');
            return;
        }
        
        var motivo = $('#motivo-rapido').val().trim();
        if (!motivo) {
            showNotification('Debe ingresar un motivo', 'warning');
            return;
        }
        
        var btn = $(this).find('button[type="submit"]');
        var originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registrando...');
        
        var datosBase = {
            IdProveedor: proveedorId,
            IdDepartamento: areaId,
            FechaVisita: $('#fecha-rapido').val(),
            HoraVisita: $('#hora-rapido').val(),
            IdVehiculo: $('#vehiculo-rapido').val() || null,
            Motivo: motivo
        };
        
        var promesas = personalSeleccionado.map(function(idPersonal) {
            var datosVisita = $.extend({}, datosBase, {
                IdProveedorPersonal: idPersonal
            });
            
            return $.ajax({
                url: 'Controlador/Registrar_Visita_Rapida.php',
                type: 'POST',
                data: datosVisita,
                dataType: 'json',
                timeout: 15000
            });
        });
        
        $.when.apply($, promesas)
            .done(function() {
                var args = Array.prototype.slice.call(arguments);
                var exitosas = 0;
                var ultimoQR = null;
                
                args.forEach(function(respuesta) {
                    if (respuesta && respuesta[0] && respuesta[0].success) {
                        exitosas++;
                        if (respuesta[0].data) {
                            ultimoQR = respuesta[0].data;
                        }
                    }
                });
                
                if (exitosas > 0) {
                    showNotification('Se registraron ' + exitosas + ' visita(s) exitosamente', 'success');
                    cargarVisitas();
                    
                    // Limpiar formulario pero mantener datos comunes
                    $('#motivo-rapido').val('');
                    $('#vehiculo-rapido').val(null).trigger('change');
                    
                    // Mostrar QR de la última visita registrada
                    if (ultimoQR) {
                        mostrarQR(ultimoQR);
                    } else {
                        // Si no hay QR, mostrar el de la última visita registrada
                        $.ajax({
                            url: 'Controlador/Obtener_Ultimo_QR.php',
                            type: 'GET',
                            data: { IdProveedor: proveedorId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success && response.data) {
                                    mostrarQR(response.data);
                                }
                            }
                        });
                    }
                } else {
                    showNotification('Error al registrar las visitas', 'error');
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error en registro:', textStatus, errorThrown);
                showNotification('Error en la conexión: ' + (errorThrown || textStatus), 'error');
            })
            .always(function() {
                btn.prop('disabled', false).html(originalText);
            });
    });
    
    // Función para cargar visitas
    function cargarVisitas() {
        if (loadingVisitas) return;
        loadingVisitas = true;
        
        var filtros = {
            proveedor: $('#filtro-proveedor').val() || '',
            area: $('#filtro-area').val() || '',
            fecha: $('#filtro-fecha').val() || 'hoy',
            fecha_inicio: $('#fecha-inicio').val() || '',
            fecha_fin: $('#fecha-fin').val() || '',
            estatus: $('#filtro-estatus').val() || '',
            personal: $('#filtro-personal').val() || '',
            qr_code: $('#filtro-qr').val() || ''
        };
        
        // Validar fechas personalizadas
        if (filtros.fecha === 'personalizado') {
            if (!filtros.fecha_inicio || !filtros.fecha_fin) {
                showNotification('Seleccione ambas fechas para el filtro personalizado', 'warning');
                loadingVisitas = false;
                return;
            }
            
            if (filtros.fecha_inicio > filtros.fecha_fin) {
                showNotification('La fecha de inicio no puede ser mayor que la fecha fin', 'warning');
                loadingVisitas = false;
                return;
            }
        }
        
        $('#loading').show();
        $('#dataTableVisitas tbody').html('<tr><td colspan="10" class="text-center">Cargando visitas...</td></tr>');
        
        $.ajax({
            url: 'Controlador/Obtener_Visitas.php',
            type: 'GET',
            data: filtros,
            dataType: 'html',
            timeout: 15000,
            success: function(response) {
                $('#loading').hide();
                if (response.trim()) {
                    $('#dataTableVisitas tbody').html(response);
                } else {
                    $('#dataTableVisitas tbody').html('<tr><td colspan="10" class="text-center text-muted">No hay visitas registradas</td></tr>');
                }
                initEvents();
                loadingVisitas = false;
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                console.error('Error al cargar visitas:', error);
                $('#dataTableVisitas tbody').html('<tr><td colspan="10" class="text-center text-danger">Error al cargar visitas: ' + (error || 'Error de conexión') + '</td></tr>');
                showNotification('Error al cargar visitas', 'error');
                loadingVisitas = false;
            }
        });
    }
    
    // Función para mostrar QR
    function mostrarQR(data) {
        if (!data || !data.QrData) {
            showNotification('Datos QR no disponibles', 'error');
            return;
        }
        
        currentQRVisitId = data.IdVisita;
        
        // Limpiar contenedor
        $('#qrCodeContainer').empty();
        
        try {
            // Generar QR
            qrGenerator = new QRCode(document.getElementById("qrCodeContainer"), {
                text: data.QrData,
                width: 250,
                height: 250,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch (error) {
            console.error('Error al generar QR:', error);
            $('#qrCodeContainer').html('<div class="text-danger">Error al generar QR</div>');
            return;
        }
        
        // Mostrar información
        var infoHtml = '<h5 class="mb-2">' + (data.Proveedor || 'Proveedor') + '</h5>';
        infoHtml += '<div class="text-left">';
        infoHtml += '<p class="mb-1"><strong>Personal:</strong> ' + (data.Personal || 'N/A') + '</p>';
        infoHtml += '<p class="mb-1"><strong>Área:</strong> ' + (data.Area || 'N/A') + '</p>';
        infoHtml += '<p class="mb-1"><strong>Fecha:</strong> ' + (data.FechaVisita || 'N/A') + '</p>';
        infoHtml += '<p class="mb-1"><strong>Hora:</strong> ' + (data.HoraVisita || 'N/A') + '</p>';
        infoHtml += '<p class="mb-0"><strong>Motivo:</strong> ' + (data.Motivo || 'N/A') + '</p>';
        infoHtml += '</div>';
        
        $('#qrInfo').html(infoHtml);
        
        // Mostrar expiración
        if (data.FechaExpiracion) {
            var fechaExpiracion = new Date(data.FechaExpiracion);
            var ahora = new Date();
            var diffMs = fechaExpiracion - ahora;
            var diffMin = Math.floor(diffMs / 60000);
            
            var mensaje = '<i class="fas fa-clock mr-1"></i>';
            if (diffMin > 0) {
                mensaje += ' Válido por ' + diffMin + ' minutos más';
            } else if (diffMin === 0) {
                mensaje += ' Expira en menos de 1 minuto';
            } else {
                mensaje += ' EXPIRADO';
            }
            
            $('#qrExpiration').html(mensaje).removeClass('text-muted').addClass(diffMin > 0 ? 'text-success' : 'text-danger');
        } else {
            $('#qrExpiration').html('').addClass('text-muted');
        }
        
        // Mostrar modal
        $('#qrModal').modal('show');
    }
    
    // Eventos de QR
    $('#btn-enviar-qr').on('click', function() {
        if (!currentQRVisitId) {
            showNotification('No hay QR activo para enviar', 'warning');
            return;
        }
        
        var btn = $(this);
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
        
        $.ajax({
            url: 'Controlador/Enviar_QR.php',
            type: 'POST',
            data: { IdVisita: currentQRVisitId },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    showNotification('QR enviado exitosamente', 'success');
                } else {
                    showNotification(response.message || 'Error al enviar QR', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al enviar QR:', error);
                showNotification('Error al enviar QR: ' + (error || 'Error de conexión'), 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    $('#btn-descargar-qr').on('click', function() {
        var canvas = document.querySelector('#qrCodeContainer canvas');
        if (canvas) {
            try {
                var link = document.createElement('a');
                link.download = 'QR_Visita_' + (currentQRVisitId || '') + '.png';
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showNotification('QR descargado exitosamente', 'success');
            } catch (error) {
                console.error('Error al descargar QR:', error);
                showNotification('Error al descargar QR', 'error');
            }
        } else {
            showNotification('No hay QR para descargar', 'warning');
        }
    });
    
    $('#btn-imprimir-qr').on('click', function() {
        var qrContent = document.getElementById('qrCodeContainer').innerHTML;
        var qrInfo = document.getElementById('qrInfo').innerHTML;
        var qrExpiration = document.getElementById('qrExpiration').innerHTML;
        
        if (!qrContent) {
            showNotification('No hay QR para imprimir', 'warning');
            return;
        }
        
        var printWindow = window.open('', '_blank', 'width=600,height=800');
        if (!printWindow) {
            showNotification('Por favor, permita ventanas emergentes para imprimir', 'warning');
            return;
        }
        
        printWindow.document.write(
            "<html><head><title>Imprimir QR - Visita</title>" +
            "<style>" +
            "body { font-family: Arial, sans-serif; text-align: center; padding: 40px; }" +
            ".qr-container { margin: 30px auto; padding: 20px; border: 2px solid #d94f00; border-radius: 10px; display: inline-block; background: white; }" +
            ".qr-info { margin: 20px 0; text-align: left; max-width: 300px; margin-left: auto; margin-right: auto; }" +
            ".qr-info p { margin: 5px 0; }" +
            ".qr-expiration { margin-top: 20px; padding: 10px; border-radius: 5px; }" +
            ".qr-expiration.success { background-color: #d4edda; color: #155724; }" +
            ".qr-expiration.danger { background-color: #f8d7da; color: #721c24; }" +
            "</style>" +
            "</head><body>" +
            "<h2 style='color: #d94f00;'>Código QR de Acceso</h2>" +
            "<div class='qr-container'>" + qrContent + "</div>" +
            "<div class='qr-info'>" + qrInfo + "</div>" +
            "<div class='qr-expiration'>" + qrExpiration + "</div>" +
            "<p style='margin-top: 30px; color: #666; font-size: 12px;'>Generado el: " + new Date().toLocaleString() + "</p>" +
            "</body></html>"
        );
        
        printWindow.document.close();
        
        // Esperar a que se cargue el contenido
        setTimeout(function() {
            printWindow.focus();
            printWindow.print();
        }, 500);
    });
    
    // Función para generar credencial PDF
    function generarCredencialPDF(IdPersonal, nombre) {
        if (confirm('¿Generar credencial PDF para ' + (nombre || 'el personal') + '?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'Controlador/generar_credencial_pdf.php';
            form.target = '_blank';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'IdPersonal';
            input.value = IdPersonal;
            form.appendChild(input);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
            
            showNotification('Generando credencial...', 'info');
        }
    }
    
    // Función para inicializar eventos de la tabla
    function initEvents() {
        // Botones de acciones
        $(document).off('click', '.btn-ver-detalles').on('click', '.btn-ver-detalles', function() {
            var idVisita = $(this).data('id');
            if (idVisita) {
                cargarModal('Modales/Detalle_Visita.php?IdVisita=' + idVisita, '#DetalleVisitaModal');
            } else {
                showNotification('ID de visita no válido', 'error');
            }
        });
        
        $(document).off('click', '.btn-ver-qr, .qr-mini').on('click', '.btn-ver-qr, .qr-mini', function() {
            var idVisita = $(this).data('id');
            if (!idVisita) {
                showNotification('ID de visita no válido', 'error');
                return;
            }
            
            $.ajax({
                url: 'Controlador/Obtener_QR_Visita.php',
                type: 'POST',
                data: { IdVisita: idVisita },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success && response.data) {
                        mostrarQR(response.data);
                    } else {
                        showNotification(response.message || 'No se pudo obtener el QR', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener QR:', error);
                    showNotification('Error al obtener QR', 'error');
                }
            });
        });
        
        $(document).off('click', '.btn-reenviar-qr').on('click', '.btn-reenviar-qr', function() {
            var idVisita = $(this).data('id');
            var proveedor = $(this).closest('tr').find('td:nth-child(3)').text() || 'proveedor';
            
            if (!idVisita) {
                showNotification('ID de visita no válido', 'error');
                return;
            }
            
            if (confirm('¿Reenviar QR a ' + proveedor + '?')) {
                $.ajax({
                    url: 'Controlador/Reenviar_QR.php',
                    type: 'POST',
                    data: { IdVisita: idVisita },
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        if (response.success) {
                            showNotification('QR reenviado exitosamente', 'success');
                        } else {
                            showNotification(response.message || 'Error al reenviar QR', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al reenviar QR:', error);
                        showNotification('Error al reenviar QR', 'error');
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-modificar').on('click', '.btn-modificar', function() {
            var idVisita = $(this).data('id');
            if (idVisita) {
                cargarModal('Modales/Modificar_Visita.php?IdVisita=' + idVisita, '#ModificarVisitaModal');
            } else {
                showNotification('ID de visita no válido', 'error');
            }
        });
        
        $(document).off('click', '.btn-cancelar').on('click', '.btn-cancelar', function() {
            var idVisita = $(this).data('id');
            var proveedor = $(this).closest('tr').find('td:nth-child(3)').text() || 'proveedor';
            
            if (!idVisita) {
                showNotification('ID de visita no válido', 'error');
                return;
            }
            
            if (confirm('¿Cancelar visita de ' + proveedor + '?')) {
                $.ajax({
                    url: 'Controlador/Cancelar_Visita.php',
                    type: 'POST',
                    data: { IdVisita: idVisita },
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        if (response.success) {
                            showNotification('Visita cancelada exitosamente', 'success');
                            cargarVisitas();
                        } else {
                            showNotification(response.message || 'Error al cancelar visita', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cancelar visita:', error);
                        showNotification('Error al cancelar visita', 'error');
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-completar').on('click', '.btn-completar', function() {
            var idVisita = $(this).data('id');
            if (!idVisita) {
                showNotification('ID de visita no válido', 'error');
                return;
            }
            
            $.ajax({
                url: 'Controlador/Completar_Visita.php',
                type: 'POST',
                data: { IdVisita: idVisita },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    if (response.success) {
                        showNotification('Visita completada exitosamente', 'success');
                        cargarVisitas();
                    } else {
                        showNotification(response.message || 'Error al completar visita', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al completar visita:', error);
                    showNotification('Error al completar visita', 'error');
                }
            });
        });
        
        $(document).off('click', '.btn-eliminar').on('click', '.btn-eliminar', function() {
            var idVisita = $(this).data('id');
            var proveedor = $(this).closest('tr').find('td:nth-child(3)').text() || 'proveedor';
            
            if (!idVisita) {
                showNotification('ID de visita no válido', 'error');
                return;
            }
            
            if (confirm('¿Está seguro de eliminar la visita de ' + proveedor + '?\nEsta acción no se puede deshacer.')) {
                $.ajax({
                    url: 'Controlador/Eliminar_Visita.php',
                    type: 'POST',
                    data: { IdVisita: idVisita },
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        if (response.success) {
                            showNotification('Visita eliminada exitosamente', 'success');
                            cargarVisitas();
                        } else {
                            showNotification(response.message || 'Error al eliminar visita', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al eliminar visita:', error);
                        showNotification('Error al eliminar visita', 'error');
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-cambiar-estatus').on('click', '.btn-cambiar-estatus', function() {
            var idVisita = $(this).data('id');
            var estatusActual = $(this).data('estatus');
            var proveedor = $(this).closest('tr').find('td:nth-child(3)').text() || 'proveedor';
            
            if (!idVisita) {
                showNotification('ID de visita no válido', 'error');
                return;
            }
            
            var nuevoEstatus = estatusActual === 'activo' ? 'pendiente' : 'activo';
            
            if (confirm('¿Cambiar estatus de la visita de ' + proveedor + ' a "' + nuevoEstatus + '"?')) {
                $.ajax({
                    url: 'Controlador/Cambiar_Estatus_Visita.php',
                    type: 'POST',
                    data: { 
                        IdVisita: idVisita,
                        Estatus: nuevoEstatus
                    },
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        if (response.success) {
                            showNotification('Estatus actualizado exitosamente', 'success');
                            cargarVisitas();
                        } else {
                            showNotification(response.message || 'Error al cambiar estatus', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cambiar estatus:', error);
                        showNotification('Error al cambiar estatus', 'error');
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-generar-credencial').on('click', '.btn-generar-credencial', function() {
            var IdPersonal = $(this).data('id');
            var nombre = $(this).data('nombre');
            if (IdPersonal) {
                generarCredencialPDF(IdPersonal, nombre);
            } else {
                showNotification('ID de personal no válido', 'error');
            }
        });
    }
    
    // Función para cargar modales
    function cargarModal(url, modalId) {
        if (!url) {
            showNotification('URL no válida', 'error');
            return;
        }
        
        var modalContainer = $('#modal-container');
        
        // Verificar si el modal ya existe
        if ($(modalId).length) {
            $(modalId).modal('show');
            return;
        }
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            timeout: 10000,
            success: function(response) {
                modalContainer.html(response);
                // Buscar el script en la respuesta y ejecutarlo
                var scriptRegex = /<script\b[^>]*>([\s\S]*?)<\/script>/gm;
                var match;
                while ((match = scriptRegex.exec(response)) !== null) {
                    if (match[1]) {
                        eval(match[1]);
                    }
                }
                $(modalId).modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar modal:', error);
                showNotification('Error al cargar el modal', 'error');
            }
        });
    }
    
    // Eventos de botones principales
    $('#btn-nuevo-proveedor').on('click', function() {
        cargarModal('Modales/Nuevo_Proveedor.php', '#NuevoProveedorModal');
    });
    
    $('#btn-nuevo-personal').on('click', function() {
        cargarModal('Modales/Nuevo_Personal.php', '#NuevoPersonalModal');
    });
    
    $('#btn-ver-qr-activo').on('click', function() {
        $.ajax({
            url: 'Controlador/Obtener_QR_Activo.php',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success && response.data) {
                    mostrarQR(response.data);
                } else {
                    showNotification('No hay visitas activas con QR disponible', 'info');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener QR activo:', error);
                showNotification('Error al obtener QR activo', 'error');
            }
        });
    });
    
    $('#btn-export-excel').on('click', function() {
        exportarExcel();
    });
    
    $('#btn-export-pdf').on('click', function() {
        showNotification('Función de exportación PDF en desarrollo', 'info');
    });
    
    $('#btn-print').on('click', function() {
        imprimirTabla();
    });
    
    $('#btn-refresh').on('click', function() {
        cargarVisitas();
        showNotification('Datos recargados', 'success');
    });
    
    // Eventos de filtros
    $('#filtro-fecha').on('change', function() {
        if ($(this).val() === 'personalizado') {
            $('#rango-fechas-container, #rango-fechas-hasta-container').show();
        } else {
            $('#rango-fechas-container, #rango-fechas-hasta-container').hide();
        }
    });
    
    $('#btn-aplicar-filtros').on('click', function() {
        cargarVisitas();
    });
    
    $('#btn-limpiar-filtros').on('click', function() {
        $('#filtro-proveedor, #filtro-area, #filtro-personal').val(null).trigger('change');
        $('#filtro-fecha').val('hoy');
        $('#filtro-estatus').val('');
        $('#filtro-qr').val('');
        $('#rango-fechas-container, #rango-fechas-hasta-container').hide();
        cargarVisitas();
        showNotification('Filtros limpiados', 'info');
    });
    
    $('#filtro-qr').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            cargarVisitas();
        }
    });
    
    // Función para exportar a Excel
    function exportarExcel() {
        var tbody = $('#dataTableVisitas tbody');
        if (tbody.find('tr').length === 0 || tbody.find('tr').hasClass('no-data')) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        var table = $('#dataTableVisitas').clone();
        table.find('.btn, .badge, .qr-mini, .fa').remove();
        table.find('td').each(function() {
            var text = $(this).text().trim();
            $(this).text(text);
        });
        
        var html = '<html><head><meta charset="UTF-8"></head><body>';
        html += '<h2 style="color: #d94f00;">Reporte de Visitas de Proveedores</h2>';
        html += '<p>Fecha de generación: ' + new Date().toLocaleString() + '</p>';
        html += table.prop('outerHTML');
        html += '</body></html>';
        
        var blob = new Blob([html], {type: 'application/vnd.ms-excel;charset=utf-8'});
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'visitas_proveedores_' + new Date().toISOString().split('T')[0] + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        showNotification('Excel generado exitosamente', 'success');
    }
    
    // Función para imprimir tabla
    function imprimirTabla() {
        var tbody = $('#dataTableVisitas tbody');
        if (tbody.find('tr').length === 0 || tbody.find('tr').hasClass('no-data')) {
            showNotification('No hay datos para imprimir', 'warning');
            return;
        }
        
        var printContent = $('#resultados-visitas').html();
        var printWindow = window.open('', '_blank', 'width=800,height=600');
        if (!printWindow) {
            showNotification('Por favor, permita ventanas emergentes para imprimir', 'warning');
            return;
        }
        
        var styles = $('link[rel="stylesheet"]').map(function() {
            return '<link rel="stylesheet" href="' + $(this).attr('href') + '">';
        }).get().join('');
        
        printWindow.document.write(
            "<html><head><title>Reporte de Visitas de Proveedores</title>" +
            styles +
            "<style>body { padding: 20px; } .btn, .badge, .qr-mini { display: none; } .table { width: 100%; }</style>" +
            "</head><body>" +
            "<h2 style='color: #d94f00;'>Reporte de Visitas de Proveedores</h2>" +
            "<p>Fecha de generación: " + new Date().toLocaleString() + "</p>" +
            printContent +
            "</body></html>"
        );
        
        printWindow.document.close();
        
        setTimeout(function() {
            printWindow.focus();
            printWindow.print();
        }, 500);
    }
    
    // Inicializar
    cargarDatosSelects();
    cargarPersonalFiltro();
    cargarVisitas();
    
    // Recargar datos cada 60 segundos
    setInterval(function() {
        cargarVisitas();
    }, 60000);
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

/* Estilos de badges */
.badge { 
    padding: 4px 10px; 
    border-radius: 12px; 
    font-size: 11px; 
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success { background-color: var(--success-color); color: white; }
.badge-warning { background-color: var(--warning-color); color: #212529; }
.badge-info { background-color: var(--info-color); color: white; }
.badge-danger { background-color: var(--danger-color); color: white; }
.badge-secondary { background-color: var(--secondary-color); color: white; }
.badge-primary { background-color: var(--primary-orange); color: white; }

/* Estilos de Select2 */
.select2-container--custom-theme {
    width: 100% !important;
}

.select2-container--custom-theme .select2-selection--single,
.select2-container--custom-theme .select2-selection--multiple {
    min-height: 38px;
    border: 2px solid var(--border-color) !important;
    border-radius: 8px !important;
}

.select2-container--custom-theme.select2-container--focus .select2-selection--single,
.select2-container--custom-theme.select2-container--focus .select2-selection--multiple {
    border-color: var(--primary-orange) !important;
    box-shadow: 0 0 0 3px rgba(217, 79, 0, 0.1);
}

.select2-dropdown.select2-dropdown-enhanced {
    border: 2px solid var(--primary-orange);
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-top: 4px;
}

/* Estilos de tabla */
.table th {
    background-color: var(--primary-orange);
    color: white;
    border-color: var(--primary-orange-dark);
    text-align: center;
    vertical-align: middle;
    font-weight: 600;
    padding: 0.75rem;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: var(--table-striped);
}

.table tbody tr:hover {
    background-color: var(--table-hover);
}

/* QR mini */
.qr-mini {
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.qr-mini:hover {
    transform: scale(1.1);
    border-color: var(--primary-orange);
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

/* Filtros */
#filtrosHeader {
    transition: background-color 0.2s;
}

#filtrosHeader:hover {
    background-color: var(--primary-orange-dark) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .col-md-2, .col-md-3 {
        margin-bottom: 15px;
    }
    
    .btn-group {
        flex-wrap: wrap;
    }
    
    .btn-group .btn {
        flex: 1;
        margin-bottom: 5px;
        font-size: 12px !important;
    }
    
    .qr-mini {
        width: 30px;
        height: 30px;
    }
    
    .notification-toast {
        left: 10px !important;
        right: 10px !important;
        min-width: auto !important;
        max-width: none !important;
    }
}
</style>
<?php
include_once '../templates/footer.php';
?>