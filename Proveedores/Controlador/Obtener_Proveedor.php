<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'data' => [],
    'message' => ''
];

if (!isset($_GET['IdProveedor']) || empty($_GET['IdProveedor'])) {
    $response['message'] = 'ID de proveedor no especificado';
    echo json_encode($response);
    exit;
}

$idProveedor = $_GET['IdProveedor'];

try {
    $sql = "SELECT 
                IdProveedor,
                NombreProveedor,
                RazonSocial,
                Email,
                Telefono,
                Direccion,
                RFC,
                MotivoIngreso,
                RutaFoto,
                CONVERT(varchar, FechaRegistro, 23) as FechaRegistro,
                CASE Status 
                    WHEN 1 THEN 'activo' 
                    WHEN 0 THEN 'inactivo' 
                    ELSE 'suspendido' 
                END as Estado,
                Status,
                ContactoNombre,
                ContactoTelefono
            FROM t_Proveedor 
            WHERE IdProveedor = ?";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([$idProveedor]);
    $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($proveedor) {
        if (empty($proveedor['RazonSocial'])) {
            $proveedor['RazonSocial'] = $proveedor['NombreProveedor'];
        }
        
        $response['success'] = true;
        $response['data'] = $proveedor;
    } else {
        $response['message'] = 'Proveedor no encontrado';
    }
    
} catch(PDOException $e) {
    $response['message'] = "Error: " . $e->getMessage();
    error_log("Error Obtener_Proveedor: " . $e->getMessage());
}

echo json_encode($response);
?>