<?php
session_start();
require_once '../api/db/conexion.php';
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

try {
    $noEmpleado = $_GET['NoEmpleado'] ?? '';

    if (empty($noEmpleado)) {
        throw new Exception('No se proporcionó el número de empleado');
    }

    $sentencia = $Conexion->prepare("
        SELECT 
            t1.IdPersonal,
            t1.NoEmpleado,
            t1.Nombre,
            t1.ApPaterno,
            t1.ApMaterno,
            t2.NomCargo,
            t3.NomDepto,
            t4.NomEmpresa,
            t5.NomLargo,
            t1.RutaFoto,
            CASE 
                WHEN t1.Status = 1 THEN 'Activo'
                WHEN t1.Status = 0 THEN 'Inactivo'
                ELSE 'Desconocido'
            END as Estatus,
            t1.Acceso
        FROM t_personal as t1 
        LEFT JOIN t_cargo as t2 ON t1.Cargo = t2.IdCargo
        LEFT JOIN t_departamento as t3 ON t1.Departamento = t3.IdDepartamento
        LEFT JOIN t_empresa as t4 ON t1.Empresa = t4.IdEmpresa
        LEFT JOIN t_ubicacion as t5 ON t1.IdUbicacion = t5.IdUbicacion
        WHERE t1.NoEmpleado = ?
    ");
    
    $sentencia->execute([$noEmpleado]);
    $personal = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($personal) {
        $sentenciaVehiculo = $Conexion->prepare("
            SELECT 
                IdVehiculo,
                Marca,
                Modelo,
                Num_Serie,
                Placas,
                Anio,
                Color,
                RutaFoto 
            FROM t_vehiculos 
            WHERE NoEmpleado = ? AND Activo = 1
            LIMIT 1
        ");
        
        $sentenciaVehiculo->execute([$noEmpleado]);
        $vehiculo = $sentenciaVehiculo->fetch(PDO::FETCH_ASSOC);

        $nombreCompleto = $personal['Nombre'] . ' ' . $personal['ApPaterno'] . ' ' . $personal['ApMaterno'];

        $response['success'] = true;
        $response['vehiculo'] = $vehiculo ? $vehiculo : null;
        $response['personal'] = array(
            'IdPersonal' => $personal['IdPersonal'],
            'NoEmpleado' => $personal['NoEmpleado'],
            'NombreCompleto' => trim($nombreCompleto),
            'Nombre' => $personal['Nombre'],
            'ApPaterno' => $personal['ApPaterno'],
            'ApMaterno' => $personal['ApMaterno'],
            'NomCargo' => $personal['NomCargo'],
            'NomDepto' => $personal['NomDepto'],
            'NomEmpresa' => $personal['NomEmpresa'],
            'NomLargo' => $personal['NomLargo'],
            'RutaFoto' => $personal['RutaFoto'],
            'Estatus' => $personal['Estatus'],
            'Acceso' => $personal['Acceso']
        );
        
    } else {
        $response['message'] = 'No se encontró el empleado con número: ' . $noEmpleado;
    }

} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Error en el servidor: ' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>