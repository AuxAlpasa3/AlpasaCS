<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../api/db/conexion.php';

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Datos no válidos');
    }

    $requiredFields = ['IdPersonal', 'IdUsuario', 'TipoTransporte', 'Ubicacion'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $IdPersonal = $input['IdPersonal'];
    $IdUsuario = $input['IdUsuario'];
    $Ubicacion = $input['Ubicacion'];
    
    $IdVeh = $input['IdVehiculo'] ?? null;
    $TipoTransporte = (int)$input['TipoTransporte'];
    if ($TipoTransporte < 0) {
        $TipoTransporte = 0;
    }
    
    $LibreUso = $input['LibreUso'] ?? 0;
    $DispN = isset($input['DispN']) ? $input['DispN'] : 'APP_MOVIL';
    $Observaciones = isset($input['Observaciones']) && $input['Observaciones'] !== 'NULL' ? $input['Observaciones'] : '';
    
    $NotificarSupervisor = isset($input['NotificarSupervisor']) ? (int)$input['NotificarSupervisor'] : 0;
    if ($NotificarSupervisor > 1) $NotificarSupervisor = 1;
    if ($NotificarSupervisor < 0) $NotificarSupervisor = 0;
    
    $MovimientoPendiente = isset($input['MovimientoPendiente']) ? $input['MovimientoPendiente'] : null;
    $HoraSalidaManual = isset($input['MovimientoPendiente']['HoraSalidaManual']) ? $input['MovimientoPendiente']['HoraSalidaManual'] : null;
    $FechaSalida = isset($input['MovimientoPendiente']['FechaSalida']) ? $input['MovimientoPendiente']['FechaSalida'] : null;

    $sqlPersonal = "SELECT 
                        t1.IdPersonal,
                        t1.NoEmpleado,
                        CONCAT(t1.Nombre,' ',t1.ApPaterno,' ',t1.ApMaterno) as NombreCompleto,
                        t1.Email,
                        t2.NomCargo,
                        t3.NomDepto,
                        t4.NomEmpresa
                    FROM t_personal t1
                    LEFT JOIN t_cargo t2 ON t1.Cargo = t2.IdCargo
                    LEFT JOIN t_departamento t3 ON t1.Departamento = t3.IdDepartamento
                    LEFT JOIN t_empresa t4 ON t1.Empresa = t4.IdEmpresa
                    WHERE t1.IdPersonal = :IdPersonal";
    
    $stmtPersonal = $Conexion->prepare($sqlPersonal);
    $stmtPersonal->bindParam(':IdPersonal', $IdPersonal, PDO::PARAM_STR);
    $stmtPersonal->execute();
    
    $personalResult = $stmtPersonal->fetchAll(PDO::FETCH_ASSOC);
    if (count($personalResult) === 0) {
        throw new Exception('Personal no encontrado');
    }
    
    $personal = $personalResult[0];
    
    $salidaRegistrada = false;
    $IdMovSalida = null;
    
    $Conexion->beginTransaction();
    
    try {
        if ($MovimientoPendiente && isset($MovimientoPendiente['IdMovEnTSal']) && isset($MovimientoPendiente['FolMovEnt'])) 
        {
            $IdMovEnTSal = (int)$MovimientoPendiente['IdMovEnTSal'];
            $FolMovEnt = (int)$MovimientoPendiente['FolMovEnt'];
            $ObservacionesSalida = isset($MovimientoPendiente['ObservacionesSalida']) ? 
                $MovimientoPendiente['ObservacionesSalida'] : 'Salida manual registrada desde móvil';
            
            if ($IdMovEnTSal <= 0 || $FolMovEnt <= 0) {
                throw new Exception('ID de movimiento pendiente no válido');
            }
            
            $sqlMovPendiente = "SELECT IdPer, Ubicacion, fecha as FechaEntrada, TiempoMarcaje as HoraEntrada, TipoVehiculo, IdVeh
                                FROM regentper 
                                WHERE FolMov = :FolMovEnt";
            
            $stmtMovPendiente = $Conexion->prepare($sqlMovPendiente);
            $stmtMovPendiente->bindParam(':FolMovEnt', $FolMovEnt, PDO::PARAM_INT);
            $stmtMovPendiente->execute();
            
            $movimientoResult = $stmtMovPendiente->fetchAll(PDO::FETCH_ASSOC);
            if (count($movimientoResult) > 0) {
                
                $movimiento = $movimientoResult[0];
                
                $fechaActual = date('Y-m-d');

                $fechaEntrada = $movimiento['FechaEntrada'];
                $horaEntrada = isset($movimiento['HoraEntrada']) ? $movimiento['HoraEntrada'] : '00:00:00';
                
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEntrada)) {
                    $fechaEntrada = date('Y-m-d', strtotime($fechaEntrada));
                }
                
                $fechaHoraEntrada = date('Y-m-d H:i:s', strtotime($fechaEntrada . ' ' . $horaEntrada));
                $fechaHoraSalida = date('Y-m-d H:i:s', strtotime($FechaSalida . ' ' . $HoraSalidaManual));
                
                $sqlDiferencia = "SELECT DATEDIFF(MINUTE, 
                                  CAST(:FechaHoraEntrada AS DATETIME), 
                                  CAST(:FechaHoraSalida AS DATETIME)) as Minutos";
                
                $stmtDiferencia = $Conexion->prepare($sqlDiferencia);
                $stmtDiferencia->bindParam(':FechaHoraEntrada', $fechaHoraEntrada, PDO::PARAM_STR);
                $stmtDiferencia->bindParam(':FechaHoraSalida', $fechaHoraSalida, PDO::PARAM_STR);
                $stmtDiferencia->execute();
                
                $diferenciaResult = $stmtDiferencia->fetchAll(PDO::FETCH_ASSOC);
                $diferencia = $diferenciaResult[0];
                
                $minutosTotales = (int)$diferencia['Minutos'];
                if ($minutosTotales < 0) {
                    $minutosTotales = 0;
                }
                
                $horasTotales = floor($minutosTotales / 60);
                $minutosRestantes = $minutosTotales % 60;
                
                $tiempoFormateado = sprintf("%02d:%02d", $horasTotales, $minutosRestantes);
                
                $sqlSalida = "INSERT INTO regsalper (
                                IdPer,
                                IdFolEnt,
                                Ubicacion,
                                DispN,
                                Fecha,
                                TiempoMarcaje,
                                TipoVehiculo,
                                Observaciones,
                                Usuario,
                                Notificar,
                                IdVeh
                            ) VALUES (
                                :IdPer, 
                                :IdFolEnt,
                                :Ubicacion, 
                                :DispN, 
                                :FechaSalida, 
                                :TiempoMarcaje, 
                                :TipoVehiculo, 
                                :Observaciones, 
                                :Usuario, 
                                :Notificar,
                                :IdVeh
                            )";
                
                $stmtSalida = $Conexion->prepare($sqlSalida);
                $stmtSalida->bindParam(':IdPer', $movimiento['IdPer'], PDO::PARAM_STR);
                $stmtSalida->bindParam(':IdFolEnt', $FolMovEnt, PDO::PARAM_STR);
                $stmtSalida->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
                $stmtSalida->bindParam(':DispN', $DispN, PDO::PARAM_STR);
                $stmtSalida->bindParam(':FechaSalida', $fechaHoraSalida, PDO::PARAM_STR);
                $stmtSalida->bindParam(':TiempoMarcaje', $HoraSalidaManual, PDO::PARAM_STR);
                $stmtSalida->bindParam(':TipoVehiculo', $TipoTransporte, PDO::PARAM_INT);
                $stmtSalida->bindParam(':Observaciones', $ObservacionesSalida, PDO::PARAM_STR);
                $stmtSalida->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
                $stmtSalida->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
                $stmtSalida->bindParam(':IdVeh', $movimiento['IdVeh'], PDO::PARAM_INT);
                
                if (!$stmtSalida->execute()) {
                    $errorInfo = $stmtSalida->errorInfo();
                    throw new Exception('Error al registrar salida: ' . ($errorInfo[2] ?? 'Error desconocido'));
                }

                $IdMovSalida = (int)$Conexion->lastInsertId();

                if ($IdMovSalida <= 0) {
                    throw new Exception("No se pudo obtener el ID de la inserción");
                }

                if ($movimiento['IdVeh']) {
                    $sqlSalidaVehiculo = "INSERT INTO regsalveh (
                                    IdVeh,
                                    IdFolEnt,
                                    Ubicacion,
                                    DispN,
                                    Fecha,
                                    TiempoMarcaje,
                                    TipoVehiculo,
                                    Observaciones,
                                    Usuario,
                                    Notificar
                                ) VALUES (
                                    :IdVeh, 
                                    :IdFolEnt,
                                    :Ubicacion, 
                                    :DispN, 
                                    :FechaSalida, 
                                    :TiempoMarcaje, 
                                    :TipoVehiculo, 
                                    :Observaciones, 
                                    :Usuario, 
                                    :Notificar
                                )";
                    
                    $stmtSalidaVeh = $Conexion->prepare($sqlSalidaVehiculo);
                    $stmtSalidaVeh->bindParam(':IdVeh', $movimiento['IdVeh'], PDO::PARAM_INT);
                    $stmtSalidaVeh->bindParam(':IdFolEnt', $FolMovEnt, PDO::PARAM_INT);
                    $stmtSalidaVeh->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':DispN', $DispN, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':FechaSalida', $fechaHoraSalida, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':TiempoMarcaje', $HoraSalidaManual, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':TipoVehiculo', $TipoTransporte, PDO::PARAM_INT);
                    $stmtSalidaVeh->bindParam(':Observaciones', $ObservacionesSalida, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
                    
                    if (!$stmtSalidaVeh->execute()) {
                        $errorInfo = $stmtSalidaVeh->errorInfo();
                        throw new Exception('Error al registrar salida vehículo: ' . ($errorInfo[2] ?? 'Error desconocido'));
                    }
                }
            
                $sqlActualizar = "UPDATE regentsalper SET 
                                  FolMovSal = :FolMovSal,
                                  FechaSalida = :FechaSalida,
                                  Tiempo = :Tiempo,
                                  StatusRegistro = 2
                                  WHERE IdMovEnTSal = :IdMovEnTSal";
                
                $stmtActualizar = $Conexion->prepare($sqlActualizar);
                $stmtActualizar->bindParam(':FolMovSal', $IdMovSalida, PDO::PARAM_INT);
                $stmtActualizar->bindParam(':FechaSalida', $fechaHoraSalida, PDO::PARAM_STR);
                $stmtActualizar->bindParam(':Tiempo', $tiempoFormateado, PDO::PARAM_STR);
                $stmtActualizar->bindParam(':IdMovEnTSal', $IdMovEnTSal, PDO::PARAM_INT);
                
                if (!$stmtActualizar->execute()) {
                    $errorInfo = $stmtActualizar->errorInfo();
                    throw new Exception('Error al actualizar movimiento: ' . ($errorInfo[2] ?? 'Error desconocido'));
                }
                
                $salidaRegistrada = true;
            }
        }
        
        $fechaActual = date('Y-m-d');
        $horaActual = date('H:i:s');
        
        $sqlEntrada = "INSERT INTO regentper (
                        IdPer,
                        Ubicacion,
                        DispN,
                        Fecha,
                        TiempoMarcaje,
                        TipoVehiculo,
                        Observaciones,
                        Usuario,
                        Notificar,
                        IdVeh
                    ) VALUES (
                        :IdPer, 
                        :Ubicacion, 
                        :DispN, 
                        GETDATE(), 
                        :TiempoMarcaje, 
                        :TipoVehiculo, 
                        :Observaciones, 
                        :Usuario, 
                        :Notificar,
                        :IdVeh
                    )";
        
        $stmtEntrada = $Conexion->prepare($sqlEntrada);
        $stmtEntrada->bindParam(':IdPer', $IdPersonal, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':DispN', $DispN, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':TiempoMarcaje', $horaActual, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':TipoVehiculo', $TipoTransporte, PDO::PARAM_INT);
        $stmtEntrada->bindParam(':Observaciones', $Observaciones, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
        $stmtEntrada->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
        
        if (!$stmtEntrada->execute()) {
            $errorInfo = $stmtEntrada->errorInfo();
            throw new Exception('Error al registrar entrada: ' . ($errorInfo[2] ?? 'Error desconocido'));
        }

        $IdMov = (int)$Conexion->lastInsertId();

        if ($IdMov <= 0) {
            throw new Exception("No se pudo obtener el ID de la inserción");
        }
        
        if ($IdVeh && $TipoTransporte != 0) {
            $sqlEntradaVeh = "INSERT INTO regentveh (
                            IdVeh,
                            Ubicacion,
                            DispN,
                            Fecha,
                            TiempoMarcaje,
                            TipoVehiculo,
                            Observaciones,
                            Usuario,
                            Notificar
                        ) VALUES (
                            :IdVeh,
                            :Ubicacion, 
                            :DispN, 
                            GETDATE(), 
                            :TiempoMarcaje, 
                            :TipoVehiculo, 
                            :Observaciones, 
                            :Usuario, 
                            :Notificar
                        )";
            
            $tipoVehiculoVeh = $LibreUso == 1 ? 1 : 2;
            
            $stmtEntradaVeh = $Conexion->prepare($sqlEntradaVeh);
            $stmtEntradaVeh->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
            $stmtEntradaVeh->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':DispN', $DispN, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':TiempoMarcaje', $horaActual, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':TipoVehiculo', $tipoVehiculoVeh, PDO::PARAM_INT);
            $stmtEntradaVeh->bindParam(':Observaciones', $Observaciones, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
            
            if (!$stmtEntradaVeh->execute()) {
                $errorInfo = $stmtEntradaVeh->errorInfo();
                throw new Exception('Error al registrar entrada vehículo: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
        }
        
        $tieneVehiculo = ($TipoTransporte == 0) ? 0 : 1;
        
        $sqlRegentSalPer = "INSERT INTO regentsalper (
                            IdPer,
                            IdUbicacion,
                            FolMovEnt,
                            FechaEntrada,
                            tieneVehiculo,
                            StatusRegistro
                        ) VALUES (
                            :IdPer, 
                            :IdUbicacion, 
                            :FolMovEnt, 
                            GETDATE(), 
                            :tieneVehiculo, 
                            1
                        )";
        
        $stmtRegentSalPer = $Conexion->prepare($sqlRegentSalPer);
        $stmtRegentSalPer->bindParam(':IdPer', $IdPersonal, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':IdUbicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':FolMovEnt', $IdMov, PDO::PARAM_INT);
        $stmtRegentSalPer->bindParam(':tieneVehiculo', $tieneVehiculo, PDO::PARAM_INT);
        
        if (!$stmtRegentSalPer->execute()) {
            $errorInfo = $stmtRegentSalPer->errorInfo();
            throw new Exception('Error al registrar en regentsalper: ' . ($errorInfo[2] ?? 'Error desconocido'));
        }
        
        $Conexion->commit();
        
        $response['success'] = true;
        $response['message'] = $salidaRegistrada ? 
            'Salida registrada y nueva entrada procesada correctamente' : 
            'Acceso registrado correctamente';
        
        $response['data'] = [
            'FolMovEnt' => $IdMov,
            'fecha' => $fechaActual,
            'hora' => $horaActual,
            'personal' => [
                'nombre' => $personal['NombreCompleto'],
                'noEmpleado' => $personal['NoEmpleado']
            ],
            'salida_registrada' => $salidaRegistrada
        ];
        
        if ($salidaRegistrada) {
            $response['data']['salida_info'] = [
                'id_movimiento_anterior' => $MovimientoPendiente['IdMovEnTSal'],
                'folio_salida' => $IdMovSalida,
                'hora_salida' => $HoraSalidaManual,
                'tiempo_estancia' => $tiempoFormateado
            ];
        }
        
    } catch (Exception $e) {
        $Conexion->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Error en registrarAcceso: ' . $e->getMessage());
    http_response_code(400);
} finally {
    $Conexion = null;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>