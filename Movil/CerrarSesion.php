<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

Include '../api/db/conexion.php';

$idUsuario = $_GET['idUsuario'] ?? '';
$deviceId = $_GET['deviceId'] ?? '';

if (empty($idUsuario) || empty($deviceId)) {
    echo json_encode(array('success' => false, 'message' => 'Parámetros incompletos'), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $cerrarSesion = $conexion->prepare("
        UPDATE t_sesiones_dispositivos 
        SET FechaLogout = GETDATE(), 
            Activa = 0 
        WHERE IdUsuario = ? 
        AND IdDispositivo = ? 
        AND Activa = 1");
    
    $cerrarSesion->execute([$idUsuario, $deviceId]);
    
    if ($cerrarSesion->rowCount() > 0) {
        $respuesta = array(
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        );
    } else {
        $respuesta = array(
            'success' => false,
            'message' => 'No se encontró sesión activa'
        );
    }
    
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Error al cerrar sesión: ' . $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
?>