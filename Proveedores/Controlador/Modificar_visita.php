<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $IdVisita = $_GET['IdVisita'] ?? '';
    
    if (empty($IdVisita)) {
        $response['message'] = 'ID de visita requerido';
        echo json_encode($response);
        exit;
    }
    
    try {
        $sql = "SELECT 
                    v.*,
                    p.NombreProveedor,
                    pp.Nombre,
                    pp.ApPaterno,
                    pp.ApMaterno,
                    a.NombreArea
                FROM Visitas v
                JOIN t_proveedores p ON v.IdProveedor = p.IdProveedor
                JOIN t_proveedor_personal pp ON v.IdProveedorPersonal = pp.IdProveedorPersonal
                JOIN Areas a ON v.IdDepartamento = a.IdDepartamento
                WHERE v.IdVisita = ?";
        
        $stmt = $Conexion->prepare($sql);
        $stmt->execute([$IdVisita]);
        $visita = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($visita) {
            $response['success'] = true;
            $response['data'] = $visita;
        } else {
            $response['message'] = 'Visita no encontrada';
        }
        
    } catch (PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar visita
    $IdVisita = $_POST['IdVisita'] ?? '';
    $IdProveedor = $_POST['IdProveedor'] ?? '';
    $IdProveedorPersonal = $_POST['IdProveedorPersonal'] ?? '';
    $IdDepartamento = $_POST['IdDepartamento'] ?? '';
    $FechaVisita = $_POST['FechaVisita'] ?? '';
    $HoraVisita = $_POST['HoraVisita'] ?? '';
    $Motivo = $_POST['Motivo'] ?? '';
    $IdVehiculo = $_POST['IdVehiculo'] ?? null;
    $Estatus = $_POST['Estatus'] ?? '';
    
    if (empty($IdVisita) || empty($IdProveedor) || empty($IdProveedorPersonal) || 
        empty($IdDepartamento) || empty($FechaVisita) || empty($HoraVisita) || empty($Motivo)) {
        $response['message'] = 'Todos los campos obligatorios son requeridos';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Determinar si hay vehículo
        $Vehiculo = $IdVehiculo ? 1 : 0;
        $Placas = '';
        
        if ($IdVehiculo) {
            // Obtener placas del vehículo
            $stmtVeh = $Conexion->prepare("SELECT Placas FROM t_vehiculos WHERE IdVehiculo = ? AND TipoVehiculo = 3");
            $stmtVeh->execute([$IdVehiculo]);
            $vehiculoData = $stmtVeh->fetch(PDO::FETCH_ASSOC);
            $Placas = $vehiculoData['Placas'] ?? '';
        }
        
        // Actualizar visita
        $sql = "UPDATE Visitas SET 
                    IdProveedor = ?,
                    IdProveedorPersonal = ?,
                    IdDepartamento = ?,
                    FechaVisita = ?,
                    HoraVisita = ?,
                    Motivo = ?,
                    Vehiculo = ?,
                    IdVehiculo = ?,
                    Placas = ?,
                    Estatus = ?,
                    FechaActualizacion = GETDATE()
                WHERE IdVisita = ?";
        
        $stmt = $Conexion->prepare($sql);
        $stmt->execute([
            $IdProveedor, $IdProveedorPersonal, $IdDepartamento, $FechaVisita, $HoraVisita,
            $Motivo, $Vehiculo, $IdVehiculo, $Placas, $Estatus, $IdVisita
        ]);
        
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Visita actualizada correctamente';
            
            // Si el estatus cambió a activo, recalcular fecha de expiración
            if ($Estatus === 'activo') {
                $fechaExpiracion = date('Y-m-d H:i:s', strtotime("$FechaVisita $HoraVisita +8 hours"));
                $updateExp = $Conexion->prepare("UPDATE Visitas SET FechaExpiracion = ? WHERE IdVisita = ?");
                $updateExp->execute([$fechaExpiracion, $IdVisita]);
            }
        } else {
            $response['message'] = 'No se realizaron cambios';
        }
        
    } catch (PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);
$Conexion = null;
?>