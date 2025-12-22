<?php
    include_once "../../templates/Sesion.php";

    $IdRemisionEncabezado = filter_var($_GET['IdRemisionEncabezado'] ?? 0, FILTER_VALIDATE_INT);
    $IdRemision = filter_var($_GET['IdRemision'] ?? 0, FILTER_VALIDATE_INT);
    $IdAlmacen = filter_var($_GET['IdAlmacen'] ?? 0, FILTER_VALIDATE_INT);

    if ($IdRemisionEncabezado <= 0) {
        echo "<script>alert('ID de remisión inválido');</script>";
        exit;
    }

    $sentRemision = $Conexion->prepare("SELECT 
        DISTINCT(t1.IdRemisionEncabezado),
        t1.TipoRemision as TipoRemisionNum
    FROM t_remision_encabezado AS t1 
    WHERE t1.Estatus IN (0,1) AND t1.IdRemisionEncabezado = ?");

    $sentRemision->execute([$IdRemisionEncabezado]);
    $Remision = $sentRemision->fetch(PDO::FETCH_OBJ);

    if (!$Remision) {
        echo "<script>alert('Remisión  no encontrada');</script>";
        exit;
    }
    ?>

    <div class="modal fade" id="EliminarRemision" tabindex="-1" role="dialog" aria-labelledby="modalEliminarRemision" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="modalEliminarRemision">Eliminar Remisión</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="EliminarRemision" id="formEliminarRemision" method="POST" action="javascript:void(0);">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <p class="mb-3">
                                    ¿Estás seguro de eliminar la Remisión<strong><?php echo " ". htmlspecialchars($IdRemision); ?></strong>?
                                </p>
                                <div class="form-group">
                                    <input type="hidden" id="user" name="user" value="<?php echo htmlspecialchars($IdUsuario); ?>">
                                    <input type="hidden" id="TipoRemision" name="TipoRemision" value="<?php echo htmlspecialchars($Remision->TipoRemisionNum); ?>">
                                    <input type="hidden" id="IdRemisionEncabezado" name="IdRemisionEncabezado" value="<?php echo htmlspecialchars($Remision->IdRemisionEncabezado); ?>">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit" name="Mov" value="EliminarRemision" id="btnEliminar">
                                        <i class="fas fa-check"></i> Sí, Eliminar
                                    </button>
                                    <button class="btn btn-danger" type="button" data-dismiss="modal">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    window.remisionEliminada = false;
    
    $('#formEliminarRemision').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        EliminarRemision(formData);
    });
    
    $('#EliminarRemision').on('hidden.bs.modal', function() {
        if (window.remisionEliminada) {
            location.reload();
        }
    });
    
    function EliminarRemision(formData) {
        $.ajax({
            url: 'ProcesoRemision/EliminarRemision.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#btnEliminar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');
            },
            success: function(response) {
                if (response.success) {
                    window.remisionEliminada = true;
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#EliminarRemision').modal('hide');
                    });
                } else {
                    window.remisionEliminada = false;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        showConfirmButton: true
                    });
                    $('#btnEliminar').prop('disabled', false).html('<i class="fas fa-check"></i> Sí, Eliminar');
                }
            },
            error: function(xhr, status, error) {
                window.remisionEliminada = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'Ocurrió un error al procesar la solicitud: ' + error,
                    showConfirmButton: true
                });
                $('#btnEliminar').prop('disabled', false).html('<i class="fas fa-check"></i> Sí, Eliminar');
            }
        });
    }
    
    $('#EliminarRemision').on('show.bs.modal', function() {
        window.remisionEliminada = false;
    });
});
</script>