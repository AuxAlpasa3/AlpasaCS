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

    $requiredFields = ['IdPersonalExterno', 'IdUsuario', 'TipoTransporte', 'Ubicacion'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $IdPersonalExterno = $input['IdPersonalExterno'];
    $IdUsuario = $input['IdUsuario'];
    $Ubicacion = $input['Ubicacion'];
    
    $TipoTransporte = (int)$input['TipoTransporte'];
    if ($TipoTransporte < 0) {
        $TipoTransporte = 0;
    }
    
    $DispN = isset($input['DispN']) ? $input['DispN'] : 'APP_MOVIL';
    $Observaciones = isset($input['Observaciones']) && $input['Observaciones'] !== 'NULL' ? $input['Observaciones'] : '';
    
    $NotificarResponsable = isset($input['NotificarResponsable']) ? (int)$input['NotificarResponsable'] : 0;
    if ($NotificarResponsable > 1) $NotificarResponsable = 1;
    if ($NotificarResponsable < 0) $NotificarResponsable = 0;
    
    $MovimientoPendiente = isset($input['MovimientoPendiente']) ? $input['MovimientoPendiente'] : null;
    $HoraSalidaManual = isset($input['MovimientoPendiente']['HoraSalidaManual']) ? $input['MovimientoPendiente']['HoraSalidaManual'] : null;
    $FechaSalida = isset($input['MovimientoPendiente']['FechaSalida']) ? $input['MovimientoPendiente']['FechaSalida'] : null;

    $sqlPersonalExterno = "SELECT 
                        t1.IdPersonalExterno,
                        t1.NumeroIdentificacion,
                        CONCAT(t1.Nombre,' ',t1.ApPaterno,' ',t1.ApMaterno) as NombreCompleto,
                        t1.Email,
                        t1.Cargo,
                        t1.EmpresaProcedencia,
                        t1.IdPersonalResponsable
                    FROM t_personal_externo t1
                    LEFT JOIN t_cargo t2 ON t1.Cargo = t2.IdCargo
                    WHERE t1.IdPersonalExterno= :IdPersonalExterno";
    
    $stmtPersonalExterno = $Conexion->prepare($sqlPersonalExterno);
    $stmtPersonalExterno->bindParam(':IdPersonalExterno', $IdPersonalExterno, PDO::PARAM_STR);
    $stmtPersonalExterno->execute();
    
    $personalExternoResult = $stmtPersonalExterno->fetchAll(PDO::FETCH_ASSOC);
    if (count($personalExternoResult) === 0) {
        throw new Exception('Personal Externo no encontrado');
    }
    
    $personalExterno = $personalExternoResult[0];
    
    $salidaRegistrada = false;
    $IdMovSalida = null;
    
    $Conexion->beginTransaction();
    
    try {
        if ($MovimientoPendiente && isset($MovimientoPendiente['IdMovEnTSal']) && isset($MovimientoPendiente['FolMovEnt'])) {
            $IdMovEnTSal = (int)$MovimientoPendiente['IdMovEnTSal'];
            $FolMovEnt = (int)$MovimientoPendiente['FolMovEnt'];
            $ObservacionesSalida = isset($MovimientoPendiente['ObservacionesSalida']) ? 
                $MovimientoPendiente['ObservacionesSalida'] : 'Salida manual registrada';
            
            if ($IdMovEnTSal <= 0 || $FolMovEnt <= 0) {
                throw new Exception('ID de movimiento pendiente no válido');
            }
            
            $sqlMovPendiente = "SELECT IdExt, Ubicacion, fecha as FechaEntrada, TiempoMarcaje as HoraEntrada
                                FROM regentext 
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
                
                $sqlSalida = "INSERT INTO regsalext (
                                IdExt,
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
                                :IdExt, 
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
                
                $stmtSalida = $Conexion->prepare($sqlSalida);
                $stmtSalida->bindParam(':IdExt', $movimiento['IdExt'], PDO::PARAM_STR);
                $stmtSalida->bindParam(':IdFolEnt', $FolMovEnt, PDO::PARAM_STR);
                $stmtSalida->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
                $stmtSalida->bindParam(':DispN', $DispN, PDO::PARAM_STR);
                $stmtSalida->bindParam(':FechaSalida', $fechaHoraSalida, PDO::PARAM_STR);
                $stmtSalida->bindParam(':TiempoMarcaje', $HoraSalidaManual, PDO::PARAM_STR);
                $stmtSalida->bindParam(':TipoVehiculo', $TipoTransporte, PDO::PARAM_INT);
                $stmtSalida->bindParam(':Observaciones', $ObservacionesSalida, PDO::PARAM_STR);
                $stmtSalida->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
                $stmtSalida->bindParam(':Notificar', $NotificarResponsable, PDO::PARAM_INT);
                
                if (!$stmtSalida->execute()) {
                    $errorInfo = $stmtSalida->errorInfo();
                    throw new Exception('Error al registrar salida: ' . ($errorInfo[2] ?? 'Error desconocido'));
                }

                $IdMovSalida = (int)$Conexion->lastInsertId();

                if ($IdMovSalida <= 0) {
                    throw new Exception("No se pudo obtener el ID de la inserción");
                }
            
                $sqlActualizar = "UPDATE regentsalext SET 
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
        
        $sqlEntrada = "INSERT INTO regentext (
                        IdExt,
                        Ubicacion,
                        DispN,
                        Fecha,
                        TiempoMarcaje,
                        TipoVehiculo,
                        Observaciones,
                        Usuario,
                        Notificar
                    ) VALUES (
                        :IdExt, 
                        :Ubicacion, 
                        :DispN, 
                        GETDATE(), 
                        :TiempoMarcaje, 
                        :TipoVehiculo, 
                        :Observaciones, 
                        :Usuario, 
                        :Notificar
                    )";
        
        $stmtEntrada = $Conexion->prepare($sqlEntrada);
        $stmtEntrada->bindParam(':IdExt', $IdPersonalExterno, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':DispN', $DispN, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':TiempoMarcaje', $horaActual, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':TipoVehiculo', $TipoTransporte, PDO::PARAM_INT);
        $stmtEntrada->bindParam(':Observaciones', $Observaciones, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':Notificar', $NotificarResponsable, PDO::PARAM_INT);
        
        if (!$stmtEntrada->execute()) {
            $errorInfo = $stmtEntrada->errorInfo();
            throw new Exception('Error al registrar entrada: ' . ($errorInfo[2] ?? 'Error desconocido'));
        }


         $IdMov = (int)$Conexion->lastInsertId();

        if ($IdMov <= 0) {
            throw new Exception("No se pudo obtener el ID de la inserción");
        }
        
        $sqlRegentSalPer = "INSERT INTO regentsalext (
                            IdExt,
                            IdUbicacion,
                            FolMovEnt,
                            FechaEntrada,
                            StatusRegistro
                        ) VALUES (
                            :IdExt, 
                            :IdUbicacion, 
                            :FolMovEnt, 
                            GETDATE(), 
                            1
                        )";
        
        $stmtRegentSalPer = $Conexion->prepare($sqlRegentSalPer);
        $stmtRegentSalPer->bindParam(':IdExt', $IdPersonalExterno, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':IdUbicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':FolMovEnt', $IdMov, PDO::PARAM_INT);
        
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
            'idMovimiento' => $IdMov,
            'IdEntSal' => $IdMov,
            'FolMovEnt' => $IdMov,
            'fecha' => $fechaActual,
            'hora' => $horaActual,
            'Personal Externo' => [
                'nombre' => $personalExterno['NombreCompleto'],
                'NumeroIdentificacion' => $personalExterno['NumeroIdentificacion']
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
    http_response_code(400);
} finally {
    $Conexion = null;
}

echo json_encode($response);
?>