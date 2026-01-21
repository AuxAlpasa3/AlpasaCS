<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
                t1.IdCargo as id, 
                t1.NomCargo as nombre 
            FROM t_cargoExterno as t1 inner join t_personal_externo as t2 
                ON t1.IdCargo = t2.Cargo
            ORDER BY t1.NomCargo";
    
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