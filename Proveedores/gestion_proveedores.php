<?php
include "../templates/head.php";
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 fw-bold" style="color: #d94f00">
                        <i class="fas fa-users-cog mr-2"></i>Gestión de Proveedores
                    </h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver al Control
                    </a>
                    <button type="button" class="btn btn-primary" id="btn-nuevo-proveedor" 
                            style="background-color: #d94f00; border-color: #d94f00;">
                        <i class="fas fa-plus mr-1"></i> Nuevo Proveedor
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary mb-4">
                <div class="card-header text-white" style="background-color: #d94f00; padding: 1rem;">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-filter mr-2"></i>Filtros de Búsqueda
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Buscar:</label>
                                <input type="text" id="filtro-busqueda" class="form-control" 
                                       placeholder="Nombre, email, teléfono, contacto...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Estado:</label>
                                <select id="filtro-estado" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                    <option value="suspendido">Suspendido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 text-right mt-4">
                            <div class="form-group">
                                <button type="button" id="btn-buscar" class="btn btn-primary"
                                        style="background-color: #d94f00; border-color: #d94f00;">
                                    <i class="fas fa-search mr-1"></i> Buscar
                                </button>
                                <button type="button" id="btn-limpiar" class="btn btn-outline-primary">
                                    <i class="fas fa-broom mr-1"></i>
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
                            <i class="fas fa-list mr-2"></i>Listado de Proveedores
                        </span>
                        <span class="badge badge-light" id="total-proveedores">0 proveedores</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2 text-primary">Cargando proveedores...</p>
                    </div>
                    
                    <div id="resultados-proveedores" class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTableProveedores">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Proveedor</th>
                                    <th>Contacto</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Estado</th>
                                    <th>Personal</th>
                                    <th>Vehículos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-proveedores"></tbody>
                        </table>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <nav aria-label="Paginación">
                                <ul class="pagination justify-content-center" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalProveedor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title" id="modalTitulo">Nuevo Proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formProveedor">
                    <input type="hidden" id="IdProveedor" name="IdProveedor">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="NombreProveedor" class="form-label">Nombre del Proveedor *</label>
                                <input type="text" class="form-control" id="NombreProveedor" 
                                       name="NombreProveedor" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="RazonSocial" class="form-label">Razón Social</label>
                                <input type="text" class="form-control" id="RazonSocial" 
                                       name="RazonSocial" placeholder="Mismo que nombre si no aplica">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="RFC" class="form-label">RFC</label>
                                <input type="text" class="form-control" id="RFC" name="RFC">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="MotivoIngreso" class="form-label">Motivo de Ingreso</label>
                                <input type="text" class="form-control" id="MotivoIngreso" 
                                       name="MotivoIngreso" placeholder="Ej: Suministro de artículos...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Telefono" class="form-label">Teléfono *</label>
                                <input type="tel" class="form-control" id="Telefono" 
                                       name="Telefono" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="Email" 
                                       name="Email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ContactoNombre" class="form-label">Persona de Contacto</label>
                                <input type="text" class="form-control" id="ContactoNombre" 
                                       name="ContactoNombre">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ContactoTelefono" class="form-label">Teléfono de Contacto</label>
                                <input type="tel" class="form-control" id="ContactoTelefono" 
                                       name="ContactoTelefono">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" id="Direccion" 
                                          name="Direccion" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Estado" class="form-label">Estado</label>
                                <select class="form-control" id="Estado" name="Estado">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                    <option value="suspendido">Suspendido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="FechaRegistro" class="form-label">Fecha de Registro</label>
                                <input type="date" class="form-control" id="FechaRegistro" 
                                       name="FechaRegistro" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" 
                        style="background-color: #d94f00; border-color: #d94f00;"
                        id="btnGuardarProveedor">
                    <i class="fas fa-save mr-1"></i> Guardar Proveedor
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var currentPage = 1;
    var itemsPerPage = 10;
    var totalItems = 0;
    
    function cargarProveedores(page = 1) {
        currentPage = page;
        
        var filtros = {
            busqueda: $('#filtro-busqueda').val(),
            estado: $('#filtro-estado').val(),
            page: page,
            limit: itemsPerPage
        };
        
        $('#loading').show();
        $('#tbody-proveedores').empty();
        
        $.ajax({
            url: 'Controlador/Obtener_Proveedores.php',
            type: 'GET',
            data: filtros,
            dataType: 'json',
            success: function(response) {
                $('#loading').hide();
                
                if (response.success) {
                    totalItems = response.total;
                    $('#total-proveedores').text(response.total + ' proveedores');
                    mostrarProveedores(response.data);
                    generarPaginacion(response.total, response.pages, page);
                } else {
                    $('#tbody-proveedores').html(
                        '<tr><td colspan="9" class="text-center">' + response.message + '</td></tr>'
                    );
                    $('#total-proveedores').text('0 proveedores');
                }
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                $('#tbody-proveedores').html(
                    '<tr><td colspan="9" class="text-center text-danger">Error al cargar proveedores</td></tr>'
                );
                $('#total-proveedores').text('0 proveedores');
                console.error('Error:', error);
            }
        });
    }
    
    function mostrarProveedores(proveedores) {
        var tbody = $('#tbody-proveedores');
        tbody.empty();
        
        if (proveedores.length === 0) {
            tbody.html(
                '<tr><td colspan="9" class="text-center">No se encontraron proveedores</td></tr>'
            );
            return;
        }
        
        $.each(proveedores, function(index, proveedor) {
            var estadoBadge = '';
            switch(proveedor.Estado) {
                case 'activo':
                    estadoBadge = '<span class="badge badge-success">Activo</span>';
                    break;
                case 'inactivo':
                    estadoBadge = '<span class="badge badge-secondary">Inactivo</span>';
                    break;
                case 'suspendido':
                    estadoBadge = '<span class="badge badge-danger">Suspendido</span>';
                    break;
                default:
                    estadoBadge = '<span class="badge badge-warning">' + proveedor.Estado + '</span>';
            }
            
            var telefono = proveedor.Telefono || 'No registrado';
            var email = proveedor.Email || 'No registrado';
            var contacto = proveedor.ContactoNombre || 'No especificado';
            
            var row = `
                <tr>
                    <td>${proveedor.IdProveedor}</td>
                    <td>
                        <strong>${proveedor.NombreProveedor}</strong>
                        ${proveedor.RazonSocial && proveedor.RazonSocial !== proveedor.NombreProveedor ? 
                         '<br><small class="text-muted">' + proveedor.RazonSocial + '</small>' : ''}
                    </td>
                    <td>${contacto}</td>
                    <td>${telefono}</td>
                    <td>${email}</td>
                    <td>${estadoBadge}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info btn-ver-personal" 
                                data-id="${proveedor.IdProveedor}"
                                data-nombre="${proveedor.NombreProveedor}">
                            <i class="fas fa-users"></i> ${proveedor.total_personal || 0}
                        </button>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning btn-ver-vehiculos" 
                                data-id="${proveedor.IdProveedor}"
                                data-nombre="${proveedor.NombreProveedor}">
                            <i class="fas fa-truck"></i> ${proveedor.total_vehiculos || 0}
                        </button>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-warning btn-editar" 
                                    data-id="${proveedor.IdProveedor}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-info btn-detalles" 
                                    data-id="${proveedor.IdProveedor}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" 
                                    data-id="${proveedor.IdProveedor}"
                                    data-nombre="${proveedor.NombreProveedor}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            
            tbody.append(row);
        });
        
        initEvents();
    }
    
    function generarPaginacion(total, totalPages, currentPage) {
        var pagination = $('#pagination');
        pagination.empty();
        
        if (totalPages <= 1) return;
        
        var prevDisabled = currentPage === 1 ? 'disabled' : '';
        pagination.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `);
        
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, startPage + 4);
        
        if (startPage > 1) {
            pagination.append(`
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
                ${startPage > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
            `);
        }
        
        for (var i = startPage; i <= endPage; i++) {
            var active = i === currentPage ? 'active' : '';
            pagination.append(`
                <li class="page-item ${active}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }
        
        if (endPage < totalPages) {
            pagination.append(`
                ${endPage < totalPages - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>
            `);
        }
        
        var nextDisabled = currentPage === totalPages ? 'disabled' : '';
        pagination.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `);
    }
    
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page) {
            cargarProveedores(page);
        }
    });
    
    $('#btn-buscar').click(function() {
        cargarProveedores(1);
    });
    
    $('#btn-limpiar').click(function() {
        $('#filtro-busqueda').val('');
        $('#filtro-estado').val('');
        cargarProveedores(1);
    });
    
    $('#filtro-busqueda').keypress(function(e) {
        if (e.which == 13) {
            cargarProveedores(1);
        }
    });
    
    $('#btn-nuevo-proveedor').click(function() {
        abrirModalProveedor();
    });
    
    function abrirModalProveedor(idProveedor = null) {
        $('#formProveedor')[0].reset();
        $('#IdProveedor').val('');
        
        if (idProveedor) {
            $('#modalTitulo').text('Editar Proveedor');
            cargarDatosProveedor(idProveedor);
        } else {
            $('#modalTitulo').text('Nuevo Proveedor');
            $('#FechaRegistro').val(new Date().toISOString().split('T')[0]);
        }
        
        $('#modalProveedor').modal('show');
    }
    
    function cargarDatosProveedor(idProveedor) {
        $.ajax({
            url: 'Controlador/Obtener_Proveedor.php',
            type: 'GET',
            data: { IdProveedor: idProveedor },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var proveedor = response.data;
                    
                    $.each(proveedor, function(key, value) {
                        var input = $('#' + key);
                        if (input.length) {
                            if (input.is('select')) {
                                input.val(value).trigger('change');
                            } else if (input.attr('type') === 'checkbox') {
                                input.prop('checked', value == 1);
                            } else {
                                input.val(value || '');
                            }
                        }
                    });
                    
                    if (!$('#RazonSocial').val()) {
                        $('#RazonSocial').val(proveedor.NombreProveedor);
                    }
                }
            }
        });
    }
    
    $('#btnGuardarProveedor').click(function() {
        var formData = $('#formProveedor').serialize();
        var url = $('#IdProveedor').val() ? 
                 'Controlador/Actualizar_Proveedor.php' : 
                 'Controlador/Crear_Proveedor.php';
        
        var btn = $(this);
        var originalText = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    $('#modalProveedor').modal('hide');
                    cargarProveedores(currentPage);
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function() {
                showNotification('Error en la conexión', 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    function initEvents() {
        $(document).off('click', '.btn-editar').on('click', '.btn-editar', function() {
            var idProveedor = $(this).data('id');
            abrirModalProveedor(idProveedor);
        });
        
        $(document).off('click', '.btn-detalles').on('click', '.btn-detalles', function() {
            var idProveedor = $(this).data('id');
            window.open('detalle_proveedor.php?id=' + idProveedor, '_blank');
        });
        
        $(document).off('click', '.btn-eliminar').on('click', '.btn-eliminar', function() {
            var idProveedor = $(this).data('id');
            var nombre = $(this).data('nombre');
            
            if (confirm('¿Está seguro de eliminar al proveedor "' + nombre + '"?\nEsta acción marcará al proveedor como inactivo.')) {
                $.ajax({
                    url: 'Controlador/Eliminar_Proveedor.php',
                    type: 'POST',
                    data: { IdProveedor: idProveedor },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showNotification('Proveedor eliminado', 'success');
                            cargarProveedores(currentPage);
                        } else {
                            showNotification(response.message, 'error');
                        }
                    }
                });
            }
        });
        
        $(document).off('click', '.btn-ver-personal').on('click', '.btn-ver-personal', function() {
            var idProveedor = $(this).data('id');
            var nombre = $(this).data('nombre');
            
            window.open('gestion_personal.php?IdProveedor=' + idProveedor + '&nombre=' + encodeURIComponent(nombre), '_blank');
        });
        
        $(document).off('click', '.btn-ver-vehiculos').on('click', '.btn-ver-vehiculos', function() {
            var idProveedor = $(this).data('id');
            var nombre = $(this).data('nombre');
            
            window.open('gestion_vehiculos.php?IdProveedor=' + idProveedor + '&nombre=' + encodeURIComponent(nombre), '_blank');
        });
    }
    
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
    
    cargarProveedores();
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
}

.table th {
    background-color: var(--primary-orange);
    color: white;
    text-align: center;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success { background-color: var(--success-color); }
.badge-warning { background-color: var(--warning-color); color: #212529; }
.badge-danger { background-color: var(--danger-color); }
.badge-secondary { background-color: var(--secondary-color); }
.badge-primary { background-color: var(--primary-orange); }

.page-item.active .page-link {
    background-color: var(--primary-orange);
    border-color: var(--primary-orange);
}

.page-link {
    color: var(--primary-orange);
}

.page-link:hover {
    color: var(--primary-orange-dark);
}

.btn-group .btn {
    margin-right: 2px;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .btn-sm {
        padding: 0.15rem 0.3rem;
        font-size: 0.75rem;
    }
}
</style>

<?php
include '../templates/footer.php';
?>