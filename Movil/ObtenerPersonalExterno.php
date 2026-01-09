<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../api/db/conexion.php';

$idPersonalExterno = $_POST['IdPersonalExterno'] ?? '';
$idUsuario = $_POST['IdUsuario'] ?? '';

if (empty($idPersonalExterno)) {
    echo json_encode(array(
        'success' => false,
        'message' => 'No se proporcionó el número de personalExterno'
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sentencia = $Conexion->prepare("
        SELECT 
            t1.IdPersonalExterno,
            t1.NumeroIdentificacion, 
            CONCAT(t1.Nombre,' ',t1.ApPaterno,' ',t1.ApMaterno) as NombreCompleto,
            t1.Cargo,
            t1.EmpresaProcedencia,
            t2.NomLargo,
            t1.RutaFoto
        FROM t_personal_Externo as t1 
        INNER JOIN t_ubicacion as t2 ON t1.AreaVisita = t2.IdUbicacion
        INNER JOIN t_personal as t3 ON t1.IdPersonalResponsable = t3.IdPersonal
        WHERE t1.IdPersonalExterno = $IdPersonalExterno
            AND t1.Status = 1 
            AND (t1.VigenciaAcceso >= GETDATE() OR t1.VigenciaAcceso IS NULL)
    ");
    
    $sentencia->execute([$idPersonalExterno]);
    $resultados = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($resultados) > 0) {
        $personalExterno = $resultados[0];
        
        $sentenciaVehiculo = $Conexion->prepare("
            SELECT IdVehiculo, Marca, Modelo, Placas, Anio, Color, RutaFoto 
            FROM t_vehiculos_Externo
            WHERE idPersonalExterno = ? and Activo = 1
        ");
        
        $sentenciaVehiculo->execute([$idPersonalExterno]);
        $vehiculosExterno = $sentenciaVehiculo->fetchAll(PDO::FETCH_OBJ);
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'IdPersonalExterno' => $personalExterno->IdPersonalExterno,
                'NumeroIdentificacion' => $personalExterno->NumeroIdentificacion,
                'NombreCompleto' => $personalExterno->NombreCompleto,
                'Cargo' => $personalExterno->Cargo,
                'EmpresaProcedencia' => $personalExterno->EmpresaProcedencia,
                'NomLargo' => $personalExterno->NomLargo,
                'RutaFoto' => $personalExterno->RutaFoto,
                'vehiculosExterno' => $vehiculosExterno
            )
        ), JSON_UNESCAPED_UNICODE);
        
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'No se encontró el personalExterno con número: ' . $idPersonalExterno
        ), JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'Error en el servidor: ' . $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
}
?>