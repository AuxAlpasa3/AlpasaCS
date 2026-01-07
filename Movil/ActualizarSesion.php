<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../api/db/conexion.php';

$idUsuario = $_GET['idUsuario'] ?? '';
$deviceId = $_GET['deviceId'] ?? '';
$deviceName = $_GET['deviceName'] ?? '';
$deviceLocation = $_GET['deviceLocation'] ?? '';
$deviceLocationId = $_GET['deviceLocationId'] ?? '';

if (empty($idUsuario) || empty($deviceId)) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $updateUsuario = $Conexion->prepare("UPDATE t_usuario SET UltimaSesion = GETDATE() WHERE IdUsuario = ?");
    $updateUsuario->execute([$idUsuario]);
    
    $checkTable = $Conexion->query("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 't_sesiones_dispositivos'");
    $tableExists = $checkTable->fetch(PDO::FETCH_ASSOC);
    
    if ($tableExists['existe'] > 0) {
        $verificarSesion = $Conexion->prepare("
            SELECT TOP 1 IdSesion 
            FROM t_sesiones_dispositivos 
            WHERE IdUsuario = ? 
            AND IdDispositivo = ? 
            AND Activa = 1
        ");
        $verificarSesion->execute([$idUsuario, $deviceId]);
        $sesionExistente = $verificarSesion->fetch(PDO::FETCH_ASSOC);
        
        if ($sesionExistente) {
            $updateSesion = $Conexion->prepare("
                UPDATE t_sesiones_dispositivos 
                SET NombreDispositivo = ?,
                    IdUbicacion = ?,
                    NombreUbicacion = ?,
                    FechaLogin = GETDATE()
                WHERE IdSesion = ?
            ");
            $updateSesion->execute([$deviceName, $deviceLocationId, $deviceLocation, $sesionExistente['IdSesion']]);
            $message = "Sesión actualizada";
        } else {
            $insertSesion = $Conexion->prepare("
                INSERT INTO t_sesiones_dispositivos 
                (IdUsuario, IdDispositivo, NombreDispositivo, IdUbicacion, NombreUbicacion, FechaLogin, Activa)
                VALUES (?, ?, ?, ?, ?, GETDATE(), 1)
            ");
            $insertSesion->execute([$idUsuario, $deviceId, $deviceName, $deviceLocationId, $deviceLocation]);
            $message = "Nueva sesión creada";
        }
    } else {
        $message = "Tabla de sesiones no existe";
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar sesión',
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>