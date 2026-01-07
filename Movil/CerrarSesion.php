<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Obtener parámetros de la URL
$idUsuario = $_GET['idUsuario'] ?? '';
$deviceId = $_GET['deviceId'] ?? '';
$deviceName = $_GET['deviceName'] ?? '';
$deviceLocationId = $_GET['deviceLocationId'] ?? '';

if (empty($idUsuario) || empty($deviceId)) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos: idUsuario y deviceId son requeridos'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 1. Verificar si la tabla de sesiones existe
    $checkTable = $Conexion->query("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 't_sesiones_dispositivos'");
    $tableExists = $checkTable->fetch(PDO::FETCH_ASSOC);
    
    $responseData = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'user_data' => [
            'IdUsuario' => $idUsuario,
            'DeviceId' => $deviceId
        ]
    ];
    
    if ($tableExists['existe'] > 0) {
        // 2. Cerrar sesiones activas para este usuario-dispositivo
        $cerrarSesiones = $Conexion->prepare("
            UPDATE t_sesiones_dispositivos 
            SET FechaLogout = GETDATE(), 
                Activa = 0 
            WHERE IdUsuario = ? 
            AND IdDispositivo = ? 
            AND Activa = 1
        ");
        
        $cerrarSesiones->execute([$idUsuario, $deviceId]);
        $rowsAffected = $cerrarSesiones->rowCount();
        
        // 3. Si se proporcionó deviceName y deviceLocationId, actualizar también
        if (!empty($deviceName) && !empty($deviceLocationId)) {
            $actualizarInfo = $Conexion->prepare("
                UPDATE t_sesiones_dispositivos 
                SET NombreDispositivo = ?,
                    IdUbicacion = ?
                WHERE IdUsuario = ? 
                AND IdDispositivo = ?
                AND Activa = 0 
                AND FechaLogout IS NOT NULL
                AND IdSesion = (SELECT MAX(IdSesion) FROM t_sesiones_dispositivos 
                               WHERE IdUsuario = ? AND IdDispositivo = ?)
            ");
            
            $actualizarInfo->execute([$deviceName, $deviceLocationId, $idUsuario, $deviceId, $idUsuario, $deviceId]);
        }
        
        if ($rowsAffected > 0) {
            $responseData['message'] = 'Sesión cerrada correctamente';
            $responseData['session_closed'] = true;
            $responseData['rows_affected'] = $rowsAffected;
        } else {
            $responseData['message'] = 'No se encontró sesión activa para cerrar';
            $responseData['session_closed'] = false;
            $responseData['rows_affected'] = 0;
        }
        
        $responseData['table_exists'] = true;
        
    } else {
        $responseData['message'] = 'Tabla de sesiones no existe';
        $responseData['session_closed'] = false;
        $responseData['table_exists'] = false;
    }
    
    echo json_encode($responseData, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Error específico de base de datos
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos',
        'error_code' => $e->getCode(),
        'error_message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Error general
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
}
?>