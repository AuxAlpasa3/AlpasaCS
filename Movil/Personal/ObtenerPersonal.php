<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../api/db/conexion.php';

$noEmpleado = $_POST['NoEmpleado'] ?? '';
$idUsuario = $_POST['IdUsuario'] ?? '';

if (empty($noEmpleado)) {
    echo json_encode(array(
        'success' => false,
        'message' => 'No se proporcionó el número de empleado'
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sentencia = $Conexion->prepare("
        SELECT 
            t1.IdPersonal,
            t1.NoEmpleado, 
            CONCAT(t1.Nombre,' ',t1.ApPaterno,' ',t1.ApMaterno) as NombreCompleto,
            t2.NomCargo, 
            t3.NomDepto, 
            t4.NomEmpresa, 
            t5.NomLargo, 
            t1.RutaFoto
        FROM t_personal as t1 
        INNER JOIN t_cargo as t2 ON t1.Cargo = t2.IdCargo
        INNER JOIN t_departamento as t3 ON t1.Departamento = t3.IdDepartamento
        INNER JOIN t_empresa as t4 ON t1.Empresa = t4.IdEmpresa
        INNER JOIN t_ubicacion as t5 ON t1.IdUbicacion = t5.IdUbicacion
        WHERE t1.NoEmpleado = ? AND t1.Status = 1
    ");
    
    $sentencia->execute([$noEmpleado]);
    $resultados = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($resultados) > 0) {
        $personal = $resultados[0];
        
        $sentenciaVehiculo = $Conexion->prepare("
            SELECT IdVehiculo, Marca, Modelo, Num_Serie, Placas, Anio, Color, RutaFoto 
            FROM t_vehiculos 
            WHERE IdAsociado = ? and Activo = 1
        ");
        
        $sentenciaVehiculo->execute([$noEmpleado]);
        $vehiculos = $sentenciaVehiculo->fetchAll(PDO::FETCH_OBJ);
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'IdPersonal' => $personal->IdPersonal,
                'NoEmpleado' => $personal->NoEmpleado,
                'NombreCompleto' => $personal->NombreCompleto,
                'NomCargo' => $personal->NomCargo,
                'NomDepto' => $personal->NomDepto,
                'NomEmpresa' => $personal->NomEmpresa,
                'NomLargo' => $personal->NomLargo,
                'RutaFoto' => $personal->RutaFoto,
                'Vehiculos' => $vehiculos
            )
        ), JSON_UNESCAPED_UNICODE);
        
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'No se encontró el empleado con número: ' . $noEmpleado
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