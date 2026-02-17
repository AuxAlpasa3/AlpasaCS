<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

if (!isset($_POST['IdProveedor']) || empty($_POST['IdProveedor'])) {
    $response['message'] = 'ID de proveedor no especificado';
    echo json_encode($response);
    exit;
}

$idProveedor = $_POST['IdProveedor'];

try {
    $sqlCheckPersonal = "SELECT COUNT(*) as total 
                        FROM t_Proveedor_Personal 
                        WHERE IdProveedor = ? AND Status = 1";
    $stmtCheck = $Conexion->prepare($sqlCheckPersonal);
    $stmtCheck->execute([$idProveedor]);
    $resultCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    $totalPersonal = $resultCheck['total'] ?? 0;
    
    if ($totalPersonal > 0) {
        $response['message'] = "No se puede eliminar el proveedor porque tiene personal registrado";
        echo json_encode($response);
        exit;
    }
    
    $sqlCheckVehiculos = "SELECT COUNT(*) as total 
                         FROM t_Vehiculos 
                         WHERE TipoVehiculo = 3 
                         AND IdAsociado = ? 
                         AND Activo = 1";
    $stmtCheckVehiculos = $Conexion->prepare($sqlCheckVehiculos);
    $stmtCheckVehiculos->execute([$idProveedor]);
    $resultVehiculos = $stmtCheckVehiculos->fetch(PDO::FETCH_ASSOC);
    $totalVehiculos = $resultVehiculos['total'] ?? 0;
    
    if ($totalVehiculos > 0) {
        $response['message'] = "No se puede eliminar el proveedor porque tiene vehículos registrados";
        echo json_encode($response);
        exit;
    }
    
    $sql = "UPDATE t_Proveedor SET Status = 0 WHERE IdProveedor = ?";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([$idProveedor]);
    
    $rowsAffected = $stmt->rowCount();
    
    if ($rowsAffected > 0) {
        $response['success'] = true;
        $response['message'] = "Proveedor marcado como inactivo";
    } else {
        $response['message'] = "El proveedor no existe o ya fue eliminado";
    }
    
} catch(PDOException $e) {
    $response['message'] = "Error al eliminar proveedor: " . $e->getMessage();
    error_log("Error Eliminar_Proveedor: " . $e->getMessage());
}

echo json_encode($response);
?>