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
    $nombreProveedor = trim($_POST['NombreProveedor']);
    $razonSocial = trim($_POST['RazonSocial'] ?? $nombreProveedor);
    $email = trim($_POST['Email']);
    $telefono = trim($_POST['Telefono']);
    $direccion = trim($_POST['Direccion'] ?? '');
    $rfc = trim($_POST['RFC'] ?? '');
    $contactoNombre = trim($_POST['ContactoNombre'] ?? '');
    $contactoTelefono = trim($_POST['ContactoTelefono'] ?? '');
    $motivoIngreso = trim($_POST['MotivoIngreso'] ?? '');
    
    $estado = $_POST['Estado'] ?? 'activo';
    $status = ($estado === 'activo') ? 1 : 0;
    
    $sqlCheck = "SELECT IdProveedor FROM t_Proveedor WHERE Email = ? AND IdProveedor != ?";
    $stmtCheck = $Conexion->prepare($sqlCheck);
    $stmtCheck->execute([$email, $idProveedor]);
    
    if ($stmtCheck->rowCount() > 0) {
        $response['message'] = "El email ya está registrado para otro proveedor";
        echo json_encode($response);
        exit;
    }
    
    $sql = "UPDATE t_Proveedor SET
                NombreProveedor = ?,
                RazonSocial = ?,
                Email = ?,
                Telefono = ?,
                Direccion = ?,
                RFC = ?,
                ContactoNombre = ?,
                ContactoTelefono = ?,
                MotivoIngreso = ?,
                Status = ?
            WHERE IdProveedor = ?";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([
        $nombreProveedor,
        $razonSocial,
        $email,
        $telefono,
        $direccion,
        $rfc,
        $contactoNombre,
        $contactoTelefono,
        $motivoIngreso,
        $status,
        $idProveedor
    ]);
    
    $rowsAffected = $stmt->rowCount();
    
    if ($rowsAffected > 0) {
        $response['success'] = true;
        $response['message'] = "Proveedor actualizado exitosamente";
    } else {
        $response['message'] = "No se realizaron cambios o el proveedor no existe";
    }
    
} catch(PDOException $e) {
    $response['message'] = "Error al actualizar proveedor: " . $e->getMessage();
    error_log("Error Actualizar_Proveedor: " . $e->getMessage());
}

echo json_encode($response);
?>