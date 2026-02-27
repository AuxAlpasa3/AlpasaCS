<?php
  Include_once "../../templates/Sesion.php";
?>
<style>
    .foto-upload-area {
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        cursor: pointer;
        background: linear-gradient(135deg, #fff8f4 0%, #fff0e6 100%);
        transition: all 0.3s ease;
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    
    .foto-upload-area.dragover {
        background: linear-gradient(135deg, #ffe8d6 0%, #ffdcc2 100%);
        border-color: #ff6b2b;
        border-style: solid;
    }
    
    .foto-placeholder {
        color: #d94f00;
        transition: all 0.3s ease;
    }
    
    .foto-placeholder i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #d94f00;
    }
    
    .foto-placeholder h5 {
        font-weight: 600;
        margin-bottom: 8px;
        color: #333;
    }
    
    .foto-placeholder p {
        color: #666;
        font-size: 13px;
        margin-bottom: 5px;
    }
    
    .foto-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .foto-preview:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #d94f00 0%, #ff6b2b 100%);
        border: none;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #c44500 0%, #e85a1f 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(217, 79, 0, 0.3);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #8a939b 100%);
        border: none;
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #5a6268 0%, #727b84 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #d94f00 0%, #ff6b2b 100%);
        border-radius: 0;
        padding: 15px 20px;
    }
    
    .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
        color: white;
    }
    
    .form-control {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px 12px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #d94f00;
        box-shadow: 0 0 0 0.2rem rgba(217, 79, 0, 0.25);
    }
    
    .select2-container--default .select2-selection--single {
        border: 1px solid #ddd;
        border-radius: 6px;
        height: 38px;
        padding: 5px;
    }
    
    .select2-container--default .select2-selection--single:focus {
        border-color: #d94f00;
        box-shadow: 0 0 0 0.2rem rgba(217, 79, 0, 0.25);
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 26px;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #d94f00;
    }
    
    .badge-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-active {
        background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
        color: white;
    }
    
    .badge-inactive {
        background: linear-gradient(135deg, #dc3545 0%, #e4606d 100%);
        color: white;
    }
    
    .close {
        color: white;
        opacity: 0.8;
        transition: all 0.3s ease;
    }
    
    .close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }
    
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .modal-footer {
        border-top: 1px solid #eee;
        padding: 15px 20px;
    }
</style>

