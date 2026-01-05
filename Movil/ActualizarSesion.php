<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

Include '../api/db/conexion.php';

$idUsuario = $_GET['idUsuario'] ?? '';
$deviceId = $_GET['deviceId'] ?? '';
$deviceName = $_GET['deviceName'] ?? '';
$deviceLocation = $_GET['deviceLocation'] ?? '';
$deviceLocationId = $_GET['deviceLocationId'] ?? '';

if (empty($idUsuario) || empty($deviceId)) {
    echo json_encode("Parámetros incompletos", JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sentenciaUsuario = $conexion->prepare("
        UPDATE t_usuario 
        SET UltimaSesion = GETDATE() 
        WHERE IdUsuario = ?");
    $sentenciaUsuario->execute([$idUsuario]);
    
    $actualizarSesion = $conexion->prepare("
        UPDATE t_sesiones_dispositivos 
        SET NombreDispositivo = ?,
            IdUbicacion = ?,
            NombreUbicacion = ?,
            FechaLogin = GETDATE()
        WHERE IdUsuario = ? 
        AND IdDispositivo = ? 
        AND Activa = 1");
    
    $actualizarSesion->execute([
        $deviceName, 
        $deviceLocationId, 
        $deviceLocation, 
        $idUsuario, 
        $deviceId
    ]);
    
    if ($actualizarSesion->rowCount() == 0) {
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
    }
    
    $respuesta = array(
        'success' => true,
        'message' => 'Sesión actualizada correctamente',
        'timestamp' => date('Y-m-d H:i:s')
    );
    
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Error al actualizar sesión: ' . $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
?>