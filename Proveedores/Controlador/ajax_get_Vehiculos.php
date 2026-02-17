<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT 
                IdVehiculo,
                Marca,
                Modelo,
                Placas,
                Color,
                Anio,
                Num_Serie,
                CASE Activo 
                    WHEN 1 THEN 'activo' 
                    ELSE 'inactivo' 
                END as Estado
            FROM t_Vehiculos 
            WHERE TipoVehiculo = 3 
            AND Activo = 1 
            ORDER BY Marca, Modelo";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($vehiculos);
    
} catch(PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error al obtener los vehículos: ' . $e->getMessage()
    ]);
}
?>