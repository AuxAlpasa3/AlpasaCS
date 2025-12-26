<?php
$IdPersonal = $_GET['IdPersonal'] ?? 0;

$sentPersonal = $Conexion->prepare("SELECT * FROM t_personal WHERE IdPersonal = ?");
$sentPersonal->execute([$IdPersonal]);
$personal = $sentPersonal->fetch(PDO::FETCH_OBJ);
?>

<div class="modal fade" id="ModificarPersonal" tabindex="-1" role="dialog" aria-labelledby="ModificarPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title" id="ModificarPersonalLabel">Modificar Personal</h5>
                <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formModificarPersonal" action="procesos/actualizar_personal.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="IdPersonal" value="<?php echo $personal->IdPersonal; ?>">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_edit">Nombre(s):</label>
                                <input type="text" class="form-control" id="nombre_edit" name="nombre" value="<?php echo htmlspecialchars($personal->Nombre ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ap_paterno_edit">Apellido Paterno:</label>
                                <input type="text" class="form-control" id="ap_paterno_edit" name="ap_paterno" value="<?php echo htmlspecialchars($personal->ApPaterno ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ap_materno_edit">Apellido Materno:</label>
                                <input type="text" class="form-control" id="ap_materno_edit" name="ap_materno" value="<?php echo htmlspecialchars($personal->ApMaterno ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="empresa_edit">Empresa:</label>
                                <select class="form-control" id="empresa_edit" name="empresa" required>
                                    <option value="0">Sin Empresa</option>
                                    <?php
                                    $sentEmpresas = $Conexion->query("SELECT IdEmpresa, NomEmpresa FROM t_empresa WHERE Status=1 ORDER BY NomEmpresa");
                                    $empresas = $sentEmpresas->fetchAll(PDO::FETCH_OBJ);
                                    foreach($empresas as $empresa){
                                        $selected = ($personal->Empresa == $empresa->IdEmpresa) ? 'selected' : '';
                                        echo "<option value='{$empresa->IdEmpresa}' $selected>{$empresa->NomEmpresa}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo_edit">Cargo:</label>
                                <select class="form-control" id="cargo_edit" name="cargo" required>
                                    <option value="0">Sin Cargo</option>
                                    <?php
                                    $sentCargos = $Conexion->query("SELECT IdCargo, NomCargo FROM t_cargo WHERE Status=1 ORDER BY NomCargo");
                                    $cargos = $sentCargos->fetchAll(PDO::FETCH_OBJ);
                                    foreach($cargos as $cargo){
                                        $selected = ($personal->Cargo == $cargo->IdCargo) ? 'selected' : '';
                                        echo "<option value='{$cargo->IdCargo}' $selected>{$cargo->NomCargo}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departamento_edit">Departamento:</label>
                                <select class="form-control" id="departamento_edit" name="departamento" required>
                                    <option value="0">Sin Departamento</option>
                                    <?php
                                    $sentDeptos = $Conexion->query("SELECT IdDepartamento, NomDepto FROM t_departamento WHERE Status=1 ORDER BY NomDepto");
                                    $deptos = $sentDeptos->fetchAll(PDO::FETCH_OBJ);
                                    foreach($deptos as $depto){
                                        $selected = ($personal->Departamento == $depto->IdDepartamento) ? 'selected' : '';
                                        echo "<option value='{$depto->IdDepartamento}' $selected>{$depto->NomDepto}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ubicacion_edit">Ubicación:</label>
                                <select class="form-control" id="ubicacion_edit" name="ubicacion" required>
                                    <option value="0">Sin Ubicación</option>
                                    <?php
                                    $sentUbicaciones = $Conexion->query("SELECT IdUbicacion, NomCorto FROM t_ubicacion WHERE Status=1 ORDER BY NomCorto");
                                    $ubicaciones = $sentUbicaciones->fetchAll(PDO::FETCH_OBJ);
                                    foreach($ubicaciones as $ubicacion){
                                        $selected = ($personal->IdUbicacion == $ubicacion->IdUbicacion) ? 'selected' : '';
                                        echo "<option value='{$ubicacion->IdUbicacion}' $selected>{$ubicacion->NomCorto}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="foto_edit">Foto:</label>
                                <?php if(!empty($personal->RutaFoto)): ?>
                                <div class="mb-2">
                                    <img src="<?php echo $personal->RutaFoto; ?>" width="80" height="80" class="img-thumbnail">
                                </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="foto_edit" name="foto" accept="image/*">
                                <input type="hidden" name="foto_actual" value="<?php echo $personal->RutaFoto; ?>">
                                <small class="text-muted">Dejar vacío para conservar la foto actual</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_edit">Estatus:</label>
                                <select class="form-control" id="status_edit" name="status" required>
                                    <option value="1" <?php echo ($personal->Status == 1) ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo ($personal->Status == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #d94f00; border-color: #d94f00;">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>