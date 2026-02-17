<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

if (!isset($_GET['IdProveedor']) || empty($_GET['IdProveedor'])) {
    echo json_encode([]);
    exit;
}

$idProveedor = $_GET['IdProveedor'];

try {
    $sql = "SELECT 
                IdProveedorPersonal,
                Nombre,
                ApPaterno,
                ApMaterno,
                RutaFoto,
                CASE Status 
                    WHEN 1 THEN 'activo' 
                    ELSE 'inactivo' 
                END as Estado
            FROM t_Proveedor_Personal 
            WHERE IdProveedor = ? AND Status = 1 
            ORDER BY Nombre, ApPaterno";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([$idProveedor]);
    
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($personal);
    
} catch(PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error al obtener el personal: ' . $e->getMessage()
    ]);
}
?>