<div class="modal fade" id="NuevoPersonal" tabindex="-1" role="dialog" aria-labelledby="NuevoPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Personal</h5>
                <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNuevoPersonal" action="procesos/guardar_personal.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="noempleado" class="font-weight-bold">No. Empleado <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="noempleado" name="noempleado" required placeholder="Ingrese el número de empleado">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre" class="font-weight-bold">Nombre(s) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Juan Carlos">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ap_paterno" class="font-weight-bold">Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ap_paterno" name="ap_paterno" required placeholder="Ej: Pérez">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ap_materno" class="font-weight-bold">Apellido Materno <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="ap_materno" name="ap_materno" required placeholder="Ej: López">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="empresa" class="font-weight-bold">Empresa <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="empresa" name="empresa" required>
                                            <option value="0">Seleccione una empresa</option>
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
                                        <label for="cargo" class="font-weight-bold">Cargo <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="cargo" name="cargo" required>
                                            <option value="0">Seleccione un cargo</option>
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
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label for="departamento" class="font-weight-bold">Departamento <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="departamento" name="departamento" required>
                                            <option value="0">Seleccione un departamento</option>
                                            <?php
                                            $sentDeptos = $Conexion->query("SELECT IdDepartamento, NomDepto FROM t_departamento ORDER BY NomDepto");
                                            $deptos = $sentDeptos->fetchAll(PDO::FETCH_OBJ);
                                            foreach($deptos as $depto){
                                                echo "<option value='{$depto->IdDepartamento}'>{$depto->NomDepto}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label for="ubicacion" class="font-weight-bold">Ubicación <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="ubicacion" name="ubicacion" required>
                                            <option value="0">Seleccione una ubicación</option>
                                            <?php
                                            $sentUbicaciones = $Conexion->query("SELECT IdUbicacion, NomCorto FROM t_ubicacion ORDER BY NomCorto");
                                            $ubicaciones = $sentUbicaciones->fetchAll(PDO::FETCH_OBJ);
                                            foreach($ubicaciones as $ubicacion){
                                                echo "<option value='{$ubicacion->IdUbicacion}'>{$ubicacion->NomCorto}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label for="status" class="font-weight-bold">Estatus <span class="text-danger">*</span></label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="1"><span class="badge badge-active">Activo</span></option>
                                            <option value="0"><span class="badge badge-inactive">Inactivo</span></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Fotografía del Personal</label>
                                <div class="foto-upload-area" id="foto-upload-area">
                                    <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*" style="display: none;">
                                    <div id="foto-content" class="w-100">
                                        <div class="foto-placeholder">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <h5>Subir Foto</h5>
                                            <p>Arrastra y suelta</p>
                                            <p>o haz clic para buscar</p>
                                            <small class="text-muted">JPG, PNG, GIF (Máx. 2MB)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Personal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: 'Seleccione una opción',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#NuevoPersonal'),
        language: {
            noResults: function() {
                return "No se encontraron resultados";
            }
        }
    });
    
    $('#NuevoPersonal').on('shown.bs.modal', function () {
        $('.select2').select2({
            placeholder: 'Seleccione una opción',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#NuevoPersonal'),
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });
    });
    
    $('#NuevoPersonal').on('hidden.bs.modal', function () {
        $('.select2').select2('destroy');
        resetFotoPreview();
        $('#formNuevoPersonal')[0].reset();
    });
    
    function resetFotoPreview() {
        $('#foto-content').html(`
            <div class="foto-placeholder">
                <i class="fas fa-cloud-upload-alt"></i>
                <h5>Subir Foto</h5>
                <p>Arrastra y suelta</p>
                <p>o haz clic para buscar</p>
                <small class="text-muted">JPG, PNG, GIF (Máx. 2MB)</small>
            </div>
        `);
        $('#foto').val('');
        $('#foto-upload-area').removeClass('dragover');
    }
    
    $('#foto').change(function() {
        handleFotoFile(this.files[0]);
    });
    
    $('#foto-upload-area').click(function() {
        $('#foto').click();
    });
    
    $('#foto-upload-area').on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });
    
    $('#foto-upload-area').on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });
    
    $('#foto-upload-area').on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
        
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            handleFotoFile(files[0]);
        }
    });
    
    function handleFotoFile(file) {
        if (!file) return;
        
        if (file.size > 2 * 1024 * 1024) {
            showAlert('error', 'Archivo demasiado grande', 'El tamaño máximo permitido es 2MB.');
            return;
        }
        
        var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            showAlert('error', 'Formato no permitido', 'Solo se aceptan imágenes JPG, PNG y GIF.');
            return;
        }
        
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#foto-content').html(`
                <div class="text-center">
                    <img src="${e.target.result}" class="foto-preview mb-2" alt="Vista previa">
                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="remove-foto">
                        <i class="fas fa-trash me-1"></i> Cambiar foto
                    </button>
                </div>
            `);
            
            $('#remove-foto').click(function(e) {
                e.stopPropagation();
                resetFotoPreview();
            });
        }
        
        reader.readAsDataURL(file);
    }
    
    function showAlert(type, title, message) {
        alert(message);
    }
    
    $('#status').change(function() {
        var badge = $(this).val() == '1' ? 'badge-active' : 'badge-inactive';
        var text = $(this).val() == '1' ? 'Activo' : 'Inactivo';
        
        $(this).find('option').each(function() {
            if($(this).is(':selected')) {
                $(this).html(`<span class="badge ${badge}">${text}</span>`);
            } else {
                var otherBadge = $(this).val() == '1' ? 'badge-active' : 'badge-inactive';
                var otherText = $(this).val() == '1' ? 'Activo' : 'Inactivo';
                $(this).html(`<span class="badge ${otherBadge}">${otherText}</span>`);
            }
        });
    });
    
    $('#status').trigger('change');
});
</script>