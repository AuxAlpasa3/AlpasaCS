<?php
include_once "../../templates/head2.php";
?>

<div class="card">
    <div class="card-header text-white" style="padding: 1rem; border-bottom: 2px solid #d94f00; background-color: #d94f00">
        <h1 class="card-title">REGISTRO DE MOVIMIENTOS DE PERSONAL</h1>
    </div>
    <div class="card-body">
        <div id="loading" class="text-center" style="display: none;">
        <div id="movimientos-container">
        </div>
    </div>
</div>

<div id="modal-container"></div>

<?php
include_once '../templates/Footer.php';
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
            success: function(response) {
                $('#movimientos-container').html(response);
                inicializarDataTable();
                $('#loading').hide();
            },
            error: function(xhr, status, error) {
                $('#movimientos-container').html(
                    '<div class="alert alert-danger">Error al cargar los movimientos: ' + error + '</div>'
                );
                $('#loading').hide();
            }
        });
    }
    
    // Función para inicializar DataTable
    function inicializarDataTable() {
        if ($.fn.DataTable.isDataTable('#dataTableMovimientos')) {
            $('#dataTableMovimientos').DataTable().destroy();
        }
        
        $('#dataTableMovimientos').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "responsive": true,
            "autoWidth": false,
            "order": [[0, "desc"]],
            "pageLength": 25
        });
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
            success: function(response) {
                $('#modal-container').html(response);
                $('.modal').modal('show');
            },
            error: function(xhr, status, error) {
                alert('Error al cargar los detalles: ' + error);
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
    
});
</script>

<style>
.badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.badge-success { background-color: #28a745; color: white; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }

.spinner-border { width: 3rem; height: 3rem; }

@media (max-width: 768px) {
    .badge { font-size: 0.75em !important; min-width: 70px !important; }
}
</style>