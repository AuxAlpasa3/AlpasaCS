<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Habilitar logging para debug
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Incluir conexión a SQL Server
require_once '../conexion/conexion.php';

// Función para registrar en log
function logMessage($message) {
    $logFile = __DIR__ . '/../logs/sesiones_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Manejar solicitud OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    try {
        // Obtener datos del request
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        logMessage("Datos recibidos: " . json_encode($input));
        
        if (empty($input)) {
            echo json_encode(['success' => false, 'error' => 'Datos inválidos o vacíos']);
            exit;
        }
        
        $action = $input['action'] ?? '';
        $IdUsuario = $input['IdUsuario'] ?? '';
        $IdDispositivo = $input['IdDispositivo'] ?? '';
        $NombreDispositivo = $input['NombreDispositivo'] ?? 'Dispositivo móvil';
        $IdUbicacion = $input['IdUbicacion'] ?? '';
        $NombreUbicacion = $input['NombreUbicacion'] ?? 'Ubicación desconocida';
        $FechaLogin = $input['FechaLogin'] ?? '';
        $FechaLogout = $input['FechaLogout'] ?? null;
        $Activa = isset($input['Activa']) ? (int)$input['Activa'] : 1;
        
        // Validar datos requeridos
        if (empty($IdUsuario) || empty($IdDispositivo)) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos: IdUsuario e IdDispositivo son requeridos']);
            exit;
        }
        
        // Validar acción
        if (!in_array($action, ['login', 'logout'])) {
            echo json_encode(['success' => false, 'error' => 'Acción no válida. Debe ser login o logout']);
            exit;
        }
        
        // Procesar según la acción
        if ($action === 'login') {
            // Primero, cerrar cualquier sesión activa previa para este usuario-dispositivo
            $stmtClose = $conn->prepare("
                UPDATE t_sesiones_dispositivos 
                SET FechaLogout = GETDATE(), 
                    Activa = 0 
                WHERE IdUsuario = ? 
                AND IdDispositivo = ? 
                AND Activa = 1
            ");
            
            $stmtClose->execute([$IdUsuario, $IdDispositivo]);
            logMessage("Sesiones anteriores cerradas para usuario $IdUsuario en dispositivo $IdDispositivo");
            
            // Registrar nueva sesión
            $stmt = $conn->prepare("
                INSERT INTO t_sesiones_dispositivos 
                (IdUsuario, IdDispositivo, NombreDispositivo, IdUbicacion, NombreUbicacion, FechaLogin, Activa)
                OUTPUT INSERTED.IdSesion
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            // Formatear fecha de login
            if (empty($FechaLogin)) {
                $fechaLoginSQL = date('Y-m-d H:i:s');
            } else {
                $fechaLoginSQL = date('Y-m-d H:i:s', strtotime($FechaLogin));
            }
            
            $params = [
                $IdUsuario,
                $IdDispositivo,
                $NombreDispositivo,
                $IdUbicacion,
                $NombreUbicacion,
                $fechaLoginSQL,
                $Activa
            ];
            
            $stmt->execute($params);
            
            // Obtener el ID insertado
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $IdSesion = $row['IdSesion'] ?? null;
            
            logMessage("Nueva sesión registrada: IdSesion=$IdSesion, Usuario=$IdUsuario, Dispositivo=$IdDispositivo");
            
            echo json_encode([
                'success' => true, 
                'message' => 'Sesión registrada exitosamente',
                'IdSesion' => $IdSesion,
                'data' => [
                    'IdUsuario' => $IdUsuario,
                    'IdDispositivo' => $IdDispositivo,
                    'FechaLogin' => $fechaLoginSQL
                ]
            ]);
            
        } elseif ($action === 'logout') {
            // Buscar sesión activa para cerrar
            $stmtFind = $conn->prepare("
                SELECT IdSesion 
                FROM t_sesiones_dispositivos 
                WHERE IdUsuario = ? 
                AND IdDispositivo = ? 
                AND Activa = 1
                ORDER BY FechaLogin DESC
            ");
            
            $stmtFind->execute([$IdUsuario, $IdDispositivo]);
            $session = $stmtFind->fetch(PDO::FETCH_ASSOC);
            
            if ($session) {
                // Cerrar sesión existente
                $IdSesion = $session['IdSesion'];
                
                $stmtUpdate = $conn->prepare("
                    UPDATE t_sesiones_dispositivos 
                    SET FechaLogout = ?, 
                        Activa = 0 
                    WHERE IdSesion = ?
                ");
                
                // Formatear fecha de logout
                if (empty($FechaLogout)) {
                    $fechaLogoutSQL = date('Y-m-d H:i:s');
                } else {
                    $fechaLogoutSQL = date('Y-m-d H:i:s', strtotime($FechaLogout));
                }
                
                $stmtUpdate->execute([$fechaLogoutSQL, $IdSesion]);
                
                logMessage("Sesión cerrada: IdSesion=$IdSesion, Usuario=$IdUsuario");
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Sesión cerrada exitosamente',
                    'IdSesion' => $IdSesion,
                    'data' => [
                        'IdUsuario' => $IdUsuario,
                        'IdDispositivo' => $IdDispositivo,
                        'FechaLogout' => $fechaLogoutSQL
                    ]
                ]);
            } else {
                // No se encontró sesión activa, crear registro histórico
                $stmtInsert = $conn->prepare("
                    INSERT INTO t_sesiones_dispositivos 
                    (IdUsuario, IdDispositivo, NombreDispositivo, IdUbicacion, NombreUbicacion, 
                     FechaLogin, FechaLogout, Activa)
                    OUTPUT INSERTED.IdSesion
                    VALUES (?, ?, ?, ?, ?, DATEADD(HOUR, -1, GETDATE()), ?, 0)
                ");
                
                // Formatear fecha de logout
                if (empty($FechaLogout)) {
                    $fechaLogoutSQL = date('Y-m-d H:i:s');
                } else {
                    $fechaLogoutSQL = date('Y-m-d H:i:s', strtotime($FechaLogout));
                }
                
                $params = [
                    $IdUsuario,
                    $IdDispositivo,
                    $NombreDispositivo ?? 'Dispositivo móvil',
                    $IdUbicacion ?? '',
                    $NombreUbicacion ?? 'Ubicación desconocida',
                    $fechaLogoutSQL
                ];
                
                $stmtInsert->execute($params);
                
                $row = $stmtInsert->fetch(PDO::FETCH_ASSOC);
                $IdSesion = $row['IdSesion'] ?? null;
                
                logMessage("Sesión histórica creada: IdSesion=$IdSesion, Usuario=$IdUsuario (no se encontró sesión activa)");
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Registro histórico de sesión creado',
                    'IdSesion' => $IdSesion,
                    'note' => 'No se encontró sesión activa, se creó registro histórico'
                ]);
            }
        }
        
    } catch (PDOException $e) {
        $errorMessage = $e->getMessage();
        logMessage("Error PDO: $errorMessage");
        
        echo json_encode([
            'success' => false, 
            'error' => 'Error de base de datos',
            'debug' => $errorMessage
        ]);
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        logMessage("Error general: $errorMessage");
        
        echo json_encode([
            'success' => false, 
            'error' => 'Error en el servidor',
            'debug' => $errorMessage
        ]);
    }
    
} elseif ($method === 'GET') {
    // Para obtener sesiones (si necesitas esta funcionalidad)
    $action = $_GET['action'] ?? '';
    
    try {
        if ($action === 'get_active_sessions') {
            // Obtener todas las sesiones activas
            $stmt = $conn->prepare("
                SELECT 
                    s.*,
                    u.Nombre as NombreUsuario,
                    u.Usuario as UsuarioLogin
                FROM t_sesiones_dispositivos s
                LEFT JOIN t_usuarios u ON s.IdUsuario = u.IdUsuario
                WHERE s.Activa = 1
                ORDER BY s.FechaLogin DESC
            ");
            
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true, 
                'sessions' => $sessions,
                'count' => count($sessions)
            ]);
            
        } elseif ($action === 'get_today_sessions') {
            // Obtener sesiones del día actual
            $stmt = $conn->prepare("
                SELECT 
                    s.*,
                    u.Nombre as NombreUsuario
                FROM t_sesiones_dispositivos s
                LEFT JOIN t_usuarios u ON s.IdUsuario = u.IdUsuario
                WHERE CONVERT(DATE, s.FechaLogin) = CONVERT(DATE, GETDATE())
                ORDER BY s.FechaLogin DESC
            ");
            
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true, 
                'sessions' => $sessions,
                'count' => count($sessions)
            ]);
            
        } elseif ($action === 'get_session_stats') {
            // Obtener estadísticas
            $stmt = $conn->prepare("
                SELECT 
                    COUNT(*) as TotalSesiones,
                    SUM(CASE WHEN Activa = 1 THEN 1 ELSE 0 END) as SesionesActivas,
                    COUNT(DISTINCT IdUsuario) as UsuariosUnicos,
                    COUNT(DISTINCT IdUbicacion) as UbicacionesUnicas,
                    COUNT(DISTINCT IdDispositivo) as DispositivosUnicos,
                    CONVERT(DATE, GETDATE()) as Fecha
                FROM t_sesiones_dispositivos
                WHERE CONVERT(DATE, FechaLogin) = CONVERT(DATE, GETDATE())
            ");
            
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true, 
                'stats' => $stats
            ]);
            
        } elseif ($action === 'get_user_sessions') {
            // Obtener sesiones de un usuario específico
            $userId = $_GET['user_id'] ?? '';
            
            if (empty($userId)) {
                echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
                exit;
            }
            
            $stmt = $conn->prepare("
                SELECT *
                FROM t_sesiones_dispositivos
                WHERE IdUsuario = ?
                ORDER BY FechaLogin DESC
                LIMIT 50
            ");
            
            $stmt->execute([$userId]);
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true, 
                'sessions' => $sessions,
                'count' => count($sessions)
            ]);
            
        } else {
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}

// Cerrar conexión
if (isset($conn)) {
    $conn = null;
}
?>