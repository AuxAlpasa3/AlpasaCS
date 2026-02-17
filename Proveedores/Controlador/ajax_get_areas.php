<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT IdDepartamento, NomDepto 
            FROM Departamentos 
            WHERE Status = 1 
            ORDER BY NomDepto ASC";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($areas);
    
} catch(PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>