<?php
include_once '../templates/head.php';
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
                                <label class="form-label">No. Identificación:</label>
                                <input type="text" id="filtro-numeroIdentificacion" class="form-control form-control-lg" placeholder="Ej: EXT-001">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Nombre:</label>
                                <input type="text" id="filtro-nombre" class="form-control form-control-lg" placeholder="Buscar por nombre">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Cargo:</label>
                                <select id="filtro-cargo" class="form-control form-control-lg select2-cargo" style="width: 100%;">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Área de Visita:</label>
                                <select id="filtro-areaVisita" class="form-control form-control-lg select2-areaVisita" style="width: 100%;">
                                    <option value="">Todas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Personal Responsable:</label>
                                <select id="filtro-personalResponsable" class="form-control form-control-lg select2-personalResponsable" style="width: 100%;">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Estatus:</label>
                                <select id="filtro-estatus" class="form-control form-control-lg">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                    <option value="2">Baja</option>
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
                                <button type="button" class="btn btn-primary" id="btn-nuevo" style="background-color: #d94f00; border-color: #d94f00;">
                                    <i class="fas fa-plus mr-1"></i> Nuevo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem;">
                    <h3 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-users mr-2"></i>Lista de Personal Externo
                        </span>
                    </h3>
                </div>
                <div class="card-body">
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-primary">Cargando personal externo...</p>
                    </div>
                    
                    <div id="resultados-personal">
                        <div class="text-center text-muted p-5">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <h4>Seleccione filtros y presione "Buscar"</h4>
                            <p>El personal externo aparecerá aquí</p>
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

<div id="modal-container"></div>

