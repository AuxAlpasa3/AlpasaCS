<?php
// Movil/BuscarUser.php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Device-Id, Device-Name, Device-Location, Device-Location-Id');

Include '../api/db/conexion.php';

// Obtener parámetros
$usuario = $_GET['usuario'] ?? '';
$password = $_GET['password'] ?? '';

// Obtener headers del dispositivo
$deviceId = $_SERVER['HTTP_DEVICE_ID'] ?? '';
$deviceName = $_SERVER['HTTP_DEVICE_NAME'] ?? '';
$deviceLocation = $_SERVER['HTTP_DEVICE_LOCATION'] ?? '';
$deviceLocationId = $_SERVER['HTTP_DEVICE_LOCATION_ID'] ?? '';

if (empty($usuario) || empty($password)) {
    echo json_encode("Usuario y contraseña son requeridos", JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // Buscar usuario
    $sentencia = $Conexion->prepare("SELECT * FROM t_usuario WHERE Usuario = ? AND TipoUsuario IN (1,2,3,4,5) AND Estatus = 1");
    $sentencia->execute([$usuario]);
    $client = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    $rowCount = $sentencia->rowCount();
    
    if ($rowCount > 0) {
        $datos = array();
        $usuarioValido = false;
        
        foreach ($client as $row) {
            if (password_verify($password, $row->Contrasenia)) {
                $usuarioValido = true;
                
                // Actualizar última sesión en t_usuario
                $sentenciaActualizar = $Conexion->prepare("
                    UPDATE t_usuario 
                    SET UltimaSesion = GETDATE() 
                    WHERE IdUsuario = ?");
                $sentenciaActualizar->execute([$row->IdUsuario]);
                
                // REGISTRAR SESIÓN EN t_sesiones_dispositivos
                if (!empty($deviceId)) {
                    registrarSesionDispositivo($Conexion, $row->IdUsuario, $deviceId, $deviceName, $deviceLocation, $deviceLocationId);
                }
                
                // Preparar respuesta
                array_push($datos, array(
                    'IdUsuario' => $row->IdUsuario,
                    'Usuario' => $row->Usuario,
                    'Descripcion' => $row->Descripcion,
                    'TipoUsuario' => $row->TipoUsuario,
                    'Estatus' => $row->Estatus,
                    'rol' => $row->rol,
                    'Sesion' => $row->Sesion,
                    'UltimaSesion' => $row->UltimaSesion,
                    'CreateDate' => $row->CreateDate,
                    'Correo' => $row->Correo,
                    'NombreColaborador' => $row->NombreColaborador,
                    'DeviceInfo' => array(
                        'DeviceId' => $deviceId,
                        'DeviceName' => $deviceName,
                        'DeviceLocation' => $deviceLocation,
                        'DeviceLocationId' => $deviceLocationId
                    )
                ));
                
                break; 
            }
        }
        
        if (!$usuarioValido) {
            $datos = "Usuario o Contraseña Incorrecto, Favor de validar la información";
        }
        
    } else {
        $datos = "El Usuario no existe, Favor de validar con el Administrador";
    }
    
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode("Error interno del servidor: " . $e->getMessage(), JSON_UNESCAPED_UNICODE);
}

/**
 * Función para registrar sesión en t_sesiones_dispositivos
 */
function registrarSesionDispositivo($conexion, $idUsuario, $deviceId, $deviceName, $deviceLocation, $deviceLocationId) {
    try {
        $cerrarSesiones = $conexion->prepare("
            UPDATE t_sesiones_dispositivos 
            SET FechaLogout = GETDATE(), 
                Activa = 0 
            WHERE IdUsuario = ? 
            AND IdDispositivo = ? 
            AND Activa = 1");
        $cerrarSesiones->execute([$idUsuario, $deviceId]);
        
        $insertarSesion = $conexion->prepare("
            INSERT INTO t_sesiones_dispositivos 
            (IdUsuario, IdDispositivo, NombreDispositivo, IdUbicacion, NombreUbicacion, FechaLogin, Activa)
            VALUES (?, ?, ?, ?, ?, GETDATE(), 1)");
        
        $insertarSesion->execute([
            $idUsuario, 
            $deviceId, 
            $deviceName, 
            $deviceLocationId, 
            $deviceLocation
        ]);
        
    } catch (Exception $e) {
        error_log("Error al registrar sesión de dispositivo: " . $e->getMessage());
    }
}
?>