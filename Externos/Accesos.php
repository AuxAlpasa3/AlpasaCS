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
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando movimientos...</p>
                    </div>
                    <div id="movimientos-container">
                        <!-- Contenido cargado por AJAX -->
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
    function cargarMovimientos() {
        $('#loading').show();
        $('#movimientos-container').empty();
        
        $.ajax({
            url: 'Controlador/ajax_movimientos.php',
            type: 'GET',
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
                // Asegurar que se oculte incluso si hay errores
                $('#loading').hide();
            }
        });
    }
    
    function inicializarDataTable() {
        // Verificar si la tabla existe
        if ($('#dataTableMovimientos').length) {
            // Destruir instancia previa si existe
            if ($.fn.DataTable.isDataTable('#dataTableMovimientos')) {
                $('#dataTableMovimientos').DataTable().destroy();
                $('#dataTableMovimientos').empty();
            }
            
            // Inicializar DataTable
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
                },
                "drawCallback": function(settings) {
                    // Asegurar que el loading esté oculto
                    $('#loading').hide();
                }
            });
        } else {
            console.warn('La tabla #dataTableMovimientos no existe en el HTML cargado');
            $('#loading').hide();
        }
    }
    
    cargarMovimientos();
    
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
                // Mostrar loading para modal si es necesario
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
    
    // Botón de recargar movimientos
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

@media (max-width: 768px) {
    .badge { 
        font-size: 0.75em !important; 
        min-width: 70px !important; 
    }
}
</style>