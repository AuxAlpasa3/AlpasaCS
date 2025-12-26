<?php
  Include_once "../../templates/Sesion.php";

$IdPersonal = $_GET['IdPersonal'] ?? 0;

$sentPersonal = $Conexion->prepare("SELECT IdPersonal, Nombre, ApPaterno, ApMaterno, Status FROM t_personal WHERE IdPersonal = ?");
$sentPersonal->execute([$IdPersonal]);
$personal = $sentPersonal->fetch(PDO::FETCH_OBJ);

$estatus_actual = ($personal->Status == 1) ? 'Activo' : 'Inactivo';
$estatus_nuevo = ($personal->Status == 1) ? 'Inactivo' : 'Activo';
?>

<div class="modal fade" id="CambiarEstatusPersonal" tabindex="-1" role="dialog" aria-labelledby="CambiarEstatusPersonalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #ffc107; color: #212529;">
                <h5 class="modal-title" id="CambiarEstatusPersonalLabel">Cambiar Estatus del Personal</h5>
                <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCambiarEstatusPersonal" action="procesos/cambiar_estatus_personal.php" method="POST">
                <input type="hidden" name="IdPersonal" value="<?php echo $personal->IdPersonal; ?>">
                <input type="hidden" name="estatus_actual" value="<?php echo $personal->Status; ?>">
                <input type="hidden" name="estatus_nuevo" value="<?php echo ($personal->Status == 1) ? 0 : 1; ?>">
                
                <div class="modal-body">
                    <div class="alert 
                        <?php echo ($personal->Status == 1) ? 'alert-warning' : 'alert-success'; ?>">
                        <i class="fas fa-exchange-alt fa-lg"></i>
                        <strong>Confirmar cambio de estatus</strong>
                    </div>
                    
                    <div class="text-center mb-4">
                        <h5><?php echo htmlspecialchars($personal->Nombre . ' ' . $personal->ApPaterno . ' ' . $personal->ApMaterno); ?></h5>
                        <div class="mt-3">
                            <span class="badge 
                                <?php echo ($personal->Status == 1) ? 'badge-success' : 'badge-danger'; ?> 
                                p-2" style="font-size: 1em;">
                                Estatus Actual: <?php echo $estatus_actual; ?>
                            </span>
                            <i class="fas fa-arrow-right mx-3 text-muted"></i>
                            <span class="badge 
                                <?php echo ($personal->Status == 1) ? 'badge-danger' : 'badge-success'; ?> 
                                p-2" style="font-size: 1em;">
                                Nuevo Estatus: <?php echo $estatus_nuevo; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="motivo">Motivo del cambio (opcional):</label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="3" 
                                  placeholder="Ingrese el motivo del cambio de estatus..."></textarea>
                    </div>
                    
                    <?php if($personal->Status == 1): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <small>Al cambiar a Inactivo, el personal ya no aparecerá en listados activos pero conservará su historial.</small>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <small>Al cambiar a Activo, el personal volverá a aparecer en todos los listados activos.</small>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-exchange-alt"></i> Cambiar Estatus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validación del formulario
    $('#formCambiarEstatusPersonal').on('submit', function(e) {
        var motivo = $('#motivo').val().trim();
        if (motivo.length > 500) {
            alert('El motivo no puede exceder los 500 caracteres');
            e.preventDefault();
            return false;
        }
        return true;
    });
});
</script>