<?php
include_once '../templates/footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    var filtrosExpandidos = true;
    
    function toggleFiltros() {
        filtrosExpandidos = !filtrosExpandidos;
        
        if (filtrosExpandidos) {
            $('#filtrosBody').slideDown(300);
            $('#filtrosHeader .toggle-icon').html('<i class="fas fa-chevron-up"></i>');
            localStorage.setItem('filtrosExpandidosPE', 'true');
        } else {
            $('#filtrosBody').slideUp(300);
            $('#filtrosHeader .toggle-icon').html('<i class="fas fa-chevron-down"></i>');
            localStorage.setItem('filtrosExpandidosPE', 'false');
        }
    }
    
    var filtrosGuardados = localStorage.getItem('filtrosExpandidosPE');
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
        $('#filtro-estatus').select2({
            theme: 'custom-theme',
            width: '100%',
            dropdownCssClass: 'select2-dropdown-enhanced',
            selectionCssClass: 'select2-selection-enhanced',
            language: 'es'
        });
        
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
                        theme: 'custom-theme',
                        placeholder: 'Todos los cargos',
                        allowClear: true,
                        width: '100%',
                        dropdownCssClass: 'select2-dropdown-enhanced',
                        selectionCssClass: 'select2-selection-enhanced',
                        language: 'es'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando cargos:', error);
            }
        });
        
        $.ajax({
            url: 'Controlador/ajax_get_areasVisita.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-areaVisita');
                    select.empty();
                    select.append('<option value="">Todas las áreas</option>');
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'custom-theme',
                        placeholder: 'Todas las áreas',
                        allowClear: true,
                        width: '100%',
                        dropdownCssClass: 'select2-dropdown-enhanced',
                        selectionCssClass: 'select2-selection-enhanced',
                        language: 'es'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando áreas de visita:', error);
            }
        });
        
        $.ajax({
            url: 'Controlador/ajax_get_personalResponsable.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (!data.error && Array.isArray(data)) {
                    var select = $('#filtro-personalResponsable');
                    select.empty();
                    select.append('<option value="">Todos los responsables</option>');
                    
                    $.each(data, function(index, item) {
                        if (item && item.id && item.nombre) {
                            select.append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        }
                    });
                    
                    select.select2({
                        theme: 'custom-theme',
                        placeholder: 'Todos los responsables',
                        allowClear: true,
                        width: '100%',
                        dropdownCssClass: 'select2-dropdown-enhanced',
                        selectionCssClass: 'select2-selection-enhanced',
                        language: 'es'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando personal responsable:', error);
            }
        });
    }
    
    function renderizarTabla(response) {
        if (!response.data || response.data.length === 0) {
            $('#resultados-personal').html(`
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>No se encontraron resultados</h4>
                    <p>No hay personal externo que coincida con los criterios de búsqueda.</p>
                </div>
            `).show();
            return;
        }

        var html = `
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="dataTablePersonalExterno">
                    <thead>
                        <tr>
                            <th>No. Identificación</th>
                            <th>Foto</th>
                            <th>Nombre Completo</th>
                            <th>Empresa</th>
                            <th>Cargo</th>
                            <th>Área de Visita</th>
                            <th>Responsable</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Vigencia</th>
                            <th>Estatus</th>
                            <th>Documento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        $.each(response.data, function(index, item) {
            html += `
                <tr>
                    <td>${item.NumeroIdentificacion}</td>
                    <td class="text-center">${item.Foto}</td>
                    <td>${item.Nombre} ${item.ApPaterno} ${item.ApMaterno}</td>
                    <td>${item.EmpresaProcedencia}</td>
                    <td>${item.Cargo}</td>
                    <td>${item.AreaVisita}</td>
                    <td>${item.PersonalResponsable}</td>
                    <td>${item.Email}</td>
                    <td>${item.Telefono}</td>
                    <td>${item.VigenciaAcceso}</td>
                    <td class="text-center">${item.EstatusHTML}</td>
                    <td class="text-center">${item.Acceso}</td>
                    <td class="text-center">${item.Acciones}</td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
            <div class="row mt-3">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info" role="status">
                        Mostrando ${response.data.length} de ${response.recordsFiltered} registros (Total: ${response.recordsTotal})
                    </div>
                </div>
            </div>
        `;

        $('#resultados-personal').html(html).show();
        
        initEvents();
        
        if ($.fn.DataTable) {
            $('#dataTablePersonalExterno').DataTable({
                paging: false,
                searching: false,
                ordering: true,
                info: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
                }
            });
        }
    }
    
    function cargarPersonalExterno() {
        var filtros = {
            filtro_numeroIdentificacion: $('#filtro-numeroIdentificacion').val(),
            filtro_nombre: $('#filtro-nombre').val(),
            filtro_cargo: $('#filtro-cargo').val(),
            filtro_areaVisita: $('#filtro-areaVisita').val(),
            filtro_personalResponsable: $('#filtro-personalResponsable').val(),
            filtro_estatus: $('#filtro-estatus').val()
        };
        
        $('#loading').show();
        $('#resultados-personal').hide();
        
        $.ajax({
            url: 'Controlador/Obtener_PersonalExterno.php',
            type: 'POST',
            data: filtros,
            dataType: 'json',
            success: function(response) {
                $('#loading').hide();
                renderizarTabla(response);
                showNotification('Personal externo cargado correctamente', 'success');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                console.error('Error:', xhr.responseText);
                $('#resultados-personal').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Error al cargar el personal externo: ${error}
                        <br>
                        <small>${xhr.responseText}</small>
                    </div>
                `).show();
                showNotification('Error al cargar el personal externo', 'error');
            }
        });
    }
    
    function exportarExcel() {
        if ($('#resultados-personal').find('tbody tr').length === 0) {
            showNotification('No hay datos para exportar', 'warning');
            return;
        }
        
        var table = $('#dataTablePersonalExterno').clone();
        table.find('.btn, .badge, .thumbnail-image, .employee-initials').remove();
        
        var html = '<table>' + table.html() + '</table>';
        var blob = new Blob([html], {type: 'application/vnd.ms-excel'});
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'personal_externo_' + new Date().toISOString().split('T')[0] + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        showNotification('Archivo Excel generado', 'success');
    }
    
    function exportarPDF() {
        showNotification('Función PDF en desarrollo', 'info');
    }
    
    function imprimirTabla() {
        if ($('#resultados-personal').find('tbody tr').length === 0) {
            showNotification('No hay datos para imprimir', 'warning');
            return;
        }
        
        window.print();
    }
    
    function initEvents() {
        $(document).off('click', '.btn-ver-foto, .btn-editar, .btn-cambiar-estatus, .btn-eliminar, .btn-generar-doc').on('click', '.btn-ver-foto, .btn-editar, .btn-cambiar-estatus, .btn-eliminar, .btn-generar-doc', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var id = $(this).data('id');
            var tipo = $(this).data('tipo');
            
            if (tipo === 'ver-foto') {
                var fullImage = $(this).data('full-image');
                var employeeName = $(this).data('employee-name');
                
                if (fullImage) {
                    $('#modalPhoto').attr('src', fullImage);
                    $('#modalEmployeeName').text(employeeName);
                    $('#photoModal').modal('show');
                }
            } else if (tipo === 'editar') {
                loadModal('Modales/Modificar.php?IdPersonalExterno=' + id, '#ModificarPersonalExterno', 'editar');
            } else if (tipo === 'cambiar-estatus') {
                loadModal('Modales/CambiarEstatus.php?IdPersonalExterno=' + id, '#CambiarEstatusPersonalExterno', 'cambiar_estatus');
            } else if (tipo === 'generar-doc') {
                window.open('Controlador/Credencial.php?id=' + id, '_blank');
            } else if (tipo === 'eliminar') {
                var nombre = $(this).data('nombre');
                
                if (confirm('¿Está seguro de eliminar al personal externo: ' + nombre + '?')) {
                    $.ajax({
                        url: 'Controlador/Eliminar_PersonalExterno.php',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showNotification(response.message, 'success');
                                cargarPersonalExterno();
                            } else {
                                showNotification(response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            showNotification('Error al eliminar el registro: ' + error, 'error');
                        }
                    });
                }
            }
        });
        
        $(document).off('click', '.view-photo-link').on('click', '.view-photo-link', function(e) {
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
    
    function loadModal(url, modalId, actionType) {
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                $('#loading').show();
            },
            success: function(response) {
                $('#loading').hide();
                $('#modal-container').html(response);
                $(modalId).modal('show');
                
                $(modalId).find('select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'custom-theme',
                            width: '100%',
                            dropdownCssClass: 'select2-dropdown-enhanced',
                            selectionCssClass: 'select2-selection-enhanced',
                            language: 'es'
                        });
                    }
                });
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el formulario', 'danger');
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
                    cargarPersonalExterno();
                    
                    if (form.attr('id') === 'formNuevoPersonalExterno') {
                        form[0].reset();
                        form.find('select').val(null).trigger('change');
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
    
    $('#btn-nuevo').click(function(e) {
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
                $('#NuevoPersonalExterno').modal('show');
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                showNotification('Error al cargar el formulario de nuevo personal', 'danger');
            }
        });
    });
    
    $('#btn-aplicar-filtros').click(function() {
        cargarPersonalExterno();
    });
    
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-numeroIdentificacion').val('');
        $('#filtro-nombre').val('');
        $('#filtro-cargo').val(null).trigger('change');
        $('#filtro-areaVisita').val(null).trigger('change');
        $('#filtro-personalResponsable').val(null).trigger('change');
        $('#filtro-estatus').val(null).trigger('change');
        
        showNotification('Filtros limpiados', 'info');
    });
    
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
        cargarPersonalExterno();
    });
    
    $('#filtro-numeroIdentificacion, #filtro-nombre').keypress(function(e) {
        if (e.which == 13) {
            cargarPersonalExterno();
        }
    });
    
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        $('#modal-container').empty();
    });
    
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

