<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
                IdEmpresa as id, 
                NomEmpresa as nombre 
            FROM t_empresa 
            ORDER BY NomEmpresa";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($empresas, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar empresas: ' . $e->getMessage()]);
} finally {
    $Conexion = null;
}
?>