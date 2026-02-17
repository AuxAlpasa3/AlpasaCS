<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT 
                pp.IdProveedorPersonal,
                pp.Nombre,
                pp.ApPaterno,
                pp.ApMaterno,
                pp.RutaFoto,
                p.NombreProveedor,
                CASE pp.Status 
                    WHEN 1 THEN 'activo' 
                    ELSE 'inactivo' 
                END as Estado
            FROM t_Proveedor_Personal pp
            INNER JOIN t_Proveedor p ON pp.IdProveedor = p.IdProveedor
            WHERE pp.Status = 1 AND p.Status = 1
            ORDER BY p.NombreProveedor, pp.Nombre";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($personal);
    
} catch(PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error al obtener el personal: ' . $e->getMessage()
    ]);
}
?>