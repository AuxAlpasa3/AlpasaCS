<?php
include '../../api/db/conexion.php';
?>

<div class="modal fade" id="GestionProveedoresModal" tabindex="-1" role="dialog" aria-labelledby="gestionProveedoresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title" id="gestionProveedoresModalLabel">
                    <i class="fas fa-users-cog mr-2"></i>Gestión de Proveedores
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- Barra de herramientas -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="btn-toolbar justify-content-between" role="toolbar">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary" id="btn-nuevo-proveedor-modal">
                                        <i class="fas fa-plus mr-1"></i> Nuevo Proveedor
                                    </button>
                                    <button type="button" class="btn btn-success" id="btn-activar-seleccionados">
                                        <i class="fas fa-check-circle mr-1"></i> Activar
                                    </button>
                                    <button type="button" class="btn btn-warning" id="btn-desactivar-seleccionados">
                                        <i class="fas fa-ban mr-1"></i> Desactivar
                                    </button>
                                    <button type="button" class="btn btn-danger" id="btn-eliminar-seleccionados">
                                        <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                    </button>
                                </div>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info" id="btn-exportar-proveedores-excel">
                                        <i class="fas fa-file-excel mr-1"></i> Excel
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="btn-refresh-proveedores">
                                        <i class="fas fa-sync-alt mr-1"></i> Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Nombre:</label>
                                                <input type="text" id="filtro-nombre-proveedor" class="form-control" placeholder="Buscar por nombre...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Email:</label>
                                                <input type="text" id="filtro-email-proveedor" class="form-control" placeholder="Buscar por email...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Teléfono:</label>
                                                <input type="text" id="filtro-telefono-proveedor" class="form-control" placeholder="Buscar por teléfono...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Estatus:</label>
                                                <select id="filtro-estatus-proveedor" class="form-control">
                                                    <option value="">Todos</option>
                                                    <option value="activo">Activo</option>
                                                    <option value="inactivo">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-right">
                                            <button type="button" class="btn btn-primary" id="btn-buscar-proveedores">
                                                <i class="fas fa-search mr-1"></i> Buscar
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="btn-limpiar-filtros-proveedores">
                                                <i class="fas fa-broom mr-1"></i> Limpiar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de proveedores -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" id="tabla-proveedores">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="check-all-proveedores">
                                            </th>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                            <th>Dirección</th>
                                            <th>Contacto</th>
                                            <th>Personal Registrado</th>
                                            <th>Estatus</th>
                                            <th>Fecha Registro</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-proveedores">
                                    </tbody>
                                </table>
                            </div>
                            <div id="loading-proveedores" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando proveedores...</p>
                            </div>
                            <div id="no-data-proveedores" class="text-center py-4" style="display: none;">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No se encontraron proveedores</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nuevo/Editar Proveedor -->
