<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT IdDepartamento as id, 
                 NomDepto as nombre 
            FROM t_departamento 
            ORDER BY NomDepto";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($departamentos, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar departamentos: ' . $e->getMessage()]);
} finally {
    $Conexion = null;
}
?>