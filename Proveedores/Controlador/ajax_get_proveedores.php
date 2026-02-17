<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT 
                IdProveedor,
                NombreProveedor,
                Email,
                Telefono,
                CASE Status 
                    WHEN 1 THEN 'activo' 
                    ELSE 'inactivo' 
                END as Estado
            FROM t_Proveedor 
            WHERE Status = 1 
            ORDER BY NombreProveedor ASC";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($proveedores);
    
} catch(PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error al obtener los proveedores: ' . $e->getMessage()
    ]);
}
?>