<div class="modal fade" id="modalProveedorForm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title" id="modalProveedorTitle">
                    <i class="fas fa-user-tie mr-2"></i>Nuevo Proveedor
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formProveedor" novalidate>
                    <input type="hidden" id="IdProveedor" name="IdProveedor">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="NombreProveedor" class="required">Nombre del Proveedor</label>
                                <input type="text" class="form-control" id="NombreProveedor" name="NombreProveedor" required>
                                <div class="invalid-feedback">Por favor ingrese el nombre del proveedor.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="RFC">RFC</label>
                                <input type="text" class="form-control" id="RFC" name="RFC" maxlength="13">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Email" class="required">Email</label>
                                <input type="email" class="form-control" id="Email" name="Email" required>
                                <div class="invalid-feedback">Por favor ingrese un email válido.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Telefono" class="required">Teléfono</label>
                                <input type="text" class="form-control" id="Telefono" name="Telefono" required>
                                <div class="invalid-feedback">Por favor ingrese un teléfono.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="NombreContacto">Nombre de Contacto</label>
                                <input type="text" class="form-control" id="NombreContacto" name="NombreContacto">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="TelefonoContacto">Teléfono de Contacto</label>
                                <input type="text" class="form-control" id="TelefonoContacto" name="TelefonoContacto">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Direccion">Dirección</label>
                                <textarea class="form-control" id="Direccion" name="Direccion" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Notas">Notas Adicionales</label>
                                <textarea class="form-control" id="Notas" name="Notas" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Estatus" class="required">Estatus</label>
                                <select class="form-control" id="Estatus" name="Estatus" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btn-guardar-proveedor">
                    <i class="fas fa-save mr-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var proveedorEditando = null;
    
    // Cargar proveedores al abrir el modal
    $('#GestionProveedoresModal').on('shown.bs.modal', function() {
        cargarProveedores();
    });
    
    // Cargar proveedores
    function cargarProveedores() {
        var filtros = {
            nombre: $('#filtro-nombre-proveedor').val(),
            email: $('#filtro-email-proveedor').val(),
            telefono: $('#filtro-telefono-proveedor').val(),
            estatus: $('#filtro-estatus-proveedor').val()
        };
        
        $('#loading-proveedores').show();
        $('#tbody-proveedores').empty();
        $('#no-data-proveedores').hide();
        
        $.ajax({
            url: 'Controlador/ajax_get_proveedores_completo.php',
            type: 'GET',
            data: filtros,
            dataType: 'json',
            success: function(data) {
                $('#loading-proveedores').hide();
                
                if (data.length === 0) {
                    $('#no-data-proveedores').show();
                    return;
                }
                
                var html = '';
                $.each(data, function(index, proveedor) {
                    var estatusBadge = proveedor.Estatus === 'activo' ? 
                        '<span class="badge badge-success">Activo</span>' : 
                        '<span class="badge badge-danger">Inactivo</span>';
                    
                    var fechaRegistro = new Date(proveedor.FechaRegistro);
                    var fechaFormateada = fechaRegistro.toLocaleDateString('es-MX');
                    
                    html += '<tr>';
                    html += '<td class="text-center"><input type="checkbox" class="check-proveedor" value="' + proveedor.IdProveedor + '"></td>';
                    html += '<td>' + proveedor.IdProveedor + '</td>';
                    html += '<td><strong>' + proveedor.NombreProveedor + '</strong></td>';
                    html += '<td>' + (proveedor.Email || '-') + '</td>';
                    html += '<td>' + (proveedor.Telefono || '-') + '</td>';
                    html += '<td>' + (proveedor.Direccion ? proveedor.Direccion.substring(0, 30) + (proveedor.Direccion.length > 30 ? '...' : '') : '-') + '</td>';
                    html += '<td>' + (proveedor.NombreContacto || '-') + '<br><small>' + (proveedor.TelefonoContacto || '') + '</small></td>';
                    html += '<td class="text-center">' + proveedor.TotalPersonal + '</td>';
                    html += '<td class="text-center">' + estatusBadge + '</td>';
                    html += '<td>' + fechaFormateada + '</td>';
                    html += '<td class="text-center">';
                    html += '<div class="btn-group btn-group-sm" role="group">';
                    html += '<button class="btn btn-info btn-editar-proveedor" data-id="' + proveedor.IdProveedor + '" title="Editar">';
                    html += '<i class="fas fa-edit"></i></button>';
                    html += '<button class="btn btn-primary btn-ver-personal" data-id="' + proveedor.IdProveedor + '" title="Ver Personal">';
                    html += '<i class="fas fa-users"></i></button>';
                    html += '<button class="btn btn-warning btn-cambiar-estatus-proveedor" data-id="' + proveedor.IdProveedor + '" data-estatus="' + proveedor.Estatus + '" title="Cambiar Estatus">';
                    html += '<i class="fas fa-exchange-alt"></i></button>';
                    html += '<button class="btn btn-danger btn-eliminar-proveedor" data-id="' + proveedor.IdProveedor + '" title="Eliminar">';
                    html += '<i class="fas fa-trash"></i></button>';
                    html += '</div></td>';
                    html += '</tr>';
                });
                
                $('#tbody-proveedores').html(html);
            },
            error: function() {
                $('#loading-proveedores').hide();
                $('#no-data-proveedores').show();
                showNotification('Error al cargar proveedores', 'error');
            }
        });
    }
    
    // Check all
    $('#check-all-proveedores').click(function() {
        $('.check-proveedor').prop('checked', this.checked);
    });
    
    // Nuevo proveedor
    $('#btn-nuevo-proveedor-modal').click(function() {
        proveedorEditando = null;
        $('#formProveedor')[0].reset();
        $('#formProveedor').removeClass('was-validated');
        $('#IdProveedor').val('');
        $('#modalProveedorTitle').html('<i class="fas fa-user-tie mr-2"></i>Nuevo Proveedor');
        $('#modalProveedorForm').modal('show');
    });
    
    // Editar proveedor
    $(document).on('click', '.btn-editar-proveedor', function() {
        var id = $(this).data('id');
        proveedorEditando = id;
        
        $.ajax({
            url: 'Controlador/ajax_get_proveedor_detalle.php',
            type: 'GET',
            data: { IdProveedor: id },
            dataType: 'json',
            success: function(proveedor) {
                $('#formProveedor')[0].reset();
                $('#formProveedor').removeClass('was-validated');
                
                $('#IdProveedor').val(proveedor.IdProveedor);
                $('#NombreProveedor').val(proveedor.NombreProveedor);
                $('#RFC').val(proveedor.RFC);
                $('#Email').val(proveedor.Email);
                $('#Telefono').val(proveedor.Telefono);
                $('#NombreContacto').val(proveedor.NombreContacto);
                $('#TelefonoContacto').val(proveedor.TelefonoContacto);
                $('#Direccion').val(proveedor.Direccion);
                $('#Notas').val(proveedor.Notas);
                $('#Estatus').val(proveedor.Estatus);
                
                $('#modalProveedorTitle').html('<i class="fas fa-edit mr-2"></i>Editar Proveedor');
                $('#modalProveedorForm').modal('show');
            },
            error: function() {
                showNotification('Error al cargar datos del proveedor', 'error');
            }
        });
    });
    
    // Guardar proveedor
    $('#btn-guardar-proveedor').click(function() {
        var form = $('#formProveedor')[0];
        
        if (!form.checkValidity()) {
            $('#formProveedor').addClass('was-validated');
            return;
        }
        
        var formData = $('#formProveedor').serialize();
        var btn = $(this);
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: 'Controlador/guardar_proveedor.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    $('#modalProveedorForm').modal('hide');
                    cargarProveedores();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error al guardar proveedor', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Ver personal del proveedor
    $(document).on('click', '.btn-ver-personal', function() {
        var idProveedor = $(this).data('id');
        window.open('personal_proveedor.php?IdProveedor=' + idProveedor, '_blank');
    });
    
    // Cambiar estatus de un proveedor
    $(document).on('click', '.btn-cambiar-estatus-proveedor', function() {
        var id = $(this).data('id');
        var estatusActual = $(this).data('estatus');
        var nuevoEstatus = estatusActual === 'activo' ? 'inactivo' : 'activo';
        var nombre = $(this).closest('tr').find('td:nth-child(3)').text();
        
        if (confirm('¿Cambiar estatus del proveedor "' + nombre + '" a ' + nuevoEstatus + '?')) {
            $.ajax({
                url: 'Controlador/cambiar_estatus_proveedor.php',
                type: 'POST',
                data: { 
                    IdProveedor: id,
                    Estatus: nuevoEstatus
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        cargarProveedores();
                    } else {
                        showNotification(response.message, 'error');
                    }
                }
            });
        }
    });
    
    // Eliminar proveedor
    $(document).on('click', '.btn-eliminar-proveedor', function() {
        var id = $(this).data('id');
        var nombre = $(this).closest('tr').find('td:nth-child(3)').text();
        
        if (confirm('¿Está seguro de eliminar el proveedor "' + nombre + '"?\nEsta acción no se puede deshacer y eliminará todo el personal asociado.')) {
            $.ajax({
                url: 'Controlador/eliminar_proveedor.php',
                type: 'POST',
                data: { IdProveedor: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        cargarProveedores();
                    } else {
                        showNotification(response.message, 'error');
                    }
                }
            });
        }
    });
    
    // Activar seleccionados
    $('#btn-activar-seleccionados').click(function() {
        var seleccionados = $('.check-proveedor:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (seleccionados.length === 0) {
            showNotification('Seleccione al menos un proveedor', 'warning');
            return;
        }
        
        if (confirm('¿Activar ' + seleccionados.length + ' proveedor(es) seleccionado(s)?')) {
            $.ajax({
                url: 'Controlador/cambiar_estatus_proveedores.php',
                type: 'POST',
                data: { 
                    IdsProveedores: seleccionados,
                    Estatus: 'activo'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        cargarProveedores();
                    } else {
                        showNotification(response.message, 'error');
                    }
                }
            });
        }
    });
    
    // Desactivar seleccionados
    $('#btn-desactivar-seleccionados').click(function() {
        var seleccionados = $('.check-proveedor:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (seleccionados.length === 0) {
            showNotification('Seleccione al menos un proveedor', 'warning');
            return;
        }
        
        if (confirm('¿Desactivar ' + seleccionados.length + ' proveedor(es) seleccionado(s)?')) {
            $.ajax({
                url: 'Controlador/cambiar_estatus_proveedores.php',
                type: 'POST',
                data: { 
                    IdsProveedores: seleccionados,
                    Estatus: 'inactivo'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        cargarProveedores();
                    } else {
                        showNotification(response.message, 'error');
                    }
                }
            });
        }
    });
    
    // Eliminar seleccionados
    $('#btn-eliminar-seleccionados').click(function() {
        var seleccionados = $('.check-proveedor:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (seleccionados.length === 0) {
            showNotification('Seleccione al menos un proveedor', 'warning');
            return;
        }
        
        if (confirm('¿Eliminar ' + seleccionados.length + ' proveedor(es) seleccionado(s)?\nEsta acción no se puede deshacer.')) {
            $.ajax({
                url: 'Controlador/eliminar_proveedores.php',
                type: 'POST',
                data: { IdsProveedores: seleccionados },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        cargarProveedores();
                    } else {
                        showNotification(response.message, 'error');
                    }
                }
            });
        }
    });
    
    // Buscar proveedores
    $('#btn-buscar-proveedores').click(function() {
        cargarProveedores();
    });
    
    // Limpiar filtros
    $('#btn-limpiar-filtros-proveedores').click(function() {
        $('#filtro-nombre-proveedor, #filtro-email-proveedor, #filtro-telefono-proveedor').val('');
        $('#filtro-estatus-proveedor').val('');
        cargarProveedores();
    });
    
    // Exportar a Excel
    $('#btn-exportar-proveedores-excel').click(function() {
        var filtros = {
            nombre: $('#filtro-nombre-proveedor').val(),
            email: $('#filtro-email-proveedor').val(),
            telefono: $('#filtro-telefono-proveedor').val(),
            estatus: $('#filtro-estatus-proveedor').val()
        };
        
        var queryString = $.param(filtros);
        window.open('Controlador/exportar_proveedores_excel.php?' + queryString, '_blank');
    });
    
    // Actualizar
    $('#btn-refresh-proveedores').click(function() {
        cargarProveedores();
        showNotification('Lista de proveedores actualizada', 'success');
    });
    
    // Tecla Enter en filtros
    $('#filtro-nombre-proveedor, #filtro-email-proveedor, #filtro-telefono-proveedor').keypress(function(e) {
        if (e.which == 13) {
            cargarProveedores();
        }
    });
});

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
</script>

<style>
#GestionProveedoresModal .modal-xl {
    max-width: 95%;
}

#tabla-proveedores th {
    background-color: #d94f00;
    color: white;
    border-color: #d94f00;
    vertical-align: middle;
}

#tabla-proveedores td {
    vertical-align: middle;
}

.required:after {
    content: " *";
    color: #dc3545;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

#loading-proveedores {
    position: absolute;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.8);
    z-index: 10;
}

.check-proveedor {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

#check-all-proveedores {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
</style>