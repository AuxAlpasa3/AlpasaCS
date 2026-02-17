<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'id' => 0
];

$required = ['NombreProveedor', 'Email', 'Telefono'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $response['message'] = "El campo $field es requerido";
        echo json_encode($response);
        exit;
    }
}

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
    
    $sqlCheck = "SELECT IdProveedor FROM t_Proveedor WHERE Email = ?";
    $stmtCheck = $Conexion->prepare($sqlCheck);
    $stmtCheck->execute([$email]);
    
    if ($stmtCheck->rowCount() > 0) {
        $response['message'] = "El email ya está registrado para otro proveedor";
        echo json_encode($response);
        exit;
    }
    
    $sql = "INSERT INTO t_Proveedor (
                NombreProveedor,
                RazonSocial,
                Email,
                Telefono,
                Direccion,
                RFC,
                ContactoNombre,
                ContactoTelefono,
                MotivoIngreso,
                Status,
                FechaRegistro
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())";
    
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
        $status
    ]);
    
    $sqlLastId = "SELECT SCOPE_IDENTITY() as IdProveedor";
    $stmtLastId = $Conexion->prepare($sqlLastId);
    $stmtLastId->execute();
    $result = $stmtLastId->fetch(PDO::FETCH_ASSOC);
    $idProveedor = $result['IdProveedor'] ?? 0;
    
    $response['success'] = true;
    $response['message'] = "Proveedor creado exitosamente";
    $response['id'] = $idProveedor;
    
} catch(PDOException $e) {
    $response['message'] = "Error al crear proveedor: " . $e->getMessage();
    error_log("Error Crear_Proveedor: " . $e->getMessage());
}

echo json_encode($response);
?>