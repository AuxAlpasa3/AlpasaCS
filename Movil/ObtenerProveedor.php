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
    // 1. Obtener información principal del proveedor
    $sentencia = $Conexion->prepare("
        SELECT 
            IdProveedor,
            NombreProveedor,
            MotivoIngreso,
            Status,
            Vigencia,
            FechaRegistro
        FROM t_Proveedor 
        WHERE IdProveedor = ?
        AND Status = 1 
        AND (Vigencia >= GETDATE() OR Vigencia IS NULL)
    ");
    
    $sentencia->execute([$idProveedor]);
    $resultados = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($resultados) > 0) {
        $proveedor = $resultados[0];
        
        // 2. Obtener personal del proveedor
        $sentenciaPersonal = $Conexion->prepare("
            SELECT 
                IdPersonal,
                IdProveedor,
                CONCAT(Nombre,' ',ApPaterno,' ',ApMaterno) AS NombreCompleto,
                RutaFoto,
                Status,
                FechaRegistro
            FROM t_Proveedor_personal
            WHERE IdProveedor = ?
            AND Status = 1
            ORDER BY Nombre, ApPaterno, ApMaterno
        ");
        
        $sentenciaPersonal->execute([$idProveedor]);
        $personal = $sentenciaPersonal->fetchAll(PDO::FETCH_OBJ);
        
        // 3. Obtener vehículos del proveedor
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
                Activo,
                FechaRegistro
            FROM t_Proveedor_vehiculo
            WHERE IdProveedor = ?
            AND Activo = 1
            ORDER BY Marca, Modelo, Placas
        ");
        
        $sentenciaVehiculo->execute([$idProveedor]);
        $vehiculos = $sentenciaVehiculo->fetchAll(PDO::FETCH_OBJ);
        
        // 4. Obtener responsable del proveedor (si existe)
        $sentenciaResponsable = $Conexion->prepare("
            SELECT 
                tp.IdPersonal,
                CONCAT(tp.Nombre,' ',tp.ApPaterno,' ',tp.ApMaterno) AS NombreCompleto,
                tp.CorreoElectronico,
                tp.Telefono
            FROM t_Proveedor tpv
            LEFT JOIN t_personal tp ON tpv.IdPersonalResponsable = tp.IdPersonal
            WHERE tpv.IdProveedor = ?
        ");
        
        $sentenciaResponsable->execute([$idProveedor]);
        $responsable = $sentenciaResponsable->fetch(PDO::FETCH_OBJ);
        
        // 5. Registrar en bitácora
        $sentenciaBitacora = $Conexion->prepare("
            INSERT INTO t_bitacora_movil (
                IdUsuario,
                Accion,
                Detalle,
                FechaHora,
                Dispositivo,
                IdProveedor
            ) VALUES (?, ?, ?, GETDATE(), 'APP_MOVIL', ?)
        ");
        
        $detalle = "Consulta de información de proveedor ID: " . $idProveedor;
        $sentenciaBitacora->execute([
            $idUsuario,
            'CONSULTA_PROVEEDOR',
            $detalle,
            $idProveedor
        ]);
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'IdProveedor' => $proveedor->IdProveedor,
                'NombreProveedor' => $proveedor->NombreProveedor,
                'MotivoIngreso' => $proveedor->MotivoIngreso,
                'Status' => $proveedor->Status,
                'Vigencia' => $proveedor->Vigencia,
                'FechaRegistro' => $proveedor->FechaRegistro,
                'Personal' => $personal,
                'Vehiculos' => $vehiculos,
                'Responsable' => $responsable
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