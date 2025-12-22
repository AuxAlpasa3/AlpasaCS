<?php
// ProcesoRevision/Cerrar.php
$IdRevision = $_GET['IdRevision'] ?? '';
?>

<div class="modal fade" id="CerrarRevision" tabindex="-1" aria-labelledby="cerrarRevisionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title" id="cerrarRevisionLabel">
                    <i class="fa fa-lock"></i> Cerrar Revisión
                </h5>
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCerrarRevision" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="Mov" value="CerrarRevision">
                    <input type="hidden" name="IdRevision" value="<?php echo htmlspecialchars($IdRevision); ?>">
                    
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>¿Está seguro que desea cerrar esta revisión?</strong>
                        <br>
                        <small>Una vez cerrada, no podrá realizar modificaciones.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="comentario" class="form-label">Comentario (opcional):</label>
                        <textarea class="form-control" id="comentario" name="comentario" rows="3" 
                                  placeholder="Ingrese un comentario sobre el cierre de la revisión"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" style="background-color: #d94f00; color: white;">
                        <i class="fa fa-lock"></i> Confirmar Cierre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>