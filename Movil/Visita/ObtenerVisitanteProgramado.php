<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../api/db/conexion.php';

$IdVisitanteProgramado = $_POST['IdVisitanteProgramado'] ?? '';
$idUsuario = $_POST['IdUsuario'] ?? '';

if (empty($IdVisitanteProgramado)) {
    echo json_encode(array(
        'success' => false,
        'message' => 'No se proporcionó el ID del visitante'
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sentencia = $Conexion->prepare("
    SELECT 
            t1.IdVisitanteProgramado,
            t1.CodigoVisita,
            t2.TipoVisita,
            t1.MotivoVisita,
            t1.FechaVigencia,
            t1.Comentarios
        FROM t_visitantes_programados as t1 
        INNER JOIN t_tipovisita as t2 on t1.TipoVisita=t2.IdTipoVisita
        INNER JOIN t_estadoCita as t3 on t1.EstadoCita=t3.IdEstadoCita
        WHERE t1.IdVisitanteProgramado =1
        AND t1.EstadoCita in (1,2) AND t1.FechaProgramada>= CAST(GETDATE() AS DATE)
        AND (t1.FechaVigencia >= CAST(GETDATE() AS DATE) OR t1.FechaVigencia IS NULL)");
    
    $sentencia->execute([$IdVisitanteProgramado]);
    $resultados = $sentencia->fetchAll(PDO::FETCH_OBJ);
    
    if (count($resultados) > 0) {
        $visitante = $resultados[0];
        
        $sentenciaPersonal = $Conexion->prepare("
             SELECT 
                IdVisitante,
                IdVisitanteProgramado,
                CONCAT(Nombre,' ',ApPaterno,' ',ApMaterno) AS NombreCompleto,
                Empresa,
                Puesto,
                RutaFoto
            FROM t_detalle_visitantes
            WHERE IdVisitanteProgramado = ?
            ORDER BY Nombre, ApPaterno, ApMaterno
            ");

        $sentenciaPersonal->execute([$IdVisitanteProgramado]);
        $personal = $sentenciaPersonal->fetchAll(PDO::FETCH_OBJ);
        
        $sentenciaVehiculo = $Conexion->prepare("
          SELECT 
              IdVehiculo,
              IdVisitanteProgramado,
              Marca,
              Modelo,
              Placas,
              Color,
              RutaFoto
          FROM t_vehiculos_visitantes
          WHERE IdVisitanteProgramado = ?
          ORDER BY Marca, Modelo, Placas
    ");
        
        $sentenciaVehiculo->execute([$IdVisitanteProgramado]);
        $vehiculos = $sentenciaVehiculo->fetchAll(PDO::FETCH_OBJ);
        
        echo json_encode(array(
            'success' => true,
            'data' => array(
                'IdVisitanteProgramado' => $visitante->IdVisitanteProgramado,
                'CodigoVisita' => $visitante->CodigoVisita,
                'TipoVisita' => $visitante->TipoVisita,
                'MotivoVisita' => $visitante->MotivoVisita,
                'FechaVigencia' => $visitante->FechaVigencia,
                'Comentarios' => $visitante->Comentarios,
                'Personal' => $personal,
                'Vehiculos' => $vehiculos
            )
        ), JSON_UNESCAPED_UNICODE);
        
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'No se encontró el visitante con ID: ' . $IdVisitanteProgramado . ' o su acceso no está vigente, favor de validar con Administrador'
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