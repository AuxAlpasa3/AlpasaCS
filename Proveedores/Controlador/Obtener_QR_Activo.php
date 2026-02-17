<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $Conexion = $db->getConnection();
    
    $sql = "SELECT v.*, p.NombreProveedor, 
                   CONCAT(pp.Nombre, ' ', pp.ApPaterno) as NombrePersonal,
                   a.NombreArea
            FROM visitas_proveedores v
            LEFT JOIN proveedores p ON v.IdProveedor = p.IdProveedor
            LEFT JOIN proveedor_personal pp ON v.IdProveedorPersonal = pp.IdProveedorPersonal
            LEFT JOIN areas a ON v.IdDepartamento = a.IdDepartamento
            WHERE v.Estatus = 'activo' 
            AND DATE(v.FechaVisita) = CURDATE()
            ORDER BY v.FechaRegistro DESC
            LIMIT 1";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    
    $visita = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($visita) {
        $qrData = json_encode([
            'IdVisita' => $visita['IdVisita'],
            'Proveedor' => $visita['NombreProveedor'],
            'Personal' => $visita['NombrePersonal'],
            'Area' => $visita['NombreArea'],
            'Fecha' => $visita['FechaVisita'],
            'Hora' => $visita['HoraVisita'],
            'QR' => $visita['QrCode']
        ]);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'IdVisita' => $visita['IdVisita'],
                'Proveedor' => $visita['NombreProveedor'],
                'Personal' => $visita['NombrePersonal'],
                'Area' => $visita['NombreArea'],
                'FechaVisita' => $visita['FechaVisita'],
                'HoraVisita' => $visita['HoraVisita'],
                'QrData' => $qrData,
                'FechaExpiracion' => date('H:i', strtotime($visita['FechaExpiracion']))
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No hay visitas activas'
        ]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>