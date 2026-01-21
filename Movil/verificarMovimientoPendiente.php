<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../api/db/conexion.php';

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

    $requiredFields = ['IdPersonal'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $IdPersonal = $input['IdPersonal'];

    $sqlMovimientoPendiente = "SELECT 
                                    res.IdMovEnTSal,
                                    res.FolMovEnt,
                                    res.FechaEntrada,
                                    res.IdUbicacion as UbicacionAnterior,
                                    u.NombreLargo as NombreUbicacionAnterior,
                                    FORMAT(res.FechaEntrada, 'HH:mm:ss') as HoraEntrada
                                FROM regentsalper res
                                LEFT JOIN t_ubicaciones u ON res.IdUbicacion = u.IdUbicacion
                                WHERE res.IdPer = :IdPersonal 
                                AND res.FolMovSal IS NULL 
                                AND res.StatusRegistro = 1";
    
    $stmtMovimiento = $Conexion->prepare($sqlMovimientoPendiente);
    $stmtMovimiento->bindParam(':IdPersonal', $IdPersonal, PDO::PARAM_STR);
    $stmtMovimiento->execute();
    
    if ($stmtMovimiento->rowCount() > 0) {
        $movimiento = $stmtMovimiento->fetch(PDO::FETCH_ASSOC);
        
        $sqlEntradaInfo = "SELECT 
                                Observaciones,
                                Ubicacion,
                                DispN,
                                TipoVehiculo
                           FROM regentper 
                           WHERE FolMov = :FolMovEnt";
        
        $stmtEntrada = $Conexion->prepare($sqlEntradaInfo);
        $stmtEntrada->bindParam(':FolMovEnt', $movimiento['FolMovEnt'], PDO::PARAM_INT);
        $stmtEntrada->execute();
        
        $entradaInfo = $stmtEntrada->fetch(PDO::FETCH_ASSOC);
        
        $response['success'] = true;
        $response['message'] = 'Existe un movimiento de entrada sin salida registrada';
        $response['data'] = [
            'movimiento_pendiente' => true,
            'IdMovEnTSal' => $movimiento['IdMovEnTSal'],
            'FolMovEnt' => $movimiento['FolMovEnt'],
            'FechaEntrada' => $movimiento['FechaEntrada'],
            'HoraEntrada' => $movimiento['HoraEntrada'],
            'UbicacionAnterior' => $movimiento['UbicacionAnterior'],
            'NombreUbicacionAnterior' => $movimiento['NombreUbicacionAnterior'] ?? 'Ubicación desconocida',
            'DetalleEntrada' => $entradaInfo ?: []
        ];
    } else {
        $response['success'] = true;
        $response['message'] = 'No hay movimientos pendientes';
        $response['data'] = [
            'movimiento_pendiente' => false
        ];
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Error en verificarMovimientoPendiente: ' . $e->getMessage());
    http_response_code(400);
} finally {
    $Conexion = null;
}

echo json_encode($response);
?>