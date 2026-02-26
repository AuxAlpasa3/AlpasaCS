<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
                IdUbicacion as id, 
                NomLargo as nombre 
            FROM t_ubicacion 
            ORDER BY NomCorto";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    $ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($ubicaciones, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar ubicaciones: ' . $e->getMessage()]);
} finally {
    $Conexion = null;
}
?>