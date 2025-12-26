<?php
    Include_once "../../templates/Sesion.php";

$IdPersonal = $_GET['IdPersonal'] ?? 0;

$sentPersonal = $Conexion->prepare("SELECT 
    p.IdPersonal, 
    p.Nombre, 
    p.ApPaterno, 
    p.ApMaterno,
    e.NomEmpresa,
    (CASE WHEN p.Status=1 THEN 'Activo' ELSE 'Inactivo' END) as Status
FROM t_personal p
LEFT JOIN t_empresa e ON p.Empresa = e.IdEmpresa
WHERE p.IdPersonal = ?");
$sentPersonal->execute([$IdPersonal]);
$personal = $sentPersonal->fetch(PDO::FETCH_OBJ);

$usuario_tiene_permiso = verificarPermisoEliminacion($_SESSION['usuario_id'] ?? 0);
?>

<div class="modal fade" id="EliminarPersonal" tabindex="-1" role="dialog" aria-labelledby="EliminarPersonalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: darkorange;">
                <h5 class="modal-title" id="EliminarPersonalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación Permanente
                </h5>
                <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEliminarPersonal" action="procesos/eliminar_personal.php" method="POST" autocomplete="off">
                <input type="hidden" name="IdPersonal" value="<?php echo $personal->IdPersonal; ?>">
                <input type="hidden" name="token" value="<?php echo generarToken(); ?>">
                
                <div class="modal-body">
                    <!-- Advertencia principal -->
                    <div class="alert alert-danger border-danger">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-radiation-alt fa-2x mr-3"></i>
                            <div>
                                <h5 class="alert-heading mb-1">¡ADVERTENCIA CRÍTICA!</h5>
                                <p class="mb-0">Esta acción eliminará permanentemente todos los datos del personal.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información del personal -->
                    <div class="card mb-4 border-danger">
                        <div class="card-header" style="background-color: #f8d7da; color: #721c24;">
                            <i class="fas fa-user-circle mr-2"></i> Información del Personal a Eliminar
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong><i class="fas fa-id-card mr-2"></i> ID:</strong>
                                        <span class="badge badge-dark"><?php echo $personal->IdPersonal; ?></span>
                                    </div>
                                    <div class="info-item mt-2">
                                        <strong><i class="fas fa-user mr-2"></i> Nombre Completo:</strong>
                                        <span><?php echo htmlspecialchars($personal->Nombre . ' ' . $personal->ApPaterno . ' ' . $personal->ApMaterno); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <strong><i class="fas fa-building mr-2"></i> Empresa:</strong>
                                        <span class="badge" style="background-color: <?php echo $personal->ColorEmpresa ?? '#d94f00'; ?>; color: <?php echo getContrastColor($personal->ColorEmpresa ?? '#d94f00'); ?>;">
                                            <?php echo $personal->NomEmpresa; ?>
                                        </span>
                                    </div>
                                    <div class="info-item mt-2">
                                        <strong><i class="fas fa-power-off mr-2"></i> Estatus Actual:</strong>
                                        <span class="badge <?php echo ($personal->Status == 'Activo') ? 'badge-success' : 'badge-danger'; ?>">
                                            <?php echo $personal->Status; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Consecuencias -->
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-circle mr-2"></i> Consecuencias de esta acción:</h6>
                        <ul class="mb-0 pl-3">
                            <li>Eliminación permanente de todos los datos personales</li>
                            <li>Pérdida de historial y registros asociados</li>
                            <li>No se puede recuperar la información eliminada</li>
                            <li>Esta acción quedará registrada en el log del sistema</li>
                        </ul>
                    </div>
                    
                    <!-- Autenticación por contraseña -->
                    <div class="card border-primary">
                        <div class="card-header" style="background-color: #cce5ff; color: #004085;">
                            <i class="fas fa-shield-alt mr-2"></i> Autenticación Requerida
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">
                                            <i class="fas fa-user-lock mr-1"></i> Usuario:
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="username" 
                                               name="username" 
                                               value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>"
                                               readonly
                                               style="background-color: #e9ecef;">
                                        <small class="text-muted">Usuario autenticado</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">
                                            <i class="fas fa-key mr-1"></i> Contraseña de Confirmación:
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password" 
                                                   name="password" 
                                                   required
                                                   autocomplete="new-password"
                                                   placeholder="Ingrese su contraseña">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Ingrese su contraseña actual para confirmar</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Confirmación de eliminación -->
                            <div class="form-group mt-3">
                                <label for="confirmacion">
                                    <i class="fas fa-check-double mr-1"></i> Escriba "ELIMINAR PERMANENTEMENTE" para confirmar:
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="confirmacion" 
                                       name="confirmacion" 
                                       required
                                       placeholder="ELIMINAR PERMANENTEMENTE"
                                       style="font-weight: bold; text-align: center;">
                                <small class="text-muted">Debe escribir exactamente como se muestra</small>
                            </div>
                            
                            <!-- Motivo de eliminación -->
                            <div class="form-group">
                                <label for="motivo_eliminacion">
                                    <i class="fas fa-clipboard-list mr-1"></i> Motivo de la Eliminación:
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="motivo_eliminacion" 
                                          name="motivo_eliminacion" 
                                          rows="3" 
                                          required
                                          placeholder="Describa el motivo por el cual está eliminando este registro..."></textarea>
                                <small class="text-muted">Este motivo quedará registrado en el historial</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Verificación de permisos -->
                    <?php if(!$usuario_tiene_permiso): ?>
                    <div class="alert alert-danger mt-3">
                        <i class="fas fa-ban mr-2"></i>
                        <strong>No tiene permisos para eliminar registros.</strong>
                        <p class="mb-0 mt-1">Contacte al administrador del sistema.</p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>
                    <button type="submit" 
                            class="btn btn-danger" 
                            id="btnConfirmarEliminar" 
                            <?php echo (!$usuario_tiene_permiso) ? 'disabled' : ''; ?>>
                        <i class="fas fa-trash-alt mr-1"></i> Eliminar Permanentemente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CSS adicional para el modal -->
<style>
.info-item {
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item strong {
    min-width: 150px;
    display: inline-block;
    color: #495057;
}

/* Animación para inputs críticos */
@keyframes pulse-warning {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}

#confirmacion, #password {
    animation: pulse-warning 2s infinite;
}

/* Estilo para botón deshabilitado */
.btn-danger:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

<!-- JavaScript para validaciones -->
<script>
$(document).ready(function() {
    // Mostrar/ocultar contraseña
    $('#togglePassword').click(function() {
        var passwordInput = $('#password');
        var type = passwordInput.attr('type');
        var icon = $(this).find('i');
        
        if (type === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Validación en tiempo real
    var confirmacionInput = $('#confirmacion');
    var passwordInput = $('#password');
    var motivoInput = $('#motivo_eliminacion');
    var btnEliminar = $('#btnConfirmarEliminar');
    
    function validarFormulario() {
        var confirmacionValida = confirmacionInput.val().toUpperCase() === 'ELIMINAR PERMANENTEMENTE';
        var passwordValida = passwordInput.val().trim().length >= 6;
        var motivoValido = motivoInput.val().trim().length >= 10;
        
        if (confirmacionValida && passwordValida && motivoValido) {
            btnEliminar.prop('disabled', false);
            return true;
        } else {
            btnEliminar.prop('disabled', true);
            return false;
        }
    }
    
    // Validar en cada cambio
    confirmacionInput.on('input', validarFormulario);
    passwordInput.on('input', validarFormulario);
    motivoInput.on('input', validarFormulario);
    
    // Validación inicial
    validarFormulario();
    
    // Prevenir envío con Enter en campos de confirmación
    $('#confirmacion').keypress(function(e) {
        if (e.which === 13) {
            e.preventDefault();
            return false;
        }
    });
    
    // Confirmación adicional antes de enviar
    $('#formEliminarPersonal').submit(function(e) {
        if (!validarFormulario()) {
            e.preventDefault();
            Swal.fire({
                title: 'Datos incompletos',
                text: 'Por favor complete todos los campos correctamente.',
                icon: 'warning',
                confirmButtonColor: '#d94f00'
            });
            return false;
        }
        
        e.preventDefault();
        
        // Mostrar confirmación final
        Swal.fire({
            title: '¿Está absolutamente seguro?',
            html: `<div class="text-danger text-center">
                      <i class="fas fa-radiation-alt fa-3x mb-3"></i>
                      <p><strong>Esta acción es IRREVERSIBLE</strong></p>
                      <p>Se eliminarán todos los datos de:<br>
                      <strong>${$('#username').val()}</strong></p>
                   </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar permanentemente',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar formulario vía AJAX
                var formData = new FormData(this);
                
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        btnEliminar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Eliminando...');
                    },
                    success: function(response) {
                        try {
                            var data = JSON.parse(response);
                            if (data.success) {
                                Swal.fire({
                                    title: '¡Eliminado!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#d94f00'
                                }).then(() => {
                                    $('.modal').modal('hide');
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#d94f00'
                                });
                                btnEliminar.prop('disabled', false).html('<i class="fas fa-trash-alt mr-1"></i> Eliminar Permanentemente');
                            }
                        } catch (e) {
                            // Si la respuesta no es JSON, recargar la página
                            $('.modal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error del sistema',
                            text: 'Ocurrió un error: ' + error,
                            icon: 'error',
                            confirmButtonColor: '#d94f00'
                        });
                        btnEliminar.prop('disabled', false).html('<i class="fas fa-trash-alt mr-1"></i> Eliminar Permanentemente');
                    }
                });
            }
        });
        
        return false;
    });
});
</script>