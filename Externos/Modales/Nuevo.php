<?php
include_once "../../templates/Sesion.php";
?>
<div class="modal fade" id="NuevoPersonalExterno" tabindex="-1" role="dialog" aria-labelledby="NuevoPersonalExternoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title" id="NuevoPersonalExternoLabel">
                    <i class="fas fa-user-plus mr-2"></i>Nuevo Personal Externo
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNuevoPersonalExterno" action="Controlador/Guardar_PersonalExterno.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Número de Identificación -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="numeroIdentificacion">Número de Identificación:</label>
                                <input type="text" class="form-control form-control-lg" id="numeroIdentificacion" name="numeroIdentificacion" placeholder="Ej: EXT-001" required>
                                <small class="text-muted">Formato sugerido: EXT-001, EXT-002, etc.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Personales -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre">Nombre(s):</label>
                                <input type="text" class="form-control form-control-lg" id="nombre" name="nombre" placeholder="Ingrese el nombre" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ap_paterno">Apellido Paterno:</label>
                                <input type="text" class="form-control form-control-lg" id="ap_paterno" name="ap_paterno" placeholder="Ingrese el apellido paterno" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ap_materno">Apellido Materno:</label>
                                <input type="text" class="form-control form-control-lg" id="ap_materno" name="ap_materno" placeholder="Ingrese el apellido materno">
                            </div>
                        </div>
                    </div>

                    <!-- Empresa y Cargo -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="empresa">Empresa de Procedencia:</label>
                                <select class="form-control form-control-lg select2-empresa" id="empresa" name="empresa" required style="width: 100%;">
                                    <option value="">Seleccione una empresa</option>
                                    <?php
                                    $sentEmpresas = $Conexion->query("SELECT IdEmpresa, NomEmpresa FROM t_empresa ORDER BY NomEmpresa");
                                    $empresas = $sentEmpresas->fetchAll(PDO::FETCH_OBJ);
                                    foreach($empresas as $empresa){
                                        echo "<option value='{$empresa->IdEmpresa}'>{$empresa->NomEmpresa}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo">Cargo:</label>
                                <select class="form-control form-control-lg select2-cargo" id="cargo" name="cargo" required style="width: 100%;">
                                    <option value="">Seleccione un cargo</option>
                                    <?php
                                    $sentCargos = $Conexion->query("SELECT IdCargo, NomCargo FROM t_cargo ORDER BY NomCargo");
                                    $cargos = $sentCargos->fetchAll(PDO::FETCH_OBJ);
                                    foreach($cargos as $cargo){
                                        echo "<option value='{$cargo->IdCargo}'>{$cargo->NomCargo}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Área de Visita y Personal Responsable -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="areaVisita">Área de Visita:</label>
                                <select class="form-control form-control-lg select2-areaVisita" id="areaVisita" name="areaVisita" required style="width: 100%;">
                                    <option value="">Seleccione un área de visita</option>
                                    <?php
                                    $sentAreas = $Conexion->query("SELECT IdAreaVisita, NomAreaVisita FROM t_area_visita ORDER BY NomAreaVisita");
                                    $areas = $sentAreas->fetchAll(PDO::FETCH_OBJ);
                                    foreach($areas as $area){
                                        echo "<option value='{$area->IdAreaVisita}'>{$area->NomAreaVisita}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="personalResponsable">Personal Responsable:</label>
                                <select class="form-control form-control-lg select2-personalResponsable" id="personalResponsable" name="personalResponsable" required style="width: 100%;">
                                    <option value="">Seleccione un responsable</option>
                                    <?php
                                    $sentResponsables = $Conexion->query("SELECT IdPersonal, CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) AS NombreCompleto FROM t_personal WHERE Estatus = 1 ORDER BY Nombre, ApPaterno");
                                    $responsables = $sentResponsables->fetchAll(PDO::FETCH_OBJ);
                                    foreach($responsables as $responsable){
                                        echo "<option value='{$responsable->IdPersonal}'>{$responsable->NombreCompleto}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="ejemplo@correo.com" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono">Teléfono:</label>
                                <input type="text" class="form-control form-control-lg" id="telefono" name="telefono" placeholder="Ej: 55-1234-5678" required>
                            </div>
                        </div>
                    </div>

                    <!-- Vigencia y Estatus -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vigenciaInicio">Vigencia Desde:</label>
                                <input type="date" class="form-control form-control-lg" id="vigenciaInicio" name="vigenciaInicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vigenciaFin">Vigencia Hasta:</label>
                                <input type="date" class="form-control form-control-lg" id="vigenciaFin" name="vigenciaFin" required>
                            </div>
                        </div>
                    </div>

                    <!-- Foto y Estatus -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="foto">Foto:</label>
                                <input type="file" class="form-control form-control-lg" id="foto" name="foto" accept="image/*">
                                <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estatus">Estatus:</label>
                                <select class="form-control form-control-lg" id="estatus" name="estatus" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                    <option value="2">Baja</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg" style="background-color: #d94f00; border-color: #d94f00;">
                        <i class="fas fa-save mr-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar Select2 en el modal
    $('#NuevoPersonalExterno .select2-empresa').select2({
        theme: 'custom-theme',
        placeholder: 'Seleccione una empresa',
        allowClear: true,
        width: '100%',
        dropdownCssClass: 'select2-dropdown-enhanced',
        selectionCssClass: 'select2-selection-enhanced',
        language: 'es'
    });
    
    $('#NuevoPersonalExterno .select2-cargo').select2({
        theme: 'custom-theme',
        placeholder: 'Seleccione un cargo',
        allowClear: true,
        width: '100%',
        dropdownCssClass: 'select2-dropdown-enhanced',
        selectionCssClass: 'select2-selection-enhanced',
        language: 'es'
    });
    
    $('#NuevoPersonalExterno .select2-areaVisita').select2({
        theme: 'custom-theme',
        placeholder: 'Seleccione un área de visita',
        allowClear: true,
        width: '100%',
        dropdownCssClass: 'select2-dropdown-enhanced',
        selectionCssClass: 'select2-selection-enhanced',
        language: 'es'
    });
    
    $('#NuevoPersonalExterno .select2-personalResponsable').select2({
        theme: 'custom-theme',
        placeholder: 'Seleccione un responsable',
        allowClear: true,
        width: '100%',
        dropdownCssClass: 'select2-dropdown-enhanced',
        selectionCssClass: 'select2-selection-enhanced',
        language: 'es'
    });
    
    // Validar que la fecha de fin sea mayor a la fecha de inicio
    $('#vigenciaInicio').on('change', function() {
        var inicio = $(this).val();
        if (inicio) {
            $('#vigenciaFin').attr('min', inicio);
        }
    });
    
    $('#vigenciaFin').on('change', function() {
        var fin = $(this).val();
        var inicio = $('#vigenciaInicio').val();
        if (inicio && fin && fin < inicio) {
            alert('La fecha de vigencia final debe ser mayor a la fecha de inicio');
            $(this).val('');
        }
    });
});
</script>