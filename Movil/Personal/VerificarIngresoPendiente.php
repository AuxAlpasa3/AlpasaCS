<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPersonal = $_POST['IdPersonal'] ?? '';
    $idUbicacion = $_POST['IdUbicacion'] ?? '';

    if (empty($idPersonal) || empty($idUbicacion) ) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos requeridos'
        ]);
        exit;
    }

    try {
        // Para SQL Server, usamos parámetros con nombres o con ?
        $query = "SELECT IdMovEnTSal FROM regentsalper 
                  WHERE IdPer = ? AND IdUbicacion = ? AND StatusRegistro = 1 AND FechaSalida IS NULL";
        
        $stmt = $Conexion->prepare($query);
        // En PDO, ejecutamos pasando los parámetros directamente
        $stmt->execute([$idPersonal, $idUbicacion]);
        
        // fetch() en lugar de get_result() y fetch_assoc()
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
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
        
        // No es necesario cerrar explícitamente, PDO lo maneja
        $stmt = null;
        
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