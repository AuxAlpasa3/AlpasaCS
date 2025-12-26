
<div class="modal fade" id="NuevoPersonal" tabindex="-1" role="dialog" aria-labelledby="NuevoPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #d94f00; color: white;">
                <h5 class="modal-title" id="NuevoPersonalLabel">Nuevo Personal</h5>
                <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNuevoPersonal" action="procesos/guardar_personal.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre(s):</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ap_paterno">Apellido Paterno:</label>
                                <input type="text" class="form-control" id="ap_paterno" name="ap_paterno" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ap_materno">Apellido Materno:</label>
                                <input type="text" class="form-control" id="ap_materno" name="ap_materno" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="empresa">Empresa:</label>
                                <select class="form-control" id="empresa" name="empresa" required>
                                    <option value="0">Sin Empresa</option>
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo">Cargo:</label>
                                <select class="form-control" id="cargo" name="cargo" required>
                                    <option value="0">Sin Cargo</option>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departamento">Departamento:</label>
                                <select class="form-control" id="departamento" name="departamento" required>
                                    <option value="0">Sin Departamento</option>
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ubicacion">Ubicación:</label>
                                <select class="form-control" id="ubicacion" name="ubicacion" required>
                                    <option value="0">Sin Ubicación</option>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="foto">Foto:</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                <small class="text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Estatus:</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #d94f00; border-color: #d94f00;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>