.table tbody tr:hover {
    background-color: var(--table-hover);
}

.thumbnail-image {
    cursor: pointer;
    transition: transform 0.2s;
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
}

.thumbnail-image:hover {
    transform: scale(1.05);
}

.employee-initials {
    cursor: default;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-orange);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    margin: 0 auto;
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

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
    margin: 2px;
}

.btn-info {
    background-color: var(--info-color);
    border-color: var(--info-color);
}

.btn-warning {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: #212529;
}

.btn-secondary {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
}

.btn-danger {
    background-color: var(--danger-color);
    border-color: var(--danger-color);
}

.btn-nuevo {
    padding: 6px 12px;
    font-size: 14px;
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
    
    .btn-nuevo {
        padding: 4px 8px;
        font-size: 12px;
    }
}

@media (max-width: 768px) {
    .badge { 
        font-size: 10px !important; 
        padding: 3px 6px !important;
    }
    
    .col-md-2, .col-md-3, .col-md-4 {
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
    
    .btn-sm {
        padding: 3px 6px;
        font-size: 11px;
        margin: 1px;
    }
    
    .select2-container--custom-theme .select2-selection--multiple .select2-selection__choice {
        font-size: 11px;
        padding: 3px 6px;
    }
    
    .btn-nuevo {
        padding: 3px 6px;
        font-size: 11px;
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
    .btn-nuevo,
    .employee-initials,
    .thumbnail-image,
    .select2-container,
    .toggle-icon,
    .btn-sm {
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

.select2-multiple-hint {
    display: block;
    font-size: 11px;
    color: var(--secondary-color);
    margin-top: 4px;
    font-style: italic;
}
</style>