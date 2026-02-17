<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Datos no válidos']);
    exit();
}

try {
    $Conexion->beginTransaction();
    
    $fechaActual = date('Y-m-d H:i:s');
    $visitantesInsertados = [];
    
    foreach ($data['Visitantes'] as $index => $visitante) {
        $sql = "INSERT INTO t_visitantes_Espontaneos (
                    TipoVisita, 
                    Nombre, 
                    ApPaterno, 
                    ApMaterno, 
                    Empresa, 
                    MotivoVisita,
                    IdPersonalVisitado,
                    IdPersonalAutoriza,
                    Telefono,
                    PlacasVehiculo,
                    MarcaVehiculo,
                    ColorVehiculo,
                    FechaRegistro,
                    UsuarioRegistro
                ) VALUES (
                    :tipoVisita,
                    :nombre,
                    :apPaterno,
                    :apMaterno,
                    :empresa,
                    :motivoVisita,
                    :idPersonalVisitado,
                    :idPersonalAutoriza,
                    :telefono,
                    :placasVehiculo,
                    :marcaVehiculo,
                    :colorVehiculo,
                    :fechaRegistro,
                    :usuarioRegistro
                )";
        
        $stmt = $Conexion->prepare($sql);
        
        // Determinar datos del vehículo para este visitante
        $placas = '';
        $marca = '';
        $color = '';
        
        if (!$data['SinVehiculo'] && !empty($data['Vehiculos'])) {
            // Asignar el primer vehículo al primer visitante
            if ($index === 0) {
                $vehiculo = $data['Vehiculos'][0];
                $placas = $vehiculo['Placas'] ?? '';
                $marca = $vehiculo['Marca'] ?? '';
                $color = $vehiculo['Color'] ?? '';
            }
        }
        
        $params = [
            ':tipoVisita' => $data['IdTipoVisita'] ?? '1',
            ':nombre' => $visitante['Nombre'] ?? '',
            ':apPaterno' => $visitante['ApPaterno'] ?? '',
            ':apMaterno' => $visitante['ApMaterno'] ?? '',
            ':empresa' => $visitante['Empresa'] ?? '',
            ':motivoVisita' => $data['MotivoVisita'] ?? '',
            ':idPersonalVisitado' => $data['IdPersonalVisitado'] ?? null,
            ':idPersonalAutoriza' => $data['IdPersonalAutoriza'] ?? null,
            ':telefono' => null,
            ':placasVehiculo' => $placas,
            ':marcaVehiculo' => $marca,
            ':colorVehiculo' => $color,
            ':fechaRegistro' => $fechaActual,
            ':usuarioRegistro' => $data['Usuario'] ?? ''
        ];
        
        $stmt->execute($params);
        $visitantesInsertados[] = $Conexion->lastInsertId();
    }
    
    // 2. Insertar en regentvis
    $sqlRegEntVis = "INSERT INTO regentvis (
                        FolMov,
                        IdVis,
                        Ubicacion,
                        TipoMov,
                        DispN,
                        Fecha,
                        TiempoMarcaje,
                        Observaciones,
                        Usuario,
                        Notificar,
                        Motivo,
                        TipoVisita,
                        SinVehiculo
                    ) VALUES (
                        :folioMov,
                        :idVis,
                        :ubicacion,
                        :tipoMov,
                        :dispN,
                        :fecha,
                        :tiempoMarcaje,
                        :observaciones,
                        :usuario,
                        :notificar,
                        :motivo,
                        :tipoVisita,
                        :sinVehiculo
                    )";
    
    $stmtRegEntVis = $Conexion->prepare($sqlRegEntVis);
    
    $paramsRegEntVis = [
        ':folioMov' => $folioMov,
        ':idVis' => $data['IdTipoVisita'] ?? '1',
        ':ubicacion' => $data['NombreUbicacion'] ?? $data['Ubicacion'] ?? '',
        ':tipoMov' => 'ENTRADA',
        ':dispN' => $data['DispN'] ?? '',
        ':fecha' => $fechaActual,
        ':tiempoMarcaje' => date('H:i:s'),
        ':observaciones' => $data['Observaciones'] ?? '',
        ':usuario' => $data['Usuario'] ?? '',
        ':notificar' => isset($data['NotificarResponsable']) ? ($data['NotificarResponsable'] ? 1 : 0) : 0,
        ':motivo' => $data['MotivoVisita'] ?? '',
        ':tipoVisita' => 'ESPONTANEA',
        ':sinVehiculo' => isset($data['SinVehiculo']) ? ($data['SinVehiculo'] ? 1 : 0) : 0
    ];
    
    $stmtRegEntVis->execute($paramsRegEntVis);
    $idRegEntVis = $Conexion->lastInsertId();
    
    // 3. Insertar en regentsalVis
    foreach ($visitantesInsertados as $idVisitante) {
        $sqlRegEntSalVis = "INSERT INTO regentsalVis (
                                IdVis,
                                IdUbicacion,
                                FolMovEnt,
                                FechaEntrada,
                                StatusRegistro,
                                IdVisitanteEspontaneo
                            ) VALUES (
                                :idVis,
                                :idUbicacion,
                                :folioMovEnt,
                                :fechaEntrada,
                                :statusRegistro,
                                :idVisitanteEspontaneo
                            )";
        
        $stmtRegEntSalVis = $Conexion->prepare($sqlRegEntSalVis);
        
        $paramsRegEntSalVis = [
            ':idVis' => $data['IdTipoVisita'] ?? '1',
            ':idUbicacion' => $data['IdUbicacion'] ?? $data['Ubicacion'] ?? '',
            ':folioMovEnt' => $folioMov,
            ':fechaEntrada' => $fechaActual,
            ':statusRegistro' => 'ACTIVO',
            ':idVisitanteEspontaneo' => $idVisitante
        ];
        
        $stmtRegEntSalVis->execute($paramsRegEntSalVis);
    }
    
    // 4. Guardar fotos si existen
    if (!empty($data['fotos']) && is_array($data['fotos'])) {
        $directorioFotos = '../fotos_visitas/';
        if (!file_exists($directorioFotos)) {
            mkdir($directorioFotos, 0777, true);
        }
        
        foreach ($data['fotos'] as $foto) {
            if (!empty($foto['base64'])) {
                $nombreArchivo = $foto['nombre'] . '_' . date('YmdHis') . '.jpg';
                $rutaCompleta = $directorioFotos . $nombreArchivo;
                
                $imagenData = base64_decode($foto['base64']);
                file_put_contents($rutaCompleta, $imagenData);
            }
        }
    }
    
    // Confirmar transacción
    $Conexion->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Visita espontánea registrada correctamente',
        'folio' => $folioMov,
        'idRegEntVis' => $idRegEntVis,
        'visitantes' => count($visitantesInsertados)
    ]);
    
} catch (PDOException $e) {
    if ($Conexion->inTransaction()) {
        $Conexion->rollBack();
    }
    
    error_log('Error en registrarVisitaEspontanea.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar la visita: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    if ($Conexion->inTransaction()) {
        $Conexion->rollBack();
    }
    
    error_log('Error general en registrarVisitaEspontanea.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error general: ' . $e->getMessage()
    ]);
}
?>