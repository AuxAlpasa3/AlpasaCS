<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../api/db/conexion.php';

$idProveedor = $_POST['IdProveedor'] ?? '';
$idUsuario = $_POST['IdUsuario'] ?? '';

if (empty($idProveedor)) {
    echo json_encode(array(
        'success' => false,
        'message' => 'No se proporcionó el ID del proveedor'
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sentencia = $Conexion->prepare("
        SELECT 
            IdProveedor,
            NombreProveedor,
            MotivoIngreso,
            Status,
            Vigencia,
            RutaFoto
        FROM t_Proveedor 
        WHERE IdProveedor = ?
        AND Status = 1 
        AND (Vigencia >= GETDATE() OR Vigencia IS NULL)
    ");
    
    $sentencia->execute([$idProveedor]);
    $resultados = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($resultados) > 0) {
        $proveedor = $resultados[0];
        
        $sentenciaPersonal = $Conexion->prepare("
            SELECT 
                IdPersonal,
                IdProveedor,
                CONCAT(Nombre,' ',ApPaterno,' ',ApMaterno) AS NombreCompleto,
                RutaFoto,
                Status
            FROM t_Proveedor_personal
            WHERE IdProveedor = ?
            AND Status = 1
            ORDER BY Nombre, ApPaterno, ApMaterno
        ");
        
        $sentenciaPersonal->execute([$idProveedor]);
        $personal = $sentenciaPersonal->fetchAll(PDO::FETCH_OBJ);
        
        $sentenciaVehiculo = $Conexion->prepare("
            SELECT 
                IdVehiculo,
                IdProveedor,
                Marca,
                Modelo,
                Num_Serie,
                Placas,
                Anio,
                Color,
                RutaFoto,
                Activo
            FROM t_Proveedor_vehiculo
            WHERE IdProveedor = ?
            AND Activo = 1
            ORDER BY Marca, Modelo, Placas
        ");
        
        $sentenciaVehiculo->execute([$idProveedor]);
        $vehiculos = $sentenciaVehiculo->fetchAll(PDO::FETCH_OBJ);
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'IdProveedor' => $proveedor->IdProveedor,
                'NombreProveedor' => $proveedor->NombreProveedor,
                'MotivoIngreso' => $proveedor->MotivoIngreso,
                'Status' => $proveedor->Status,
                'RutaFoto' => $proveedor->RutaFoto,
                'Vigencia' => $proveedor->Vigencia,
                'Personal' => $personal,
                'Vehiculos' => $vehiculos
            )
        ), JSON_UNESCAPED_UNICODE);
        
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'No se encontró el proveedor con ID: ' . $idProveedor . ' o su acceso no está vigente'
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