<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

// Obtener datos del POST
$IdProveedor = $_POST['IdProveedor'] ?? 0;
$NombreProveedor = $_POST['NombreProveedor'] ?? '';
$RFC = $_POST['RFC'] ?? '';
$Email = $_POST['Email'] ?? '';
$Telefono = $_POST['Telefono'] ?? '';
$NombreContacto = $_POST['NombreContacto'] ?? '';
$TelefonoContacto = $_POST['TelefonoContacto'] ?? '';
$Direccion = $_POST['Direccion'] ?? '';
$Notas = $_POST['Notas'] ?? '';
$Estatus = $_POST['Estatus'] ?? 'activo';

try {
    $db = new Database();
    $Conexion = $db->getConnection();
    
    if ($IdProveedor > 0) {
        // Actualizar proveedor existente
        $sql = "UPDATE proveedores SET 
                NombreProveedor = :NombreProveedor,
                RFC = :RFC,
                Email = :Email,
                Telefono = :Telefono,
                NombreContacto = :NombreContacto,
                TelefonoContacto = :TelefonoContacto,
                Direccion = :Direccion,
                Notas = :Notas,
                Estatus = :Estatus,
                FechaActualizacion = NOW()
                WHERE IdProveedor = :IdProveedor";
        
        $stmt = $Conexion->prepare($sql);
        $stmt->bindParam(':IdProveedor', $IdProveedor, PDO::PARAM_INT);
    } else {
        // Insertar nuevo proveedor
        $sql = "INSERT INTO proveedores 
                (NombreProveedor, RFC, Email, Telefono, NombreContacto, 
                 TelefonoContacto, Direccion, Notas, Estatus, FechaRegistro) 
                VALUES 
                (:NombreProveedor, :RFC, :Email, :Telefono, :NombreContacto, 
                 :TelefonoContacto, :Direccion, :Notas, :Estatus, NOW())";
        
        $stmt = $Conexion->prepare($sql);
    }
    
    $stmt->bindParam(':NombreProveedor', $NombreProveedor);
    $stmt->bindParam(':RFC', $RFC);
    $stmt->bindParam(':Email', $Email);
    $stmt->bindParam(':Telefono', $Telefono);
    $stmt->bindParam(':NombreContacto', $NombreContacto);
    $stmt->bindParam(':TelefonoContacto', $TelefonoContacto);
    $stmt->bindParam(':Direccion', $Direccion);
    $stmt->bindParam(':Notas', $Notas);
    $stmt->bindParam(':Estatus', $Estatus);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => $IdProveedor > 0 ? 
                'Proveedor actualizado exitosamente' : 
                'Proveedor registrado exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar proveedor'
        ]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>