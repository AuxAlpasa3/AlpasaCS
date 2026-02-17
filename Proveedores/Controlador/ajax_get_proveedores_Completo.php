<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

// Obtener filtros
$filtroNombre = $_GET['nombre'] ?? '';
$filtroEmail = $_GET['email'] ?? '';
$filtroTelefono = $_GET['telefono'] ?? '';
$filtroEstatus = $_GET['estatus'] ?? '';

try {
    
    $sql = "SELECT t1.*, 
                   COUNT(t2.IdProveedorPersonal) as TotalPersonal
            FROM t_Proveedor t1
            LEFT JOIN t_Proveedor_personal t2 ON t1.IdProveedor = t2.IdProveedor
            WHERE 1=1";
    
    $params = [];
    
    if ($filtroNombre) {
        $sql .= " AND t1.NombreProveedor LIKE :nombre";
        $params[':nombre'] = '%' . $filtroNombre . '%';
    }
    
    if ($filtroEmail) {
        $sql .= " AND t1.Email LIKE :email";
        $params[':email'] = '%' . $filtroEmail . '%';
    }
    
    if ($filtroTelefono) {
        $sql .= " AND (t1.Telefono LIKE :telefono OR t1.TelefonoContacto LIKE :telefono)";
        $params[':telefono'] = '%' . $filtroTelefono . '%';
    }
    
    if ($filtroEstatus) {
        $sql .= " AND t1.Estatus = :estatus";
        $params[':estatus'] = $filtroEstatus;
    }
    
    $sql .= " GROUP BY t1.IdProveedor
              ORDER BY t1.NombreProveedor";
    
    $stmt = $Conexion->prepare($sql);
    
    // Bind parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($proveedores);
    
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>