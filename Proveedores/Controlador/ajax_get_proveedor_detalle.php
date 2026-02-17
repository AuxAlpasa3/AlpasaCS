<?php

include '../../api/db/conexion.php';

header('Content-Type: application/json');

$IdProveedor = $_GET['IdProveedor'] ?? 0;

try {
    
    $sql = "SELECT * FROM proveedores WHERE IdProveedor = :IdProveedor";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->bindParam(':IdProveedor', $IdProveedor, PDO::PARAM_INT);
    $stmt->execute();
    
    $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($proveedor) {
        echo json_encode($proveedor);
    } else {
        echo json_encode(['error' => 'Proveedor no encontrado']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>