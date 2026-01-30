<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../api/db/conexion.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input !== null && json_last_error() === JSON_ERROR_NONE) {
        $ubicacion = $input['Ubicacion'] ?? '';
    } else {
        $ubicacion = $_POST['Ubicacion'] ?? '';
    }
}


try {
    $sentencia = $Conexion->prepare("
        SELECT 
            IdVehiculo,
            Marca,
            Modelo,
            Placas,
            Color,
            Anio,
            RutaFoto,
            LibreUso
        FROM t_vehiculos 
        WHERE Activo = 1 
        AND LibreUso = 1
        ORDER BY Marca, Modelo
    ");
    
    $sentencia->execute();
    $vehiculos = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    $responseData = array();
    
    foreach ($vehiculos as $vehiculo) {
        $responseData[] = array(
            'IdVehiculo' => (int)$vehiculo->IdVehiculo,
            'Marca' => $vehiculo->Marca,
            'Modelo' => $vehiculo->Modelo,
            'Placas' => $vehiculo->Placas,
            'Color' => $vehiculo->Color,
            'Anio' => $vehiculo->Anio,
            'RutaFoto' => $vehiculo->RutaFoto,
            'LibreUso' => (int)$vehiculo->LibreUso,
            'Disponible' => true
        );
    }
    
    echo json_encode(array(
        'success' => true,
        'message' => 'Vehículos de libre uso obtenidos correctamente',
        'data' => $responseData
    ), JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
?>