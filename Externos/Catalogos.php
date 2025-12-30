<?php
include '../templates/head.php';
?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Catálogo de Externos Recurrentes</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00">
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn-nuevo btn btn-primary btn-g">
                                <i class="fa fa-plus"></i> Añadir Nuevo
                            </button>
                            
                            <div id="notification-area" class="mt-3"></div>
                            
                            <div class="table-responsive pt-2">
                                <table class="table table-bordered table-striped" id="dataTablePersonal">
                                    <thead>
                                        <tr>
                                            <th>NoEmpleado</th>
                                            <th>Foto</th>
                                            <th>Nombre</th>
                                            <th>Apellido Paterno</th>
                                            <th>Apellido Materno</th>
                                            <th>Cargo</th>
                                            <th>Departamento</th>
                                            <th>Empresa</th>
                                            <th>Estatus</th>
                                            <th>Ubicación</th>
                                            <th>Acceso</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="photoModalLabel">Foto del Empleado</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="modalPhoto" src="" alt="Foto del empleado" class="img-fluid" style="max-height: 70vh;">
                                    <p id="modalEmployeeName" class="mt-3 font-weight-bold"></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="modal-container"></div>
                </div>
            </div>
        </div>
    </section>
</div> 

<script type="text/javascript">
$(document).ready(function() {
    var table = $('#dataTablePersonal').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "Controlador/Obtener_Personal.php",
            "type": "POST",
            "dataType": "json",
            "error": function(xhr, error, thrown) {
                console.error("Error al cargar datos:", error);
                showNotification('Error al cargar los datos. Por favor, recarga la página.', 'danger');
            }
        },
        "columns": [
            { 
                "data": "NoEmpleado",
                "className": "text-center"
            },
            { 
                "data": "Foto",
                "orderable": false,
                "searchable": false,
                "className": "text-center"
            },
            { "data": "Nombre" },
            { "data": "ApPaterno" },
            { "data": "ApMaterno" },
            { "data": "Cargo" },
            { "data": "Departamento" },
            { "data": "Empresa" },
            { 
                "data": "Estatus",
                "className": "text-center"
            },
            { "data": "Ubicacion" },
            { 
                "data": "Acceso",
                "orderable": false,
                "searchable": false,
                "className": "text-center"
            },
            { 
                "data": "Acciones",
                "orderable": false,
                "searchable": false,
                "className": "text-center"
            }
        ],
        "language": {
            "processing": "<div class='spinner-border text-primary' role='status'><span class='sr-only'>Cargando...</span></div>",
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron registros",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primera",
                "last": "Última",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "loadingRecords": "Cargando...",
            "emptyTable": "No hay datos disponibles en la tabla"
        },
        "responsive": true,
        "autoWidth": false,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 2, 3, 4, 5, 6, 7, 8, 9]
                }
            },
            {
                text: '<i class="fa fa-sync-alt"></i> Recargar',
                className: 'btn btn-warning btn-sm',
                action: function (e, dt, node, config) {
                    table.ajax.reload();
                    showNotification('Tabla recargada correctamente', 'success');
                }
            }
        ],
        "initComplete": function(settings, json) {
            initEvents();
            console.log('DataTable inicializado correctamente');
        },
        "drawCallback": function(settings) {
            initEvents();
        }
    });
    
    function initEvents() {
        $('.thumbnail-image, .view-photo-link').off('click').on('click', function(e) {
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
    
    $(document).on('click', '.btn-nuevo', function() {
        loadModal('Modales/Nuevo.php', '#NuevoPersonal', 'nuevo');
    });
    
    $(document).on('click', '.btn-editar', function() {
        var id = $(this).data('id');
        loadModal('Modales/Modificar.php?IdPersonal=' + id, '#ModificarPersonal', 'editar');
    });
    
    $(document).on('click', '.btn-cambiar-estatus', function() {
        var id = $(this).data('id');
        loadModal('Modales/CambiarEstatus.php?IdPersonal=' + id, '#CambiarEstatusPersonal', 'cambiar_estatus');
    });
    
    function loadModal(url, modalId, actionType) {
        $('#modal-container').load(url, function(response, status, xhr) {
            if (status === "error") {
                showNotification('Error al cargar el formulario', 'danger');
                return;
            }
            $(modalId).modal('show');
            
            // Inicializar Select2 si está disponible
            if ($.fn.select2) {
                $(modalId).find('select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'bootstrap4',
                            placeholder: 'Seleccione una opción',
                            allowClear: true
                        });
                    }
                });
            }
        });
    }
    
    $(document).on('submit', '#formNuevoPersonal, #formModificarPersonal, #formCambiarEstatusPersonal', function(e) {
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
                    table.ajax.reload(null, false);
                    
                    if (form.attr('id') === 'formNuevoPersonal') {
                        form[0].reset();
                        if ($.fn.select2) {
                            form.find('select').val(null).trigger('change');
                        }
                    }
                } else {
                    showNotification(response.message || 'Error en la operación', 'danger');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Error: ' + error, 'danger');
                console.error('Error AJAX:', xhr.responseText);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    $(document).on('click', '[data-dismiss="modal"], .btn-close, .modal-close', function() {
        $('.modal').modal('hide');
    });
    
    $(document).on('hidden.bs.modal', '.modal', function() {
        if ($(this).attr('id') !== 'photoModal') {
            $('#modal-container').empty();
        }
    });
    
    function showNotification(message, type) {
        var icon = type === 'success' ? 'fa-check-circle' : 
                   type === 'warning' ? 'fa-exclamation-triangle' : 
                   type === 'info' ? 'fa-info-circle' : 'fa-times-circle';
        
        var alert = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fa ${icon} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('#notification-area').html(alert);
        
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
    
    window.reloadTable = function() {
        table.ajax.reload();
    };
    
    window.showAlert = showNotification;
});
</script>

<?php
include '../templates/footer.php';
?>