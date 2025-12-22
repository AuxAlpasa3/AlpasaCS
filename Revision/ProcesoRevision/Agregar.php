<?php
include_once "../../templates/Sesion.php";

// Obtener el próximo ID de revisión disponible
$MaximoIdRevision = $Conexion->query("SELECT COALESCE(MIN(IdRevision), 0) + 1 AS IdRevision 
    FROM (
        SELECT IdRevision FROM t_revision 
        UNION ALL 
        SELECT 0
    ) AS combined 
    WHERE IdRevision + 1 NOT IN (SELECT IdRevision FROM t_revision)");
$docu = $MaximoIdRevision->fetchAll(PDO::FETCH_OBJ);
$IdRevisionCont = $docu[0]->IdRevision ?? 1;

// Obtener tipos de revisión
$TipoRev = $Conexion->query("SELECT IdTipoRevision, TipoRevision FROM t_tipoRevision ORDER BY IdTipoRevision")->fetchAll(PDO::FETCH_OBJ);

// Obtener ubicaciones
$Ubicaciones = $Conexion->query("SELECT IdUbicacion, Ubicacion FROM t_ubicacion ORDER BY IdUbicacion")->fetchAll(PDO::FETCH_OBJ);
?>

<div class="modal fade" id="nuevaRevision" tabindex="-1" role="dialog" aria-labelledby="nuevaRevisionTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title" id="nuevaRevisionTitle">Nueva Revisión</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="AgregarRevision" name="AgregarRevision" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="IdUsuario" name="IdUsuario" value="<?= htmlspecialchars($IdUsuario) ?>">
                    <input type="hidden" id="IdRevision" name="IdRevision"
                        value="<?= htmlspecialchars($IdRevisionCont) ?>">

                    <div class="row">
                        <div class="col-md-2 form-group" style="text-align: center;">
                            <label for="IdRevisionDisplay">ID Revisión</label>
                            <input type="text" class="form-control" id="IdRevisionDisplay"
                                value="<?= htmlspecialchars($IdRevisionCont) ?>" readonly>
                        </div>
                        <div class="col-md-5 form-group" style="text-align: center;">
                            <label for="TipoRevision">Tipo Revisión</label>
                            <select class="form-control select2" name="TipoRevision" id="TipoRevision" required>
                                <option value="" disabled selected>Seleccione el Tipo Revisión...</option>
                                <?php foreach ($TipoRev as $TipoRevision): ?>
                                    <option value="<?= htmlspecialchars($TipoRevision->IdTipoRevision) ?>">
                                        <?= htmlspecialchars($TipoRevision->TipoRevision) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-5 form-group" style="text-align: center;">
                            <label for="Descripcion">Descripción</label>
                            <input id="Descripcion" class="form-control" type="text" name="Descripcion"
                                onkeyup="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group" style="text-align: center;">
                            <label for="FechaInicio">Fecha Inicio</label>
                            <input type="date" class="form-control" id="FechaInicio" name="FechaInicio" required>
                        </div>
                        <div class="col-md-4 form-group" style="text-align: center;">
                            <label for="FechaFinal">Fecha Final</label>
                            <input type="date" class="form-control" id="FechaFinal" name="FechaFinal">
                        </div>
                        <div class="col-md-4 form-group" style="text-align: center;">
                            <label for="Ubicaciones">Ubicación</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        id="selectAllUbicaciones">
                                        <i class="fas fa-check-circle"></i> Todas
                                    </button>
                                </div>
                                <select class="form-control select2" name="Ubicaciones[]" id="Ubicaciones" multiple
                                    required>
                                    <option value="" disabled>Seleccione al menos una ubicación...</option>
                                    <?php foreach ($Ubicaciones as $Ubicar): ?>
                                        <option value="<?= htmlspecialchars($Ubicar->IdUbicacion) ?>">
                                            <?= htmlspecialchars($Ubicar->Ubicacion) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <small class="text-muted">Seleccione una o más ubicaciones.</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" name="Mov" value="AgregarRevision">
                            <i class="fas fa-save"></i> Agregar Revisión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Inicializar Select2
        $('.select2').select2({
            dropdownParent: $('#nuevaRevision'),
            width: '100%',
            placeholder: "Selecciona...",
            closeOnSelect: false
        });

        // Botón para seleccionar/deseleccionar todas las ubicaciones
        $('#selectAllUbicaciones').click(function () {
            var allSelected = $('#Ubicaciones option:not(:disabled)').length === $('#Ubicaciones').val()?.length;

            if (allSelected) {
                $('#Ubicaciones').val([]).trigger('change');
                $(this).html('<i class="fas fa-check-circle"></i> Todas');
            } else {
                $('#Ubicaciones option:not(:disabled)').prop('selected', true);
                $('#Ubicaciones').trigger('change');
                $(this).html('<i class="fas fa-times-circle"></i> Ninguna');
            }
        });

        // Cuando cambia la fecha de inicio
        $('#FechaInicio').change(function () {
            // Establecer la fecha mínima para fecha final
            $('#FechaFinal').attr('min', $(this).val());

            // Si hay una fecha final y es ANTERIOR a la fecha de inicio, limpiarla
            if ($('#FechaFinal').val() && new Date($('#FechaFinal').val()) < new Date($(this).val())) {
                $('#FechaFinal').val('');
            }
        });

        // Validación cuando cambia la fecha final
        $('#FechaFinal').change(function () {
            var fechaInicio = new Date($('#FechaInicio').val());
            var fechaFinal = new Date($(this).val());

            // Validar que la fecha final NO SEA ANTERIOR a la fecha de inicio
            if ($(this).val() && fechaFinal < fechaInicio) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La fecha final debe ser igual o posterior a la fecha de inicio.',
                });
                $(this).val('');
            }
        });

        // Validación del formulario al enviar
        $('#AgregarRevision').on('submit', function (e) {
            let isValid = true;

            // Validar ubicaciones
            var selectedUbicaciones = $('#Ubicaciones').val();
            if (!selectedUbicaciones || selectedUbicaciones.length < 1) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe seleccionar al menos una ubicación.',
                });
                $('#Ubicaciones').focus();
            }

            // Validar fecha de inicio
            if (!$('#FechaInicio').val()) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La fecha de inicio es obligatoria.',
                });
                $('#FechaInicio').focus();
            }

            var fechaInicio = new Date($('#FechaInicio').val());
            var fechaFinal = $('#FechaFinal').val() ? new Date($('#FechaFinal').val()) : null;

            if (fechaFinal && fechaFinal < fechaInicio) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'La fecha final debe ser igual o posterior a la fecha de inicio.',
                });
                $('#FechaFinal').focus();
            }

            if (!$('#TipoRevision').val()) {
                isValid = false;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El tipo de revisión es obligatorio.',
                });
                $('#TipoRevision').focus();
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            Swal.fire({
                title: '¿Agregar Revisión?',
                text: "¿Está seguro de que desea crear esta revisión?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d94f00',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, agregar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    return true;
                } else {
                    e.preventDefault();
                    return false;
                }
            });
        });

        $('#Ubicaciones').on('change', function () {
            var totalOptions = $('#Ubicaciones option:not(:disabled)').length;
            var selectedCount = $(this).val()?.length || 0;

            if (selectedCount === totalOptions) {
                $('#selectAllUbicaciones').html('<i class="fas fa-times-circle"></i> Ninguna');
            } else {
                $('#selectAllUbicaciones').html('<i class="fas fa-check-circle"></i> Todas');
            }
        });

        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        today = yyyy + '-' + mm + '-' + dd;
        $('#FechaInicio').val(today);
    });
</script>