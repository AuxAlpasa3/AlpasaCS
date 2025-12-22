<?php 
    Include_once "../../templates/Sesion.php";
    $IdRevision = $_GET['IdRevision'] ?? 0;

$TipoRev = $Conexion->query("SELECT IdTipoRevision, TipoRevision FROM t_tipoRevision ORDER BY IdTipoRevision")->fetchAll(PDO::FETCH_OBJ);

       $sentRevision = $Conexion->query("SELECT t1.IdRevision,t2.TipoRevision,t1.Descripcion,  CONVERT(DATE,t1.FechaInicio) AS FechaInicio ,CONVERT(DATE,t1.FechaFinal) AS FechaFinal,t1.Estatus
          FROM dbo.t_Revision AS t1
          INNER JOIN dbo.t_tipoRevision AS t2 ON t2.IdTipoRevision = t1.TipoRevision
          where IdRevision=$IdRevision");
          $Revisiones = $sentRevision->fetchAll(PDO::FETCH_OBJ);
    
         foreach($Revisiones as $Revision){
            $IdRevision=$Revision->IdRevision;
?>

<div class="modal fade"  id="ModificarRevision" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title" id="nuevaRevisionTitle">Modificar Revisión</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ModificarRevision" name="ModificarRevision" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="IdUsuario" name="IdUsuario" value="<?= htmlspecialchars($IdUsuario) ?>">
                    <input type="hidden" id="IdRevision" name="IdRevision" value="<?= htmlspecialchars($IdRevision) ?>">
                    
                    <div class="row">
                        <div class="col-md-2 form-group" style="text-align: center;">
                            <label for="IdRevisionDisplay">ID Revisión</label>
                            <input type="text" class="form-control" id="IdRevisionDisplay" 
                                   value="<?=htmlspecialchars($IdRevision)?>" readonly>
                        </div>
                        <div class="col-md-5 form-group" style="text-align: center;">
                            <label for="TipoRevision">Tipo Revisión</label>
                            <select class="form-control select2" name="TipoRevision" id="TipoRevision" required>
                                <option value="" disabled selected>Seleccione el Tipo Revisión...</option>
                                <?php foreach($TipoRev as $TipoRevision): ?>
                                    <option value="<?= htmlspecialchars($TipoRevision->IdTipoRevision) ?>" 
                                        <?php if($TipoRevision->IdTipoRevision==$Revision->TipoRevision) 
                                                echo 'selected="selected"'; ?>>  
                                        <?= htmlspecialchars($TipoRevision->TipoRevision) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5 form-group" style="text-align: center;">
                            <label for="Descripcion">Descripción</label>
                            <input id="Descripcion" class="form-control" type="text" name="Descripcion" 
                            onkeyup="this.value = this.value.toUpperCase()" value="<?php echo $Revision->Descripcion;?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group" style="text-align: center;">
                            <label for="FechaInicio">Fecha Inicio</label>
                            <input type="date" class="form-control" value="<?php echo $Revision->FechaInicio;?>" id="FechaInicio" name="FechaInicio" required>
                        </div>
                        <div class="col-md-6 form-group" style="text-align: center;">
                            <label for="FechaFinal">Fecha Final</label>
                            <input type="date" class="form-control" value="<?php echo $Revision->FechaFinal;?>" id="FechaFinal" name="FechaFinal">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" name="Mov" value="ModificarRevision">
                            <i class="fas fa-save"></i>Modificar Revisión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script>
$(document).ready(function() {
    $('.select2').select2({
        dropdownParent: $('#ModificarRevision'),
        width: '100%',
        placeholder: "Selecciona...",
        closeOnSelect: false
    });

    $('#FechaInicio').change(function() {
        $('#FechaFinal').attr('min', $(this).val());
        if ($('#FechaFinal').val() && new Date($('#FechaFinal').val()) <= new Date($(this).val())) {
            $('#FechaFinal').val('');
        }
    });

    $('#FechaFinal').change(function() {
        var fechaInicio = new Date($('#FechaInicio').val());
        var fechaFinal = new Date($(this).val());
        
        if ($(this).val() && fechaFinal <= fechaInicio) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La fecha final debe ser posterior a la fecha de inicio.',
            });
            $(this).val('');
        }
    });

    $('#ModificarRevision').on('submit', function(e) {
        var fechaInicio = new Date($('#FechaInicio').val());
        var fechaFinal = $('#FechaFinal').val() ? new Date($('#FechaFinal').val()) : null;
        
        if (fechaFinal && fechaFinal <= fechaInicio) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La fecha final debe ser posterior a la fecha de inicio.',
            });
            $('#FechaFinal').focus();
            return;
        }
        
        if (!$('#FechaInicio').val()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La fecha de inicio es obligatoria.',
            });
            $('#FechaInicio').focus();
        }
    });

});
</script>