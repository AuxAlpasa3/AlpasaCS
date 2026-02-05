<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../api/db/conexion.php';

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Datos no válidos');
    }

    $IdProveedor = $input['IdProveedor'] ?? 0;

    $sql = "SELECT 
                t1.IdVehiculo,
                t1.Marca,
                t1.Modelo,
                t1.Placas,
                t1.Color,
                t1.LibreUso,
                t1.IdAsociado,
                t1.RutaFoto,
                t2.NombreProveedor as NombreCompleto
            FROM t_vehiculos as t1
            LEFT JOIN t_Proveedor as t2 ON t1.IdAsociado = t2.IdProveedor
            WHERE t1.Activo = 1 and t1.TipoVehiculo=3";

    $params = [];
    
    
    if ($IdProveedor !== '' && $IdProveedor !== null) {
        $sql .= " AND t1.IdAsociado = :IdProveedor";
        $params[':IdProveedor'] = $IdProveedor;
    }

    
    $sql .= " ORDER BY t1.Placas ASC";
    
    $stmt = $Conexion->prepare($sql);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['message'] = 'Vehículos obtenidos correctamente';
    $response['data'] = $vehiculos;

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Error en obtenerVehiculosPorTipo: ' . $e->getMessage());
    http_response_code(400);
} finally {
    $Conexion = null;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>