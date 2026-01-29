<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

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
        throw new Exception('Método no permitido. Se requiere POST');
    }
    $input = null;
    
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
    } else {
        $input = $_POST;
    }
    
    error_log("Datos recibidos: " . print_r($input, true));
    error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'No definido'));
    error_log("php://input: " . file_get_contents('php://input'));

    if (!$input || !is_array($input)) {
        throw new Exception('Datos no válidos o vacíos');
    }

    if (!isset($input['IdPersonal']) || empty(trim($input['IdPersonal']))) {
        throw new Exception('El campo IdPersonal es requerido y no puede estar vacío');
    }

    $IdPersonal = trim($input['IdPersonal']);

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
                            where t1.IdPer = :IdPersonal 
                            and t1.FolMovSal IS NULL
                            and t1.StatusRegistro = 1";
    
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