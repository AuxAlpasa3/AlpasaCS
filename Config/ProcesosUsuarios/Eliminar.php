<?php
    Include_once "../../templates/Sesion.php";

    $IdUsuarioNum = $_GET['IdUsuario'] ?? 0;

    $sentUsuarios = $Conexion->query("SELECT t1.IdUsuario AS IdUsuarioNum, t1.Correo, t1.Usuario, t1.NombreColaborador, t1.TipoUsuario as TipoUsuarioNum, t2.TipoUsuario, t1.Contrasenia, t1.Estatus FROM t_usuario as t1 inner join t_tipoUsuario as t2 on t1.TipoUsuario=t2.IdTipoUsuario where IdUsuario=$IdUsuarioNum");
    $Usuarios = $sentUsuarios->fetchAll(PDO::FETCH_OBJ);
    
    foreach($Usuarios as $Usuario){
        $IdUsuario=$Usuario->IdUsuario;
        $estaActivo = ($Usuario->Estatus == 1); 
?>
<div class="modal fade" id="EliminarUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Eliminar Usuario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="EliminarUsuario" id="EliminarUsuario" method="POST" enctype="multipart/form-data">
                    <div class="row" style="align-content: center;">
                        <div class="col-md-12" style="text-align: center;">
                            <?php if ($estaActivo): ?>
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i> No se puede eliminar un usuario activo.
                                </div>
                                <p>Debe desactivar al usuario primero antes de eliminarlo.</p>
                            <?php else: ?>
                                <label for="Id">¿Estás seguro de eliminar a este usuario?</label>
                                    <div class="alert alert-info mt-2" style="background-color: #d94f00;">
                                    <i class="fas fa-info-circle"></i> Para confirmar esta acción, ingrese su contraseña.
                                </div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <input type="hidden" id="user" name="user" value="<?php echo $IdUsuario; ?>">
                                <input type="hidden" id="IdUsuario" name="IdUsuario" value="<?php echo $IdUsuarioNum; ?>">

                                <label for="TUsuario">Usuario:</label>
                                <input id="Usuario" class="form-control" type="text" name="Usuario" value="<?php echo $Usuario->Usuario;?>" readonly>
                                <label for="TUsuario">Nombre Colaborador:</label>
                                <input id="NomColab" class="form-control" type="text" name="NomColab" value="<?php echo $Usuario->NombreColaborador; ?>" readonly>
                                
                                <?php if (!$estaActivo): ?>
                                    <label for="contraseniaConfirmacion" class="mt-3">Contraseña de confirmación:</label>
                                    <input id="contraseniaConfirmacion" class="form-control" type="password" name="contraseniaConfirmacion" required>
                                    <div id="errorContrasenia" class="text-danger mt-2" style="display: none;">
                                        <i class="fas fa-times-circle"></i> Contraseña incorrecta
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align: center;">
                            <div class="form-group">
                                <?php if (!$estaActivo): ?>
                                    <button class="btn btn-success" type="button" id="btnConfirmarEliminacion">Confirmar Eliminación</button>
                                <?php endif; ?>
                                <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#btnConfirmarEliminacion').click(function() {
        var contrasenia = $('#contraseniaConfirmacion').val();
        
        if (!contrasenia) {
            $('#errorContrasenia').text('Por favor ingrese su contraseña').show();
            return;
        }
        
        $.ajax({
            url: 'ProcesosUsuarios/VerificarContra.php',
            type: 'POST',
            data: {
                contrasenia: contrasenia
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    $('#EliminarUsuario form').append('<input type="hidden" name="Mov" value="EliminarUsuario">');
                    $('#EliminarUsuario form').submit();
                } else {
                    $('#errorContrasenia').text('Contraseña incorrecta').show();
                }
            },
            error: function() {
                $('#errorContrasenia').text('Error al verificar la contraseña').show();
            }
        });
    });

    $('#contraseniaConfirmacion').on('input', function() {
        $('#errorContrasenia').hide();
    });
});
</script>
<?php  
    }
?>