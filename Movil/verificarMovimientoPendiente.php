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
                            t1.IdMovEnTSal,
                            t1.FolMovEnt,
                            t1.FechaEntrada,
                            t1.IdUbicacion as UbicacionAnterior,
                            t2.NomLargo as NombreUbicacionAnterior,
                            FORMAT(t1.FechaEntrada, 'HH:mm:ss') as HoraEntrada
                            FROM
                            regentsalper as t1
                            LEFT JOIN t_ubicacion_interna as t2
                            on t1.IdUbicacion=t2.IdUbicacion
                            where t1.IdPer =:IdPersonal 
                            and t1.FolMovSal IS NULL
                            and t1.StatusRegistro= 1 ";
    
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
                           WHERE FolMov = 2";
        
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