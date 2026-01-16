<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

require_once '../api/db/conexion2.php';

$data = json_decode(file_get_contents('php://input'), true);

$IdBoletasEnc = isset($data['IdBoletasEnc']) ? $data['IdBoletasEnc'] : '';
$UUID = isset($data['UUID']) ? $data['UUID'] : '';

if (empty($IdBoletasEnc) && isset($_POST['IdBoletasEnc'])) {
    $IdBoletasEnc = $_POST['IdBoletasEnc'];
    $UUID = $_POST['UUID'] ?? '';
} 

if (empty($IdBoletasEnc) || empty($UUID)) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Faltan datos requeridos: IdBoletasEnc y UUID son obligatorios'
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $Conexion->beginTransaction();
    
    $sentenciaVerificar = $Conexion->prepare("
        SELECT IdBoletasEnc, Estatus, IdBoletasEnc 
        FROM t_boleta_enc 
        WHERE IdBoletasEnc = ? AND Estatus = 0
    ");
    
    $sentenciaVerificar->execute([$IdBoletasEnc]);
    $boleta = $sentenciaVerificar->fetch(PDO::FETCH_OBJ);
    
    if (!$boleta) {
        $Conexion->rollBack();
        echo json_encode(array(
            'success' => false,
            'message' => 'La boleta no existe o ya no está disponible (estatus diferente de 0)'
        ), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $idBoletasEnc = $boleta->IdBoletasEnc;
    
    $sentenciaUpdateDet = $Conexion->prepare("
        UPDATE t_boleta_det 
        SET UUID = ?
        WHERE IdBoletasEnc = ?
    ");
    
    $resultadoUpdateDet = $sentenciaUpdateDet->execute([$UUID, $idBoletasEnc]);
    
    if (!$resultadoUpdateDet) {
        $Conexion->rollBack();
        echo json_encode(array(
            'success' => false,
            'message' => 'Error al actualizar el UUID en los detalles de la boleta'
        ), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $sentenciaUpdateEnc = $Conexion->prepare("
        UPDATE t_boleta_enc 
        SET Estatus = 1 , FechaLlegada = GETDATE(), HoraLlegada = CONVERT(VARCHAR, GETDATE(), 8)
        WHERE IdBoletasEnc = ?
    ");
    
    $resultadoUpdateEnc = $sentenciaUpdateEnc->execute([$IdBoletasEnc]);
    
    if (!$resultadoUpdateEnc) {
        $Conexion->rollBack();
        echo json_encode(array(
            'success' => false,
            'message' => 'Error al actualizar el estatus de la boleta'
        ), JSON_UNESCAPED_UNICODE);
        exit;
    }

    $Conexion->commit();
    
    echo json_encode(array(
        'success' => true,
        'message' => 'Entrada registrada correctamente',
        'data' => array(
            'IdBoletasEnc' => $IdBoletasEnc,
            'UUID' => $UUID,
            'FechaEntrada' => date('Y-m-d H:i:s'),
            'Observaciones' => $Observaciones
        )
    ), JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    if (isset($Conexion) && $Conexion->inTransaction()) {
        $Conexion->rollBack();
    }
    
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}