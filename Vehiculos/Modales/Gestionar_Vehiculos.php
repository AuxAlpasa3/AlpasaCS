<?php
$IdPersonal = $_GET['IdPersonal'] ?? '';
$NoEmpleado = $_GET['NoEmpleado'] ?? '';
$Nombre = $_GET['Nombre'] ?? '';
?>

<div class="modal fade" id="GestionarVehiculos" tabindex="-1" aria-labelledby="GestionarVehiculosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title" id="GestionarVehiculosLabel">
                    <i class="fas fa-car"></i> Gestión de Vehículos - <?php echo htmlspecialchars($Nombre); ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="color: white;">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="IdPersonal" value="<?php echo $IdPersonal; ?>">
                <input type="hidden" id="NoEmpleado" value="<?php echo $NoEmpleado; ?>">
                <input type="hidden" id="NombreEmpleado" value="<?php echo htmlspecialchars($Nombre); ?>">
                
                <div class="card mb-4">
                    <div class="card-header" style="background-color: #f8f9fa;">
                        <h6 class="mb-0"><i class="fas fa-plus-circle"></i> Agregar Nuevo Vehículo</h6>
                    </div>
                    <div class="card-body">
                        <form id="formNuevoVehiculo" enctype="multipart/form-data">
                            <input type="hidden" name="NoEmpleado" value="<?php echo $NoEmpleado; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Marca *</label>
                                        <input type="text" name="Marca" class="form-control" required style="border-color: #d94f00;" placeholder="Ej: Toyota">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Modelo *</label>
                                        <input type="text" name="Modelo" class="form-control" required style="border-color: #d94f00;" placeholder="Ej: Corolla">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Número de Serie *</label>
                                        <input type="text" name="Num_Serie" class="form-control" required style="border-color: #d94f00;" placeholder="Ej: 1HGCM82633A123456">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Placas *</label>
                                        <input type="text" name="Placas" class="form-control" required style="border-color: #d94f00;" placeholder="Ej: ABC123">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Año *</label>
                                        <select name="Anio" class="form-control" required style="border-color: #d94f00;">
                                            <option value="">Seleccionar año</option>
                                            <?php
                                            $currentYear = date('Y');
                                            for ($year = $currentYear; $year >= 1990; $year--) {
                                                echo "<option value='$year'>$year</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Color *</label>
                                        <input type="text" name="Color" class="form-control" required style="border-color: #d94f00;" placeholder="Ej: Rojo">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Activo</label>
                                        <select name="Activo" class="form-control" style="border-color: #d94f00;">
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Foto del Vehículo</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="FotoVehiculo" name="FotoVehiculo" accept="image/*">
                                    <label class="custom-file-label" for="FotoVehiculo">Seleccionar archivo...</label>
                                </div>
                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                            </div>
                            
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary" style="background-color: #d94f00; border-color: #d94f00;">
                                    <i class="fas fa-save"></i> Guardar Vehículo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Lista de vehículos asignados -->
                <div class="card">
                    <div class="card-header" style="background-color: #f8f9fa;">
                        <h6 class="mb-0"><i class="fas fa-list"></i> Vehículos Asignados</h6>
                    </div>
                    <div class="card-body">
                        <div id="lista-vehiculos">
                            <!-- Los vehículos se cargarán aquí vía AJAX -->
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2 text-muted">Cargando vehículos...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Cargar lista de vehículos al abrir el modal
    $('#GestionarVehiculos').on('shown.bs.modal', function() {
        cargarListaVehiculos();
    });
    
    // Preview del nombre del archivo seleccionado
    $('#FotoVehiculo').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    function cargarListaVehiculos() {
        var noEmpleado = $('#NoEmpleado').val();
        
        $.ajax({
            url: 'Controlador/Obtener_Vehiculos.php',
            type: 'GET',
            data: { NoEmpleado: noEmpleado },
            success: function(response) {
                $('#lista-vehiculos').html(response);
            },
            error: function() {
                $('#lista-vehiculos').html(
                    '<div class="alert alert-danger">Error al cargar los vehículos</div>'
                );
            }
        });
    }
});
</script>