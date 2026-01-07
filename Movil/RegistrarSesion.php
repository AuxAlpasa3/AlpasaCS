<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Device-Id, Device-Name, Device-Location, Device-Location-Id');

require_once '../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del request
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'error' => 'No se recibieron datos'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $action = $input['action'] ?? '';
        $IdUsuario = $input['IdUsuario'] ?? '';
        $IdDispositivo = $input['IdDispositivo'] ?? '';
        
        if (empty($IdUsuario) || empty($action) || empty($IdDispositivo)) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $checkTable = $Conexion->query("SELECT COUNT(*) as existe FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 't_sesiones_dispositivos'");
        $tableExists = $checkTable->fetch(PDO::FETCH_ASSOC);
        
        if ($tableExists['existe'] == 0) {
            echo json_encode(['success' => false, 'error' => 'Tabla de sesiones no existe'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        if ($action === 'login') {
            $NombreDispositivo = $input['NombreDispositivo'] ?? 'Dispositivo móvil';
            $IdUbicacion = $input['IdUbicacion'] ?? '';
            $NombreUbicacion = $input['NombreUbicacion'] ?? 'Ubicación desconocida';
            
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
            
            echo json_encode([
                'success' => true, 
                'message' => 'Sesión de login registrada exitosamente'
            ], JSON_UNESCAPED_UNICODE);
            
        } elseif ($action === 'logout') {
            $stmtUpdate = $Conexion->prepare("
                UPDATE t_sesiones_dispositivos 
                SET FechaLogout = GETDATE(), 
                    Activa = 0 
                WHERE IdUsuario = ? 
                AND IdDispositivo = ? 
                AND Activa = 1
            ");
            
            $stmtUpdate->execute([$IdUsuario, $IdDispositivo]);
            $rowsAffected = $stmtUpdate->rowCount();
            
            echo json_encode([
                'success' => true, 
                'message' => $rowsAffected > 0 ? 'Sesión de logout registrada' : 'No había sesión activa',
                'rows_affected' => $rowsAffected
            ], JSON_UNESCAPED_UNICODE);
            
        } else {
            echo json_encode(['success' => false, 'error' => 'Acción no válida'], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'error' => 'Error en el servidor',
            'details' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
}
?>