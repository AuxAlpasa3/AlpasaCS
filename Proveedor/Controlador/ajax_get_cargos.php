<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
                IdCargo as id, 
                NomCargo as nombre 
            FROM t_cargo 
            ORDER BY NomCargo";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    $cargos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($cargos, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar cargos: ' . $e->getMessage()]);
} finally {
    $Conexion = null;
}
?>