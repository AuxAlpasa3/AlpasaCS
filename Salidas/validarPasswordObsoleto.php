<?php
include ('../api/db/conexion.php');

header('Content-Type: application/json');

try {
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['esValida' => false, 'error' => 'Método no permitido']);
        exit;
    }

    // Obtener y validar datos JSON
    $json = file_get_contents('php://input');
    if (empty($json)) {
        http_response_code(400);
        echo json_encode(['esValida' => false, 'error' => 'Datos JSON no proporcionados']);
        exit;
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['esValida' => false, 'error' => 'JSON inválido']);
        exit;
    }

    $password = $data['password'] ?? '';
    $accion = $data['accion'] ?? 'autorizar_obsoleto';

    // Validar campo obligatorio
    if (empty($password)) {
        http_response_code(400);
        echo json_encode(['esValida' => false, 'error' => 'Contraseña requerida']);
        exit;
    }

    $esValida = false;
    $usuarioAutorizado = '';
    $tipoUsuario = '';

    $sentencia = $Conexion->prepare("SELECT 
                                    IdUsuario, 
                                    Contrasenia, 
                                    Usuario,
                                    TipoUsuario 
                                FROM t_usuario 
                                WHERE (TipoUsuario = 1 OR TipoUsuario = 5 OR TipoUsuario = 2) 
                                AND Estatus = 1");
    
    $sentencia->execute();
    $usuarios = $sentencia->fetchAll(PDO::FETCH_OBJ);

    foreach ($usuarios as $usuario) {
        if (password_verify($password, $usuario->Contrasenia)) {
            $esValida = true;
            $usuarioAutorizado = $usuario->Usuario;
            $tipoUsuario = $usuario->TipoUsuario;
            
            break; 
        }
    }

    echo json_encode([
        'esValida' => $esValida,
        'usuario' => $usuarioAutorizado,
        'tipoUsuario' => $tipoUsuario
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error en validarPasswordObsoleto: " . $e->getMessage());
    echo json_encode(['esValida' => false, 'error' => 'Error interno del servidor']);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error general en validarPasswordObsoleto: " . $e->getMessage());
    echo json_encode(['esValida' => false, 'error' => 'Error interno del servidor']);
}

?>