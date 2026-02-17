<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

// Obtener datos del POST
$IdProveedor = $_POST['IdProveedor'] ?? 0;
$IdProveedorPersonal = $_POST['IdProveedorPersonal'] ?? 0;
$IdDepartamento = $_POST['IdDepartamento'] ?? 0;
$FechaVisita = $_POST['FechaVisita'] ?? date('Y-m-d');
$HoraVisita = $_POST['HoraVisita'] ?? date('H:i');
$IdVehiculo = $_POST['IdVehiculo'] ?? null;
$Motivo = $_POST['Motivo'] ?? '';

try {
    $db = new Database();
    $Conexion = $db->getConnection();
    
    // Generar código QR único
    $qrCode = 'QR' . date('YmdHis') . rand(1000, 9999);
    
    // Calcular fecha de expiración (8 horas después)
    $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+8 hours'));
    
    // Insertar visita
    $sql = "INSERT INTO visitas_proveedores 
            (IdProveedor, IdProveedorPersonal, IdDepartamento, FechaVisita, HoraVisita, 
             IdVehiculo, Motivo, QrCode, Estatus, FechaRegistro, FechaExpiracion) 
            VALUES 
            (:IdProveedor, :IdProveedorPersonal, :IdDepartamento, :FechaVisita, :HoraVisita, 
             :IdVehiculo, :Motivo, :QrCode, 'pendiente', NOW(), :FechaExpiracion)";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->bindParam(':IdProveedor', $IdProveedor, PDO::PARAM_INT);
    $stmt->bindParam(':IdProveedorPersonal', $IdProveedorPersonal, PDO::PARAM_INT);
    $stmt->bindParam(':IdDepartamento', $IdDepartamento, PDO::PARAM_INT);
    $stmt->bindParam(':FechaVisita', $FechaVisita);
    $stmt->bindParam(':HoraVisita', $HoraVisita);
    $stmt->bindParam(':IdVehiculo', $IdVehiculo, PDO::PARAM_INT);
    $stmt->bindParam(':Motivo', $Motivo);
    $stmt->bindParam(':QrCode', $qrCode);
    $stmt->bindParam(':FechaExpiracion', $fechaExpiracion);
    
    if ($stmt->execute()) {
        $IdVisita = $Conexion->lastInsertId();
        
        // Obtener datos completos para el QR
        $sqlDetalle = "SELECT v.*, p.NombreProveedor, 
                              CONCAT(pp.Nombre, ' ', pp.ApPaterno) as NombrePersonal,
                              a.NombreArea
                       FROM visitas_proveedores v
                       LEFT JOIN proveedores p ON v.IdProveedor = p.IdProveedor
                       LEFT JOIN proveedor_personal pp ON v.IdProveedorPersonal = pp.IdProveedorPersonal
                       LEFT JOIN areas a ON v.IdDepartamento = a.IdDepartamento
                       WHERE v.IdVisita = :IdVisita";
        
        $stmtDetalle = $Conexion->prepare($sqlDetalle);
        $stmtDetalle->bindParam(':IdVisita', $IdVisita, PDO::PARAM_INT);
        $stmtDetalle->execute();
        $visita = $stmtDetalle->fetch(PDO::FETCH_ASSOC);
        
        // Datos para el QR
        $qrData = json_encode([
            'IdVisita' => $IdVisita,
            'Proveedor' => $visita['NombreProveedor'],
            'Personal' => $visita['NombrePersonal'],
            'Area' => $visita['NombreArea'],
            'Fecha' => $visita['FechaVisita'],
            'Hora' => $visita['HoraVisita'],
            'QR' => $qrCode
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Visita registrada exitosamente',
            'data' => [
                'IdVisita' => $IdVisita,
                'Proveedor' => $visita['NombreProveedor'],
                'Personal' => $visita['NombrePersonal'],
                'Area' => $visita['NombreArea'],
                'FechaVisita' => $FechaVisita,
                'HoraVisita' => $HoraVisita,
                'QrData' => $qrData,
                'FechaExpiracion' => date('H:i', strtotime($fechaExpiracion))
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar visita'
        ]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>