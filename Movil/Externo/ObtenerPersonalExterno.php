<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../../api/db/conexion.php';

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
    $sentencia = $Conexion->prepare("SELECT 
            t1.IdPersonalExterno,
            t1.NumeroIdentificacion, 
            CONCAT(t1.Nombre,' ',t1.ApPaterno,' ',t1.ApMaterno) as NombreCompleto,
            t4.NomCargo as Cargo,
            t1.EmpresaProcedencia,
            (CASE WHEN t1.AreaVisita=NULL THEN 'TODAS' WHEN t1.AreaVisita=0 THEN 'TODAS' ELSE t2.NomLargo END) as AreaVisitaNombre,
            t1.RutaFoto
        FROM t_personal_Externo as t1 
        LEFT JOIN t_ubicacion as t2 ON t1.AreaVisita = t2.IdUbicacion
        INNER JOIN t_personal as t3 ON t1.IdPersonalResponsable = t3.IdPersonal
        INNER JOIN t_cargoExterno as t4 on t1.Cargo=t4.IdCargo
        WHERE t1.IdPersonalExterno = ?
            AND t1.Status = 1 
            AND (t1.VigenciaAcceso >= GETDATE() OR t1.VigenciaAcceso IS NULL)");
    
    $sentencia->execute([$idPersonalExterno]);
    $resultados = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($resultados) > 0) {
        $personalExterno = $resultados[0];
        
        $sentenciaVehiculo = $Conexion->prepare("
            SELECT IdVehiculoExterno, Marca, Modelo, Placas, Anio, Color, RutaFoto 
            FROM t_Vehiculos
            WHERE IdAsociado = ? and Activo = 1
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
                'AreaVisitaNombre' => $personalExterno->AreaVisitaNombre,
                'RutaFoto' => $personalExterno->RutaFoto,
                'vehiculosExterno' => $vehiculosExterno
            )
        ), JSON_UNESCAPED_UNICODE);
        
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'No se encontró el personal Externo con número: ' . $idPersonalExterno
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