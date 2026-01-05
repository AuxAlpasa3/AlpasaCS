<?php
// Movil/ActualizarSesion.php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Device-Id, Device-Name, Device-Location, Device-Location-Id');

Include '../api/db/conexion.php';

// Obtener parámetros
$idUsuario = $_GET['idUsuario'] ?? '';
$deviceId = $_GET['deviceId'] ?? '';
$deviceName = $_GET['deviceName'] ?? '';
$deviceLocation = $_GET['deviceLocation'] ?? '';
$deviceLocationId = $_GET['deviceLocationId'] ?? '';

if (empty($idUsuario) || empty($deviceId)) {
    echo json_encode(array('success' => false, 'message' => 'Parámetros incompletos'), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 1. Actualizar última sesión en t_usuario
    $updateUsuario = $Conexion->prepare("UPDATE t_usuario SET UltimaSesion = GETDATE() WHERE IdUsuario = ?");
    $updateUsuario->execute([$idUsuario]);
    
    // 2. Verificar si la tabla de sesiones existe
    $checkTable = $Conexion->query("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 't_sesiones_dispositivos'");
    $tableExists = $checkTable->fetch(PDO::FETCH_ASSOC);
    
    if ($tableExists['existe'] > 0) {
        // 3. Verificar si existe sesión activa
        $verificarSesion = $Conexion->prepare("
            SELECT TOP 1 IdSesion 
            FROM t_sesiones_dispositivos 
            WHERE IdUsuario = ? 
            AND IdDispositivo = ? 
            AND Activa = 1
        ");
        $verificarSesion->execute([$idUsuario, $deviceId]);
        $sesionExistente = $verificarSesion->fetch(PDO::FETCH_ASSOC);
        
        if ($sesionExistente && isset($sesionExistente['IdSesion'])) {
            // Actualizar sesión existente
            $updateSesion = $Conexion->prepare("
                UPDATE t_sesiones_dispositivos 
                SET NombreDispositivo = ?,
                    IdUbicacion = ?,
                    NombreUbicacion = ?,
                    FechaLogin = GETDATE()
                WHERE IdSesion = ?
            ");
            
            $updateSesion->execute([$deviceName, $deviceLocationId, $deviceLocation, $sesionExistente['IdSesion']]);
            
            $message = "Sesión existente actualizada";
        } else {
            // Crear nueva sesión
            $insertSesion = $Conexion->prepare("
                INSERT INTO t_sesiones_dispositivos 
                (IdUsuario, IdDispositivo, NombreDispositivo, IdUbicacion, NombreUbicacion, FechaLogin, Activa)
                VALUES (?, ?, ?, ?, ?, GETDATE(), 1)
            ");
            
            $insertSesion->execute([$idUsuario, $deviceId, $deviceName, $deviceLocationId, $deviceLocation]);
            
            $message = "Nueva sesión creada";
        }
    } else {
      
        $createTable = $Conexion->prepare("
            CREATE TABLE t_sesiones_dispositivos (
                IdSesion INT IDENTITY(1,1) PRIMARY KEY,
                IdUsuario INT NOT NULL,
                IdDispositivo NVARCHAR(255) NOT NULL,
                NombreDispositivo NVARCHAR(100),
                IdUbicacion NVARCHAR(100),
                NombreUbicacion NVARCHAR(255),
                FechaLogin DATETIME DEFAULT GETDATE(),
                FechaLogout DATETIME NULL,
                Activa BIT DEFAULT 1,
                FOREIGN KEY (IdUsuario) REFERENCES t_usuario(IdUsuario)
            )
        ");
        $createTable->execute();
        
        $insertSesion = $Conexion->prepare("
            INSERT INTO t_sesiones_dispositivos 
            (IdUsuario, IdDispositivo, NombreDispositivo, IdUbicacion, NombreUbicacion, FechaLogin, Activa)
            VALUES (?, ?, ?, ?, ?, GETDATE(), 1)
        ");
        
        $insertSesion->execute([$idUsuario, $deviceId, $deviceName, $deviceLocationId, $deviceLocation]);
        
        $message = "Tabla creada y sesión registrada";
    }
    
    echo json_encode(array(
        'success' => true,
        'message' => $message,
        'data' => [
            'IdUsuario' => $idUsuario,
            'DeviceId' => $deviceId,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ), JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Error al actualizar sesión: ' . $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
?>