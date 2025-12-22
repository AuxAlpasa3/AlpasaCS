<?php
    Include_once "../../templates/Sesion.php";

    // Validar y obtener el ID de usuario de forma segura
    $IdUsuarioNum = isset($_GET['IdUsuario']) ? (int)$_GET['IdUsuario'] : 0;
    
    // Validar que el ID sea válido
    if ($IdUsuarioNum <= 0) {
        die("ID de usuario no válido");
    }
    
    // Preparar consulta para prevenir inyecciones SQL
    $sentUsuarios = $Conexion->prepare("SELECT t1.IdUsuario AS IdUsuarioNum, t1.Correo, t1.Usuario, t1.NombreColaborador, t1.TipoUsuario as TipoUsuarioNum, t2.TipoUsuario, t1.Contrasenia 
                                       FROM t_usuario as t1 
                                       INNER JOIN t_tipoUsuario as t2 ON t1.TipoUsuario=t2.IdTipoUsuario 
                                       WHERE t1.IdUsuario = :idUsuario");
    $sentUsuarios->bindParam(':idUsuario', $IdUsuarioNum, PDO::PARAM_INT);
    $sentUsuarios->execute();
    
    $Usuarios = $sentUsuarios->fetchAll(PDO::FETCH_OBJ);
    
    if (count($Usuarios) === 0) {
        die("Usuario no encontrado");
    }
    
    $Usuario = $Usuarios[0];
    $IdUsuario = $Usuario->IdUsuarioNum;
?>
<div class="modal fade" id="ContraseniaUsuario" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #d94f00;">
                <h5 class="modal-title text-white" id="title">Nueva Contraseña</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form name="ModificarPassword" id="ModificarPassword" method="POST">
                    <div class="row" style="align-content: center;">
                        <input type="hidden" id="user" name="user" value="<?php echo htmlspecialchars($IdUsuario); ?>">
                        <div class="col-md-6" style="text-align: center;">
                            <div class="form-group">
                                <label for="pass1">Contraseña:</label>
                                <input id="pass1" class="form-control" type="Password" name="pass1" required>
                            </div>
                        </div>
                        <div class="col-md-6" style="text-align: center;">
                            <div class="form-group">
                                <label for="pass2">Vuelve a Escribir Contraseña:</label>
                                <input id="pass2" class="form-control" type="Password" name="pass2" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="text-align: center;">
                        <div class="form-group">
                            <button class="btn btn-success" type="submit" name="Mov" value="ModificarPassword" id="Mov">Modificar</button>
                            <button class="btn btn-danger" id="cancelar" type="button" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>