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
                    <div class="col-sm-6 text-right">
                        <a href="gestion_proveedores.php" class="btn btn-primary" 
                        style="background-color: #d94f00; border-color: #d94f00;">
                            <i class="fas fa-users-cog"></i> Gestionar Proveedores
                        </a>
                    </div>
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
    var filtrosExpandidos = true;
    var currentQRVisitId = null;
    var qrGenerator = null;
    var currentProveedorId = null;
    
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
    
    $('#filtrosHeader').click(toggleFiltros);
    
    function showNotification(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 
                         type === 'error' ? 'alert-danger' : 
                         type === 'warning' ? 'alert-warning' : 'alert-info';
        
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" 
                 style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(alertHtml);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }
    
    function cargarDatosSelects() {
        $.ajax({
            url: 'Controlador/ajax_get_proveedores.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (Array.isArray(data)) {
                    var selectRapido = $('#proveedor-rapido');
                    var selectFiltro = $('#filtro-proveedor');
                    selectRapido.empty().append('<option value="">Buscar proveedor...</option>');
                    selectFiltro.empty().append('<option value="">Todos los proveedores</option>');
                    
                    $.each(data, function(index, item) {
                        var option = '<option value="' + item.IdProveedor + '">' + 
                                   item.NombreProveedor + ' - ' + (item.Email || '') + '</option>';
                        selectRapido.append(option);
                        selectFiltro.append(option);
                    });
                    
                    $('.select2-proveedor').select2({
                        theme: 'custom-theme',
                        placeholder: 'Buscar proveedor...',
                        allowClear: true,
                        width: '100%'
                    });
                    
                    $('.select2-proveedor-filtro').select2({
                        theme: 'custom-theme',
                        placeholder: 'Todos los proveedores',
                        allowClear: true,
                        width: '100%'
                    });
                }
            }
        });
        
        $.ajax({
            url: 'Controlador/ajax_get_areas.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (Array.isArray(data)) {
                    var selectRapido = $('#area-rapido');
                    var selectFiltro = $('#filtro-area');
                    selectRapido.empty().append('<option value="">Seleccionar área</option>');
                    selectFiltro.empty().append('<option value="">Todas las áreas</option>');
                    
                    $.each(data, function(index, item) {
                        var option = '<option value="' + item.IdDepartamento + '">' + item.NombreArea + '</option>';
                        selectRapido.append(option);
                        selectFiltro.append(option);
                    });
                    
                    $('.select2-area, .select2-area-filtro').select2({
                        theme: 'custom-theme',
                        placeholder: 'Seleccionar área...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            }
        });
        
        $.ajax({
            url: 'Controlador/ajax_get_vehiculos.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (Array.isArray(data)) {
                    var select = $('#vehiculo-rapido');
                    select.empty().append('<option value="">Sin vehículo</option>');
                    
                    $.each(data, function(index, item) {
                        var texto = item.Marca + ' ' + item.Modelo;
                        if (item.Placas) {
                            texto += ' (' + item.Placas + ')';
                        }
                        select.append('<option value="' + item.IdVehiculo + '">' + texto + '</option>');
                    });
                    
                    $('.select2-vehiculo').select2({
                        theme: 'custom-theme',
                        placeholder: 'Seleccionar vehículo...',
                        allowClear: true,
                        width: '100%'
                    });
                }
            }
        });
    }
    
    $('#proveedor-rapido').change(function() {
        var proveedorId = $(this).val();
        if (proveedorId) {
            cargarPersonalProveedor(proveedorId);
        } else {
            $('#personal-proveedor').empty().append('<option value="">Seleccionar personal...</option>');
        }
    });
    
    function cargarPersonalProveedor(proveedorId) {
        $.ajax({
            url: 'Controlador/ajax_get_personal_proveedor.php',
            type: 'GET',
            data: { IdProveedor: proveedorId },
            dataType: 'json',
            success: function(data) {
                var select = $('#personal-proveedor');
                select.empty().append('<option value="">Seleccionar personal...</option>');
                
                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function(index, item) {
                        var nombreCompleto = item.Nombre;
                        if (item.ApPaterno) nombreCompleto += ' ' + item.ApPaterno;
                        if (item.ApMaterno) nombreCompleto += ' ' + item.ApMaterno;
                        
                        select.append('<option value="' + item.IdProveedorPersonal + '">' + nombreCompleto + '</option>');
                    });
                } else {
                    select.append('<option value="">No hay personal registrado</option>');
                }
                
                cargarPersonalFiltro();
            }
        });
    }
    
    function cargarPersonalFiltro() {
        $.ajax({
            url: 'Controlador/ajax_get_personal_todos.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var select = $('#filtro-personal');
                select.empty().append('<option value="">Todo el personal</option>');
                
                if (Array.isArray(data) && data.length > 0) {
                    $.each(data, function(index, item) {
                        var nombreCompleto = item.Nombre;
                        if (item.ApPaterno) nombreCompleto += ' ' + item.ApPaterno;
                        if (item.ApMaterno) nombreCompleto += ' ' + item.ApMaterno;
                        
                        select.append('<option value="' + item.IdProveedorPersonal + '">' + nombreCompleto + ' - ' + (item.NombreProveedor || '') + '</option>');
                    });
                }
                
                $('.select2-personal-filtro').select2({
                    theme: 'custom-theme',
                    placeholder: 'Todo el personal',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    }
    
    $('.select2-personal-proveedor').select2({
        theme: 'custom-theme',
        placeholder: 'Seleccionar personal...',
        allowClear: true,
        width: '100%'
    });
    
    $('#formRegistroRapido').submit(function(e) {
    e.preventDefault();
    
    var personalSeleccionado = $('#personal-proveedor').val();
    
    if (!personalSeleccionado || personalSeleccionado.length === 0) {
        showNotification('Debe seleccionar al menos un personal', 'warning');
        return;
    }
    
    var formData = $(this).serialize();
    var btn = $(this).find('button[type="submit"]');
    var originalText = btn.html();
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    var promesas = personalSeleccionado.map(function(idPersonal) {
        var datosVisita = {
            IdProveedor: $('#proveedor-rapido').val(),
            IdProveedorPersonal: idPersonal,
            IdDepartamento: $('#area-rapido').val(),
            FechaVisita: $('#fecha-rapido').val(),
            HoraVisita: $('#hora-rapido').val(),
            IdVehiculo: $('#vehiculo-rapido').val(),
            Motivo: $('#motivo-rapido').val()
        };
        
        return $.ajax({
            url: 'Controlador/Registrar_Visita_Rapida.php',
            type: 'POST',
            data: datosVisita,
            dataType: 'json'
        });
    });
    
    $.when.apply($, promesas).done(function() {
        var args = Array.prototype.slice.call(arguments);
        var exitosas = args.filter(function(respuesta) {
            return respuesta[0].success;
        }).length;
        
        if (exitosas > 0) {
            showNotification('Se registraron ' + exitosas + ' visita(s) exitosamente', 'success');
            cargarVisitas();
            
            if (args[0][0].success && args[0][0].data) {
                mostrarQR(args[0][0].data);
            }
        } else {
            showNotification('Error al registrar las visitas', 'error');
        }
    }).fail(function() {
        showNotification('Error en la conexión', 'error');
    }).always(function() {
        btn.prop('disabled', false).html(originalText);
    });
});
    
    function cargarVisitas() {
        var filtros = {
            proveedor: $('#filtro-proveedor').val(),
            area: $('#filtro-area').val(),
            fecha: $('#filtro-fecha').val(),
            fecha_inicio: $('#fecha-inicio').val(),
            fecha_fin: $('#fecha-fin').val(),
            estatus: $('#filtro-estatus').val(),
            personal: $('#filtro-personal').val(),
            qr_code: $('#filtro-qr').val()
        };
        
        $('#loading').show();
        
        $.ajax({
            url: 'Controlador/Obtener_Visitas.php',
            type: 'GET',
            data: filtros,
            success: function(response) {
                $('#loading').hide();
                $('#dataTableVisitas tbody').html(response);
                initEvents();
                showNotification('Visitas cargadas', 'success');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar visitas: ' + error, 'error');
            }
        });
    }
    
    function mostrarQR(data) {
        currentQRVisitId = data.IdVisita;
        
        $('#qrCodeContainer').empty();
        
        qrGenerator = new QRCode(document.getElementById("qrCodeContainer"), {
            text: data.QrData,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        $('#qrInfo').html(`
            <h5>${data.Proveedor}</h5>
            <p><strong>Personal:</strong> ${data.Personal}<br>
            <strong>Área:</strong> ${data.Area}<br>
            <strong>Fecha:</strong> ${data.FechaVisita}<br>
            <strong>Hora:</strong> ${data.HoraVisita}</p>
        `);
        
        $('#qrExpiration').html('<i class="fas fa-clock"></i> Válido hasta: ' + data.FechaExpiracion);
        
        $('#qrModal').modal('show');
    }
    
    $('#btn-enviar-qr').click(function() {
        if (!currentQRVisitId) return;
        
        var btn = $(this);
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
        
        $.ajax({
            url: 'Controlador/Enviar_QR.php',
            type: 'POST',
            data: { IdVisita: currentQRVisitId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification('QR enviado al proveedor', 'success');
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error al enviar QR', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    $('#btn-gestion-proveedores').click(function() {
        cargarModal('Modales/Gestion_Proveedores.php', '#GestionProveedoresModal');
    });
    
    $('#btn-nuevo-proveedor').click(function() {
        cargarModal('Modales/Nuevo_Proveedor.php', '#NuevoProveedorModal');
    });
    
    $('#btn-nuevo-personal').click(function() {
        cargarModal('Modales/Nuevo_Personal.php', '#NuevoPersonalModal');
    });
    
    $('#btn-descargar-qr').click(function() {
        var canvas = document.querySelector('#qrCodeContainer canvas');
        if (canvas) {
            var link = document.createElement('a');
            link.download = 'QR_Visita_' + currentQRVisitId + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
            showNotification('QR descargado', 'success');
        }
    });
    
    $('#btn-imprimir-qr').click(function() {
        var printWindow = window.open('', '_blank');
        var qrContent = document.getElementById('qrCodeContainer').innerHTML;
        var qrInfo = document.getElementById('qrInfo').innerHTML;
        
        printWindow.document.write(
            "<html>" +
            "<head>" +
            "<title>Imprimir QR</title>" +
            "<style>" +
            "body { text-align: center; padding: 20px; }" +
            ".qr-container { margin: 20px auto; }" +
            ".qr-info { margin: 20px 0; }" +
            "</style>" +
            "</head>" +
            "<body>" +
            "<h3>Código QR de Acceso</h3>" +
            "<div class='qr-container'>" + qrContent + "</div>" +
            "<div class='qr-info'>" + qrInfo + "</div>" +
            "</body>" +
            "</html>"
        );
        
        printWindow.document.close();
        printWindow.print();
    });
    
    function generarCredencialPDF(IdPersonal, nombre) {
        if (confirm('¿Generar credencial PDF para ' + nombre + '?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'Controlador/generar_credencial_pdf.php';
            form.style.display = 'none';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'IdPersonal';
            input.value = IdPersonal;
            form.appendChild(input);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    }
    
    function initEvents() {
        $(document).off('click', '.btn-ver-detalles').on('click', '.btn-ver-detalles', function() {
            var idVisita = $(this).data('id');
            cargarModal('Modales/Detalle_Visita.php?IdVisita=' + idVisita, '#DetalleVisitaModal');
        });
        
        $(document).off('click', '.btn-ver-qr, .qr-mini').on('click', '.btn-ver-qr, .qr-mini', function() {
            var idVisita = $(this).data('id');
            
            $.ajax({
                url: 'Controlador/Obtener_QR_Visita.php',
                type: 'POST',
                data: { IdVisita: idVisita },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarQR(response.data);
                    }
                }
            });
        });
        
        $(document).off('click', '.btn-reenviar-qr').on('click', '.btn-reenviar-qr', function() {
            var idVisita = $(this).data('id');
            var proveedor = $(this).closest('tr').find('td:nth-child(3)').text();
            
            if (confirm('¿Reenviar QR a ' + proveedor + '?')) {
                $.ajax({
                    url: 'Controlador/Reenviar_QR.php',
                    type: 'POST',
                    data: { IdVisita: idVisita },
                    dataType: 'json',
                    success: function(response) {
                        showNotification(response.message, response.success ? 'success' : 'error');
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-modificar').on('click', '.btn-modificar', function() {
            var idVisita = $(this).data('id');
            cargarModal('Modales/Modificar_Visita.php?IdVisita=' + idVisita, '#ModificarVisitaModal');
        });
        
        $(document).off('click', '.btn-cancelar').on('click', '.btn-cancelar', function() {
            var idVisita = $(this).data('id');
            var proveedor = $(this).closest('tr').find('td:nth-child(3)').text();
            
            if (confirm('¿Cancelar visita de ' + proveedor + '?')) {
                $.ajax({
                    url: 'Controlador/Cancelar_Visita.php',
                    type: 'POST',
                    data: { IdVisita: idVisita },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showNotification('Visita cancelada', 'success');
                            cargarVisitas();
                        }
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-completar').on('click', '.btn-completar', function() {
            var idVisita = $(this).data('id');
            
            $.ajax({
                url: 'Controlador/Completar_Visita.php',
                type: 'POST',
                data: { IdVisita: idVisita },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification('Visita completada', 'success');
                        cargarVisitas();
                    }
                }
            });
        });
        
        $(document).off('click', '.btn-eliminar').on('click', '.btn-eliminar', function() {
            var idVisita = $(this).data('id');
            var proveedor = $(this).closest('tr').find('td:nth-child(3)').text();
            
            if (confirm('¿Está seguro de eliminar la visita de ' + proveedor + '?\nEsta acción no se puede deshacer.')) {
                $.ajax({
                    url: 'Controlador/Eliminar_Visita.php',
                    type: 'POST',
                    data: { IdVisita: idVisita },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showNotification('Visita eliminada', 'success');
                            cargarVisitas();
                        } else {
                            showNotification(response.message, 'error');
                        }
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-cambiar-estatus').on('click', '.btn-cambiar-estatus', function() {
            var idVisita = $(this).data('id');
            var estatusActual = $(this).data('estatus');
            var proveedor = $(this).closest('tr').find('td:nth-child(3)').text();
            
            if (confirm('¿Cambiar estatus de la visita de ' + proveedor + '?')) {
                $.ajax({
                    url: 'Controlador/Cambiar_Estatus_Visita.php',
                    type: 'POST',
                    data: { 
                        IdVisita: idVisita,
                        Estatus: estatusActual === 'activo' ? 'pendiente' : 'activo'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showNotification('Estatus actualizado', 'success');
                            cargarVisitas();
                        }
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-generar-credencial').on('click', '.btn-generar-credencial', function() {
            var IdPersonal = $(this).data('id');
            var nombre = $(this).data('nombre');
            generarCredencialPDF(IdPersonal, nombre);
        });
    }
    
    function cargarModal(url, modalId) {
        $('#modal-container').load(url, function() {
            $(modalId).modal('show');
        });
    }
    
    $('#btn-ver-qr-activo').click(function() {
        $.ajax({
            url: 'Controlador/Obtener_QR_Activo.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    mostrarQR(response.data);
                } else {
                    showNotification('No hay visitas activas', 'info');
                }
            }
        });
    });
    
    $('#btn-export-excel').click(function() {
        exportarExcel();
    });
    
    $('#btn-export-pdf').click(function() {
        exportarPDF();
    });
    
    $('#btn-print').click(function() {
        imprimirTabla();
    });
    
    $('#btn-refresh').click(function() {
        cargarVisitas();
        showNotification('Datos recargados', 'success');
    });
    
    $('#filtro-fecha').change(function() {
        if ($(this).val() === 'personalizado') {
            $('#rango-fechas-container, #rango-fechas-hasta-container').show();
        } else {
            $('#rango-fechas-container, #rango-fechas-hasta-container').hide();
        }
    });
    
    $('#btn-aplicar-filtros').click(function() {
        cargarVisitas();
    });
    
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-proveedor, #filtro-area, #filtro-personal').val(null).trigger('change');
        $('#filtro-fecha').val('hoy');
        $('#filtro-estatus').val('');
        $('#filtro-qr').val('');
        $('#rango-fechas-container, #rango-fechas-hasta-container').hide();
        cargarVisitas();
        showNotification('Filtros limpiados', 'info');
    });
    
    $('#filtro-qr').keypress(function(e) {
        if (e.which == 13) {
            cargarVisitas();
        }
    });
    
    function exportarExcel() {
        if ($('#dataTableVisitas tbody tr').length === 0 || 
            $('#dataTableVisitas tbody tr').hasClass('no-data')) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        var table = $('#dataTableVisitas').clone();
        table.find('.btn, .badge, .qr-mini').remove();
        
        var html = '<table>' + table.html() + '</table>';
        var blob = new Blob([html], {type: 'application/vnd.ms-excel'});
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'visitas_proveedores_' + new Date().toISOString().split('T')[0] + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        showNotification('Excel generado', 'success');
    }
    
    function exportarPDF() {
        showNotification('Exportación PDF en desarrollo', 'info');
    }
    
    function imprimirTabla() {
        var printContent = $('#resultados-visitas').html();
        var originalContent = $('body').html();
        
        $('body').html('<div class="container-fluid">' + printContent + '</div>');
        window.print();
        $('body').html(originalContent);
        cargarDatosSelects();
        showNotification('Listo para imprimir', 'info');
    }
    
    cargarDatosSelects();
    cargarVisitas();
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

.badge-success { background-color: var(--success-color); color: white; }
.badge-warning { background-color: var(--warning-color); color: #212529; }
.badge-info { background-color: var(--info-color); color: white; }
.badge-danger { background-color: var(--danger-color); color: white; }
.badge-secondary { background-color: var(--secondary-color); color: white; }
.badge-primary { background-color: var(--primary-orange); color: white; }

.select2-container--custom-theme {
    width: 100% !important;
}

.select2-container--custom-theme .select2-selection--single {
    min-height: 42px;
    border: 2px solid var(--border-color) !important;
    border-radius: 8px !important;
}

.select2-container--custom-theme.select2-container--focus .select2-selection--single {
    border-color: var(--primary-orange) !important;
    box-shadow: 0 0 0 3px rgba(217, 79, 0, 0.1);
}

.select2-dropdown.select2-dropdown-enhanced {
    border: 2px solid var(--primary-orange);
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-top: 4px;
}

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

.qr-mini {
    width: 40px;
    height: 40px;
    display: inline-block;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    transition: transform 0.2s;
}

.qr-mini:hover {
    transform: scale(1.1);
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

#filtrosHeader {
    transition: background-color 0.2s;
}

#filtrosHeader:hover {
    background-color: var(--primary-orange-dark) !important;
}

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
}
</style>
<?php
include_once '../templates/footer.php';
?>