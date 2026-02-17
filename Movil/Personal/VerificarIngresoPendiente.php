<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../api/db/conexion.php';

// Verificar conexión
if (!$Conexion) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPersonal = $_POST['IdPersonal'] ?? '';
    $idUbicacion = $_POST['IdUbicacion'] ?? '';

    // Log para debug
    error_log("IdPersonal recibido: " . $idPersonal);
    error_log("IdUbicacion recibido: " . $idUbicacion);

    if (empty($idPersonal) || empty($idUbicacion)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos requeridos',
            'received' => [
                'IdPersonal' => $idPersonal,
                'IdUbicacion' => $idUbicacion
            ]
        ]);
        exit;
    }

    try {
        // Consulta principal usando PDO
        $query = "SELECT IdMovEnTSal FROM regentsalper 
                  WHERE IdPer = :idPersonal AND IdUbicacion = :idUbicacion 
                  AND StatusRegistro = 1 AND FechaSalida IS NULL";
        
        $stmt = $Conexion->prepare($query);
        $stmt->bindParam(':idPersonal', $idPersonal);
        $stmt->bindParam(':idUbicacion', $idUbicacion);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'IdMovEnTSal' => $row['IdMovEnTSal']
                ]
            ]);
        } else {
            // Verificar si hay registros con esos parámetros (para debug)
            $checkQuery = "SELECT COUNT(*) as total FROM regentsalper 
                          WHERE IdPer = :idPersonal AND IdUbicacion = :idUbicacion AND StatusRegistro = 1";
            $checkStmt = $Conexion->prepare($checkQuery);
            $checkStmt->bindParam(':idPersonal', $idPersonal);
            $checkStmt->bindParam(':idUbicacion', $idUbicacion);
            $checkStmt->execute();
            $total = $checkStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            echo json_encode([
                'success' => false,
                'message' => 'No hay ingreso pendiente',
                'debug' => [
                    'total_registros_con_params' => $total
                ]
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la consulta: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>