<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../api/db/conexion.php';

// Manejar preflight CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

try {
    // Obtener y validar datos
    $idPersonal = trim($_POST['IdPersonal'] ?? '');
    $idUbicacion = trim($_POST['IdUbicacion'] ?? '');

    if (empty($idPersonal) || empty($idUbicacion)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos requeridos'
        ]);
        exit;
    }

    // Validar que sean numéricos (si aplica)
    if (!is_numeric($idPersonal) || !is_numeric($idUbicacion)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Los IDs deben ser valores numéricos'
        ]);
        exit;
    }

    // Consulta preparada con tipos correctos
    $query = "SELECT IdMovEnTSal FROM regentsalper 
              WHERE IdPer = ? AND IdUbicacion = ? 
              AND StatusRegistro = 1 AND FechaSalida IS NULL 
              LIMIT 1";
    
    $stmt = $Conexion->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $Conexion->error);
    }

    // Usar "ss" para strings o "ii" para enteros según tu BD
    $stmt->bind_param("ss", $idPersonal, $idUbicacion);
    
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'data' => [
                'IdMovEnTSal' => $row['IdMovEnTSal']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No hay ingreso pendiente'
        ]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error en VerificarIngresoPendiente: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor'
    ]);
}
?>