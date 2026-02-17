<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'data' => [],
    'total' => 0,
    'pages' => 0,
    'message' => ''
];

try {
    $busqueda = $_GET['busqueda'] ?? '';
    $estado = $_GET['estado'] ?? '';
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;
    
    $sql = "SELECT 
                p.IdProveedor,
                p.NombreProveedor,
                p.RazonSocial,
                p.Email,
                p.Telefono,
                p.Direccion,
                p.RFC,
                p.MotivoIngreso,
                p.RutaFoto,
                p.FechaRegistro,
                p.ContactoNombre,
                p.ContactoTelefono,
                CASE p.Status 
                    WHEN 1 THEN 'activo' 
                    WHEN 0 THEN 'inactivo' 
                    ELSE 'suspendido' 
                END as Estado,
                p.Status
            FROM t_Proveedor p 
            WHERE 1=1";
    
    $sqlCount = "SELECT COUNT(*) as total FROM t_Proveedor p WHERE 1=1";
    $params = [];
    $paramsCount = [];
    
    if (!empty($busqueda)) {
        $sql .= " AND (p.NombreProveedor LIKE ? OR p.Email LIKE ? OR p.Telefono LIKE ? 
                OR p.RazonSocial LIKE ? OR p.ContactoNombre LIKE ?)";
        $sqlCount .= " AND (p.NombreProveedor LIKE ? OR p.Email LIKE ? OR p.Telefono LIKE ? 
                      OR p.RazonSocial LIKE ? OR p.ContactoNombre LIKE ?)";
        $searchTerm = "%$busqueda%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        array_push($paramsCount, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }
    
    if (!empty($estado)) {
        if ($estado === 'activo') {
            $sql .= " AND p.Status = 1";
            $sqlCount .= " AND p.Status = 1";
        } elseif ($estado === 'inactivo') {
            $sql .= " AND p.Status = 0";
            $sqlCount .= " AND p.Status = 0";
        } elseif ($estado === 'suspendido') {
            $sql .= " AND p.Status = 2";
            $sqlCount .= " AND p.Status = 2";
        }
    }
    
    $stmtCount = $Conexion->prepare($sqlCount);
    $stmtCount->execute($paramsCount);
    $totalResult = $stmtCount->fetch(PDO::FETCH_ASSOC);
    $total = $totalResult['total'] ?? 0;
    
    $sql .= " ORDER BY p.FechaRegistro DESC, p.NombreProveedor ASC 
              OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
    $params[] = $offset;
    $params[] = $limit;
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute($params);
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($proveedores as &$proveedor) {
        $sqlPersonal = "SELECT COUNT(*) as total 
                       FROM t_Proveedor_Personal 
                       WHERE IdProveedor = ? AND Status = 1";
        $stmtPersonal = $Conexion->prepare($sqlPersonal);
        $stmtPersonal->execute([$proveedor['IdProveedor']]);
        $personalResult = $stmtPersonal->fetch(PDO::FETCH_ASSOC);
        $proveedor['total_personal'] = $personalResult['total'] ?? 0;
        
        $sqlVehiculos = "SELECT COUNT(*) as total 
                        FROM t_Vehiculos 
                        WHERE TipoVehiculo = 3 
                        AND IdAsociado = ? 
                        AND Activo = 1";
        $stmtVehiculos = $Conexion->prepare($sqlVehiculos);
        $stmtVehiculos->execute([$proveedor['IdProveedor']]);
        $vehiculosResult = $stmtVehiculos->fetch(PDO::FETCH_ASSOC);
        $proveedor['total_vehiculos'] = $vehiculosResult['total'] ?? 0;
        
        if (empty($proveedor['RazonSocial'])) {
            $proveedor['RazonSocial'] = $proveedor['NombreProveedor'];
        }
    }
    
    $response['success'] = true;
    $response['data'] = $proveedores;
    $response['total'] = $total;
    $response['pages'] = ceil($total / $limit);
    
} catch(PDOException $e) {
    $response['message'] = "Error en la consulta: " . $e->getMessage();
    error_log("Error Obtener_Proveedores: " . $e->getMessage());
}

echo json_encode($response);
?>