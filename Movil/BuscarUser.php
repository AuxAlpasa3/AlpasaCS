<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Device-Id, Device-Name, Device-Location, Device-Location-Id');

require_once '../api/db/conexion.php';

$usuario = $_GET['usuario'] ?? '';
$password = $_GET['password'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode("Usuario y contraseña son requeridos", JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sentencia = $Conexion->prepare("SELECT * FROM t_usuario WHERE Usuario = ? AND TipoUsuario IN (1,2,3,4,5) AND Estatus = 1");
    $sentencia->execute([$usuario]);
    $client = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($client) > 0) {
        $usuarioEncontrado = false;
        $resultado = array();
        
        foreach ($client as $row) {
            if (password_verify($password, $row->Contrasenia)) {
                $usuarioEncontrado = true;
                
                $updateSesion = $Conexion->prepare("UPDATE t_usuario SET UltimaSesion = GETDATE() WHERE IdUsuario = ?");
                $updateSesion->execute([$row->IdUsuario]);
                
                $resultado = array(array(
                    'IdUsuario' => $row->IdUsuario,
                    'Usuario' => $row->Usuario,
                    'Descripcion' => $row->Descripcion,
                    'TipoUsuario' => $row->TipoUsuario,
                    'Estatus' => $row->Estatus,
                    'rol' => $row->rol,
                    'Sesion' => $row->Sesion,
                    'UltimaSesion' => $row->UltimaSesion,
                    'CreateDate' => $row->CreateDate
                ));
                
                break;
            }
        }
        
        if (!$usuarioEncontrado) {
            $resultado = "Usuario o Contraseña Incorrecto, Favor de validar la información";
        }
        
    } else {
        $resultado = "El Usuario no existe, Favor de validar con el Administrador";
    }
    
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode("Error en el servidor: " . $e->getMessage(), JSON_UNESCAPED_UNICODE);
}
?>