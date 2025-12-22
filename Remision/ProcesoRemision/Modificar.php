<?php
include_once "../../templates/Sesion.php";
$IdRemisionEncabezado = $_GET['IdRemisionEncabezado'] ?? 0;

$sentRemision = $Conexion->query("SELECT Distinct(t1.IdRemisionEncabezado),t1.IdRemision ,t1.Transportista ,t1.Placas ,t1.Chofer ,t1.FechaRemision ,t1.TipoRemision as TipoRemisionNum,t6.TipoRemision ,t1.Cantidad ,t1.FechaRegistro ,t1.ReferenciaORDR ,t1.Estatus as EstatusNum,t5.Estatus, t1.Contenedor, t1.Caja, t1.Tracto, t1.Sellos, t1.Cliente 
    FROM t_remision_encabezado AS t1 
    INNER JOIN t_estatusrem     AS t5 on t1.Estatus=t5.idEstatus 
    INNER JOIN t_tipoRemision   AS t6 on t1.TipoRemision=t6.IdTipoRemision
    WHERE t1.Estatus in(0,1) AND t1.IdRemisionEncabezado=" . $IdRemisionEncabezado . ";");
$Remisiones = $sentRemision->fetchAll(PDO::FETCH_OBJ);

$clientes = $Conexion->query("SELECT IdCliente, NombreCliente FROM t_cliente ORDER BY NombreCliente")->fetchAll(PDO::FETCH_OBJ);
$tiposRemision = $Conexion->query("SELECT IdTipoRemision, TipoRemision FROM t_tipoRemision ORDER BY TipoRemision")->fetchAll(PDO::FETCH_OBJ);

foreach ($Remisiones as $Remision) {
?>

    <div class="modal fade" id="ModificarRemision" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #d94f00;">
                    <h5 class="modal-title text-white" id="title">Modificar Remisión : <?php echo $Remision->IdRemision; ?></h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="ModificarRemision" id="ModificarRemision" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="IdUsuario" name="IdUsuario" value="<?= htmlspecialchars($IdUsuario) ?>">
                        <input type="hidden" id="IdRemisionEncabezado" name="IdRemisionEncabezado" value="<?php echo $IdRemisionEncabezado; ?>">

                        <div class="step" id="step1">
                            <div class="row">
                                <div class="col-md-4 form-group text-center">
                                 <label for="IdRemisionDisplay">IdRemision</label>
                                    <input type="text" class="form-control" id="IdRemisionDisplay" value="<?php echo $Remision->IdRemision; ?>" readonly>
                                </div>
                                <div class="col-md-4 form-group text-center">
                                    <label for="TipoRemision">Tipo Remision</label>
                                    <input type="text" class="form-control" id="TipoRemision" value="<?php echo $Remision->TipoRemision; ?>" readonly>
                                </div>
                                <div class="col-md-4 form-group text-center">
                                    <label for="Fecha">Fecha</label>
                                    <input type="date" class="form-control" id="Fecha" name="Fecha" value="<?php echo $Remision->FechaRemision; ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 form-group text-center">
                                    <label for="ClienteId">Cliente</label>
                                    <select class="form-control select2" name="ClienteId" id="ClienteId" required>
                                        <option value="" disabled>Seleccione el Cliente...</option>
                                        <?php foreach ($clientes as $cliente): ?>
                                                <option value="<?= htmlspecialchars($cliente->IdCliente) ?>" 
                                                    <?= ($cliente->IdCliente == $Remision->Cliente) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cliente->NombreCliente) ?>
                                                </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Sección de Transportista -->
                            <div class="row">
                                <div class="col-md-4 form-group text-center">
                                    <label for="Transportista">Transportista</label>
                                    <input type="text" class="form-control" id="Transportista" name="Transportista" 
                                        value="<?php echo htmlspecialchars($Remision->Transportista); ?>"
                                        onkeyup="this.value = this.value.toUpperCase()" placeholder="Transportista">
                                </div>
                                <div class="col-md-4 form-group text-center">
                                    <label for="Placas">Placas</label>
                                    <input type="text" class="form-control" id="Placas" name="Placas" 
                                        value="<?php echo htmlspecialchars($Remision->Placas); ?>"
                                        onkeyup="this.value = this.value.toUpperCase()" placeholder="Placas">
                                </div>
                                <div class="col-md-4 form-group text-center">
                                    <label for="Chofer">Chofer</label>
                                    <input type="text" class="form-control" id="Chofer" name="Chofer" 
                                        value="<?php echo htmlspecialchars($Remision->Chofer); ?>"
                                        onkeyup="this.value = this.value.toUpperCase()" placeholder="Chofer">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 form-group text-center">
                                    <label for="Contenedor">Contenedor</label>
                                    <input type="text" class="form-control" id="Contenedor" name="Contenedor" 
                                        value="<?php echo htmlspecialchars($Remision->Contenedor ?? ''); ?>"
                                        onkeyup="this.value = this.value.toUpperCase()" placeholder="Contenedor">
                                </div>
                                <div class="col-md-3 form-group text-center">
                                    <label for="Caja">Caja</label>
                                    <input type="text" class="form-control" id="Caja" name="Caja" 
                                        value="<?php echo htmlspecialchars($Remision->Caja ?? ''); ?>"
                                        onkeyup="this.value = this.value.toUpperCase()" placeholder="Caja">
                                </div>
                                <div class="col-md-3 form-group text-center">
                                    <label for="Tracto">Tracto</label>
                                    <input type="text" class="form-control" id="Tracto" name="Tracto" 
                                        value="<?php echo htmlspecialchars($Remision->Tracto ?? ''); ?>"
                                        onkeyup="this.value = this.value.toUpperCase()" placeholder="Tracto">
                                </div>
                                <div class="col-md-3 form-group text-center">
                                    <label for="Sellos">Sellos</label>
                                    <input type="text" class="form-control" id="Sellos" name="Sellos" 
                                        value="<?php echo htmlspecialchars($Remision->Sellos ?? ''); ?>"
                                        onkeyup="this.value = this.value.toUpperCase()" placeholder="Sellos">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-success" onclick="guardarModificacionCompleta()">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.select2').select2({
                dropdownParent: $('#ModificarRemision .modal-content'),
                width: '100%'
            });
        });

        function guardarModificacionCompleta() {
            let isValid = true;

            $('#step1 [required]').each(function () {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                mostrarError('Por favor complete todos los campos requeridos');
                return;
            }

            Swal.fire({
                title: 'Guardando cambios...',
                html: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('Mov', 'ModificarRemision');
            formData.append('IdRemisionEncabezado', $('#IdRemisionEncabezado').val());
            formData.append('IdUsuario', $('#IdUsuario').val());
            formData.append('TipoRemision', $('#TipoRemision').val());
            formData.append('Fecha', $('#Fecha').val());
            formData.append('ClienteId', $('#ClienteId').val());
            formData.append('Transportista', $('#Transportista').val());
            formData.append('Placas', $('#Placas').val());
            formData.append('Chofer', $('#Chofer').val());
            formData.append('Contenedor', $('#Contenedor').val());
            formData.append('Caja', $('#Caja').val());
            formData.append('Tracto', $('#Tracto').val());
            formData.append('Sellos', $('#Sellos').val());

            $.ajax({
                url: 'ProcesoRemision/ModificarRemision.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Cambios guardados!',
                            text: 'La remisión se ha actualizado exitosamente',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#ModificarRemision').modal('hide');
                                location.reload();
                            }
                        });
                    } else {
                        mostrarError('Error al guardar: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close();
                    mostrarError('Error de conexión: ' + error);
                }
            });
        }

        function mostrarAdvertencia(mensaje) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: mensaje,
                confirmButtonText: 'Entendido'
            });
        }

        function mostrarError(mensaje) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonText: 'Entendido'
            });
        }
    </script>
<?php } ?>