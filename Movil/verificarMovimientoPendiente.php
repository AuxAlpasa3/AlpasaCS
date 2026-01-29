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
    // if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //     throw new Exception('Método no permitido');
    // }
    
    if (!$input || !is_array($input)) {
        throw new Exception('Datos JSON no válidos');
    }

    if (!isset($input['IdPersonal']) || trim($input['IdPersonal']) === '') {
        throw new Exception('El campo IdPersonal es requerido');
    }

    $IdPersonal = trim($input['IdPersonal']);

    if (!$Conexion) {
        throw new Exception('Error de conexión a la base de datos');
    }

    $sqlMovimientoPendiente = "SELECT
                            t1.IdMovEnTSal,
                            t1.FolMovEnt,
                            t1.FechaEntrada,
                            t1.IdUbicacion as UbicacionAnterior,
                            t2.NomLargo as NombreUbicacionAnterior,
                            CONVERT(VARCHAR(8), t1.FechaEntrada, 108) as HoraEntrada
                            FROM
                            regentsalper as t1
                            LEFT JOIN t_ubicacion_interna as t2
                            ON t1.IdUbicacion = t2.IdUbicacion
                            WHERE t1.IdPer = :IdPersonal 
                            AND t1.FolMovSal IS NULL
                            AND t1.StatusRegistro = 1";
    
    $stmtMovimiento = $Conexion->prepare($sqlMovimientoPendiente);
    $stmtMovimiento->bindParam(':IdPersonal', $IdPersonal, PDO::PARAM_STR);
    
    if (!$stmtMovimiento->execute()) {
        throw new Exception('Error al ejecutar consulta de movimiento pendiente');
    }
    
    if ($stmtMovimiento->rowCount() > 0) {
        $movimiento = $stmtMovimiento->fetch(PDO::FETCH_ASSOC);
        
        if (!isset($movimiento['FolMovEnt']) || empty($movimiento['FolMovEnt'])) {
            throw new Exception('FolMovEnt no encontrado en el registro');
        }
        
        $sqlEntradaInfo = "SELECT 
                                Observaciones,
                                Ubicacion,
                                DispN,
                                TipoVehiculo
                           FROM regentper 
                           WHERE FolMov = :FolMovEnt";
        
        $stmtEntrada = $Conexion->prepare($sqlEntradaInfo);
        $stmtEntrada->bindParam(':FolMovEnt', $movimiento['FolMovEnt'], PDO::PARAM_INT);
        
        if (!$stmtEntrada->execute()) {
            throw new Exception('Error al obtener detalle de entrada');
        }
        
        $entradaInfo = $stmtEntrada->fetch(PDO::FETCH_ASSOC);
        
        $response['success'] = true;
        $response['message'] = 'Existe un movimiento de entrada sin salida registrada';
        $response['data'] = [
            'movimiento_pendiente' => true,
            'IdMovEnTSal' => (int)$movimiento['IdMovEnTSal'],
            'FolMovEnt' => (int)$movimiento['FolMovEnt'],
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

} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = 'Error de base de datos: ' . $e->getMessage();
    error_log('Error PDO en verificarMovimientoPendiente: ' . $e->getMessage());
    http_response_code(500);
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Error en verificarMovimientoPendiente: ' . $e->getMessage());
    http_response_code(400);
} finally {
    if (isset($Conexion)) {
        $Conexion = null;
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>