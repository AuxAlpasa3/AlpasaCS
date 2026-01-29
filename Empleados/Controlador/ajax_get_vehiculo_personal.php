<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

try {
    $noEmpleado = $_GET['NoEmpleado'] ?? '';
    
    if (!$noEmpleado) {
        echo json_encode([
            'success' => false,
            'message' => 'No. Empleado no proporcionado'
        ]);
        exit;
    }
    
    $sql = "SELECT 
                IdVehiculo,
                NoEmpleado,
                Marca,
                Modelo,
                Num_Serie,
                Placas,
                Anio,
                Color,
                Activo,
                RutaFoto
            FROM t_vehiculos 
            WHERE NoEmpleado = ? AND Activo = 1";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute([$noEmpleado]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($vehiculo) {
        echo json_encode([
            'success' => true,
            'vehiculo' => $vehiculo
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'vehiculo' => null,
            'message' => 'No hay vehículo asignado'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>