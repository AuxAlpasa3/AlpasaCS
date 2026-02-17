<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {

    $sql = "SELECT 
                IdPersonal,
                Nombre,
                ApPaterno,
                ApMaterno
            FROM t_personal 
            WHERE Status = 1 
            ORDER BY Nombre ASC";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear los nombres completos
    foreach ($personal as &$persona) {
        $persona['NombreCompleto'] = trim(
            $persona['Nombre'] . ' ' . 
            $persona['ApPaterno'] . ' ' . 
            $persona['ApMaterno']
        );
    }
    
    echo json_encode([
        'success' => true,
        'data' => $personal
    ]);
    
} catch (PDOException $e) {
    error_log('Error en obtenerPersonal.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener el personal: ' . $e->getMessage()
    ]);
}
?>