<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Device-Id, Device-Name, Device-Location, Device-Location-Id');

require_once '../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        // También verificar datos de headers
        $deviceHeaders = [
            'Device-Id' => $_SERVER['HTTP_DEVICE_ID'] ?? '',
            'Device-Name' => $_SERVER['HTTP_DEVICE_NAME'] ?? '',
            'Device-Location' => $_SERVER['HTTP_DEVICE_LOCATION'] ?? '',
            'Device-Location-Id' => $_SERVER['HTTP_DEVICE_LOCATION_ID'] ?? ''
        ];
        
        // Validar que tenemos datos mínimos
        $action = $input['action'] ?? '';
        $IdUsuario = $input['IdUsuario'] ?? '';
        
        if (empty($IdUsuario) || empty($action)) {
            echo json_encode([
                'success' => false, 
                'error' => 'Datos incompletos: IdUsuario y action son requeridos'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $IdDispositivo = !empty($deviceHeaders['Device-Id']) ? $deviceHeaders['Device-Id'] : ($input['IdDispositivo'] ?? 'unknown');
        $NombreDispositivo = !empty($deviceHeaders['Device-Name']) ? $deviceHeaders['Device-Name'] : ($input['NombreDispositivo'] ?? 'Dispositivo móvil');
        $NombreUbicacion = !empty($deviceHeaders['Device-Location']) ? $deviceHeaders['Device-Location'] : ($input['NombreUbicacion'] ?? 'Ubicación desconocida');
        $IdUbicacion = !empty($deviceHeaders['Device-Location-Id']) ? $deviceHeaders['Device-Location-Id'] : ($input['IdUbicacion'] ?? '');
        $source = $input['source'] ?? 'app';
        
        $checkTable = $Conexion->query("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 't_sesiones_dispositivos'");
        $tableExists = $checkTable->fetch(PDO::FETCH_ASSOC);
        
        if ($tableExists['existe'] == 0) {
            // Crear tabla si no existe
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
        }
        
        if ($action === 'login') {
            $stmtClose = $Conexion->prepare("
                UPDATE t_sesiones_dispositivos 
                SET FechaLogout = GETDATE(), 
                    Activa = 0 
                WHERE IdUsuario = ? 
                AND IdDispositivo = ? 
                AND Activa = 1
            ");
            
            $stmtClose->execute([$IdUsuario, $IdDispositivo]);
            
            $stmt = $Conexion->prepare("
                INSERT INTO t_sesiones_dispositivos 
                (IdUsuario, IdDispositivo, NombreDispositivo, IdUbicacion, NombreUbicacion, FechaLogin, Activa)
                VALUES (?, ?, ?, ?, ?, GETDATE(), 1)
            ");
            
            $stmt->execute([
                $IdUsuario,
                $IdDispositivo,
                $NombreDispositivo,
                $IdUbicacion,
                $NombreUbicacion
            ]);
            
            $updateUsuario = $Conexion->prepare("
                UPDATE t_usuario 
                SET UltimaSesion = GETDATE() 
                WHERE IdUsuario = ?
            ");
            $updateUsuario->execute([$IdUsuario]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Sesión de login registrada exitosamente',
                'data' => [
                    'IdUsuario' => $IdUsuario,
                    'IdDispositivo' => $IdDispositivo,
                    'FechaLogin' => date('Y-m-d H:i:s'),
                    'source' => $source
                ]
            ], JSON_UNESCAPED_UNICODE);
            
        } elseif ($action === 'logout') {
            $stmtFind = $Conexion->prepare("
                SELECT TOP 1 IdSesion 
                FROM t_sesiones_dispositivos 
                WHERE IdUsuario = ? 
                AND IdDispositivo = ? 
                AND Activa = 1
                ORDER BY FechaLogin DESC
            ");
            
            $stmtFind->execute([$IdUsuario, $IdDispositivo]);
            $session = $stmtFind->fetch(PDO::FETCH_ASSOC);
            
            if ($session && isset($session['IdSesion'])) {
                // Cerrar sesión existente
                $IdSesion = $session['IdSesion'];
                
                $stmtUpdate = $Conexion->prepare("
                    UPDATE t_sesiones_dispositivos 
                    SET FechaLogout = GETDATE(), 
                        Activa = 0 
                    WHERE IdSesion = ?
                ");
                
                $stmtUpdate->execute([$IdSesion]);
                
                $message = 'Sesión de logout registrada exitosamente';
            } else {
                $stmtInsert = $Conexion->prepare("
                    INSERT INTO t_sesiones_dispositivos 
                    (IdUsuario, IdDispositivo, NombreDispositivo, IdUbicacion, NombreUbicacion, 
                     FechaLogin, FechaLogout, Activa)
                    VALUES (?, ?, ?, ?, ?, DATEADD(HOUR, -1, GETDATE()), GETDATE(), 0)
                ");
                
                $stmtInsert->execute([
                    $IdUsuario,
                    $IdDispositivo,
                    $NombreDispositivo,
                    $IdUbicacion,
                    $NombreUbicacion
                ]);
                
                $message = 'Registro histórico de sesión creado (no había sesión activa)';
            }
            
            echo json_encode([
                'success' => true, 
                'message' => $message,
                'data' => [
                    'IdUsuario' => $IdUsuario,
                    'IdDispositivo' => $IdDispositivo,
                    'FechaLogout' => date('Y-m-d H:i:s'),
                    'source' => $source
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false, 
                'error' => 'Acción no válida. Debe ser "login" o "logout"'
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false, 
            'error' => 'Error de base de datos',
            'details' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'error' => 'Error en el servidor',
            'details' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    
} elseif ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    
    try {
        // Verificar si la tabla existe
        $checkTable = $Conexion->query("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 't_sesiones_dispositivos'");
        $tableExists = $checkTable->fetch(PDO::FETCH_ASSOC);
        
        if ($tableExists['existe'] == 0) {
            echo json_encode([
                'success' => true, 
                'message' => 'Tabla de sesiones no existe aún',
                'sessions' => [],
                'count' => 0
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        if ($action === 'get_active_sessions') {
            $stmt = $Conexion->prepare("
                SELECT 
                    s.*,
                    u.Descripcion as NombreUsuario,
                    u.Usuario as UsuarioLogin
                FROM t_sesiones_dispositivos s
                LEFT JOIN t_usuario u ON s.IdUsuario = u.IdUsuario
                WHERE s.Activa = 1
                ORDER BY s.FechaLogin DESC
            ");
            
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true, 
                'sessions' => $sessions,
                'count' => count($sessions)
            ], JSON_UNESCAPED_UNICODE);
            
        } elseif ($action === 'check_session') {
            $IdUsuario = $_GET['IdUsuario'] ?? '';
            $IdDispositivo = $_GET['IdDispositivo'] ?? '';
            
            if (empty($IdUsuario) || empty($IdDispositivo)) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'IdUsuario e IdDispositivo son requeridos'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $stmt = $Conexion->prepare("
                SELECT TOP 1 *
                FROM t_sesiones_dispositivos 
                WHERE IdUsuario = ? 
                AND IdDispositivo = ? 
                AND Activa = 1
                ORDER BY FechaLogin DESC
            ");
            
            $stmt->execute([$IdUsuario, $IdDispositivo]);
            $session = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($session) {
                echo json_encode([
                    'success' => true, 
                    'hasActiveSession' => true,
                    'session' => $session
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => true, 
                    'hasActiveSession' => false,
                    'message' => 'No hay sesión activa'
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } else {
            echo json_encode([
                'success' => false, 
                'error' => 'Acción no válida para GET',
                'available_actions' => [
                    'get_active_sessions',
                    'check_session'
                ]
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false, 
            'error' => 'Error de base de datos',
            'details' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    
} else {
    echo json_encode([
        'success' => false, 
        'error' => 'Método no permitido',
        'method' => $method
    ], JSON_UNESCAPED_UNICODE);
}
?>