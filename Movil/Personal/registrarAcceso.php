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

    error_log('Payload recibido: ' . json_encode($input));

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
    
    $FechaNuevaEntrada = isset($input['MovimientoPendiente']['FechaNuevaEntrada']) ? 
        $input['MovimientoPendiente']['FechaNuevaEntrada'] : (isset($input['FechaEntrada']) ? $input['FechaEntrada'] : date('Y-m-d'));
    
    $HoraNuevaEntrada = isset($input['MovimientoPendiente']['HoraNuevaEntrada']) ? 
        $input['MovimientoPendiente']['HoraNuevaEntrada'] : (isset($input['HoraEntrada']) ? $input['HoraEntrada'] : date('H:i:s'));
    
    error_log("Fecha nueva entrada: $FechaNuevaEntrada");
    error_log("Hora nueva entrada: $HoraNuevaEntrada");
    error_log("ID Vehículo actual: " . ($IdVeh ?: 'NULL'));
    error_log("Tipo Transporte actual: $TipoTransporte");
    error_log("Libre Uso actual: $LibreUso");
    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $FechaNuevaEntrada)) {
        throw new Exception("Formato de fecha de nueva entrada inválido. Debe ser YYYY-MM-DD");
    }
    
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $HoraNuevaEntrada)) {
        if (preg_match('/^\d{2}:\d{2}$/', $HoraNuevaEntrada)) {
            $HoraNuevaEntrada .= ':00';
        } else {
            throw new Exception("Formato de hora de nueva entrada inválido. Debe ser HH:MM:SS");
        }
    }
    
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
    $IdMovSalidaVehiculo = null;
    $salidaVehiculoRegistrada = false;
    
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
            
            error_log("Procesando movimiento pendiente: IdMovEnTSal=$IdMovEnTSal, FolMovEnt=$FolMovEnt");
            
            $sqlMovPendiente = "SELECT 
                                    t1.IdPer, 
                                    t1.Ubicacion, 
                                    t1.fecha as FechaEntrada, 
                                    t1.TiempoMarcaje as HoraEntrada, 
                                    t1.TipoVehiculo, 
                                    t1.IdVeh,
                                    t2.tieneVehiculo
                                FROM regentper t1
                                LEFT JOIN regentsalper t2 ON t1.FolMov = t2.FolMovEnt
                                WHERE t1.FolMov = :FolMovEnt";
            
            $stmtMovPendiente = $Conexion->prepare($sqlMovPendiente);
            $stmtMovPendiente->bindParam(':FolMovEnt', $FolMovEnt, PDO::PARAM_INT);
            $stmtMovPendiente->execute();
            
            $movimientoResult = $stmtMovPendiente->fetchAll(PDO::FETCH_ASSOC);
            if (count($movimientoResult) > 0) {
                
                $movimiento = $movimientoResult[0];
                $tieneVehiculoPendiente = (int)$movimiento['tieneVehiculo'];
                $IdVehPendiente = $movimiento['IdVeh'] ? (int)$movimiento['IdVeh'] : null;
                
                error_log("Vehículo pendiente ID: " . ($IdVehPendiente ?: 'NULL'));
                error_log("Tiene vehículo pendiente: $tieneVehiculoPendiente");
                
                $fechaEntrada = $movimiento['FechaEntrada'];
                $horaEntrada = isset($movimiento['HoraEntrada']) ? $movimiento['HoraEntrada'] : '00:00:00';
                
                if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEntrada)) {
                    $fechaEntrada = date('Y-m-d', strtotime($fechaEntrada));
                }
                
                $fechaHoraEntrada = date('Y-m-d H:i:s', strtotime($fechaEntrada . ' ' . $horaEntrada));
                $fechaHoraSalida = date('Y-m-d H:i:s', strtotime($FechaSalida . ' ' . $HoraSalidaManual));
                
                error_log("Fecha/hora entrada anterior: $fechaHoraEntrada");
                error_log("Fecha/hora salida manual: $fechaHoraSalida");
                
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
                
                error_log("Tiempo total estancia: $tiempoFormateado ($minutosTotales minutos)");
                
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
                error_log("Salida personal registrada ID: $IdMovSalida");

                if ($IdMovSalida <= 0) {
                    throw new Exception("No se pudo obtener el ID de la inserción");
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
                error_log("Salida del movimiento anterior registrada exitosamente");
            }
        }
        
        $fechaHoraNuevaEntrada = $FechaNuevaEntrada . ' ' . $HoraNuevaEntrada;
        error_log("Registrando nueva entrada: $fechaHoraNuevaEntrada");
        
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
                        :Fecha, 
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
        $stmtEntrada->bindParam(':Fecha', $FechaNuevaEntrada, PDO::PARAM_STR);
        $stmtEntrada->bindParam(':TiempoMarcaje', $HoraNuevaEntrada, PDO::PARAM_STR);
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
        error_log("Entrada personal registrada ID: $IdMov");

        if ($IdMov <= 0) {
            throw new Exception("No se pudo obtener el ID de la inserción");
        }
        
        $IdMovVeh = null;
        if ($IdVeh && $TipoTransporte != 0) {
            error_log("Registrando entrada de vehículo ID: $IdVeh");
            
            $tipoVehiculoVeh = $LibreUso == 1 ? 1 : 2;
            
            $sqlVerificarEntradaVehiculoPendiente = "SELECT 
                t1.FolMov as FolMovEntVeh,
                t1.IdVeh,
                t1.Ubicacion as UbicacionEntrada,
                t1.Fecha as FechaEntrada,
                t1.TiempoMarcaje as HoraEntrada,
                t1.TipoVehiculo,
                t2.FolMovEnt,
                t2.FolMovSal,
                t2.StatusRegistro
            FROM regentveh t1
            LEFT JOIN regentsalveh t2 ON t1.FolMov = t2.FolMovEnt
            WHERE t1.IdVeh = :IdVeh 
            AND t2.StatusRegistro = 1
            ORDER BY t1.Fecha DESC, t1.TiempoMarcaje DESC";
            
            $stmtVerificarEntradaVehiculoPendiente = $Conexion->prepare($sqlVerificarEntradaVehiculoPendiente);
            $stmtVerificarEntradaVehiculoPendiente->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
            $stmtVerificarEntradaVehiculoPendiente->execute();
            
            $entradaVehiculoPendienteResult = $stmtVerificarEntradaVehiculoPendiente->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($entradaVehiculoPendienteResult) > 0) {
                $entradaVehiculoPendiente = $entradaVehiculoPendienteResult[0];
                $FolMovEntVehPendiente = (int)$entradaVehiculoPendiente['FolMovEntVeh'];
                $StatusRegistro = (int)$entradaVehiculoPendiente['StatusRegistro'];
                
                error_log("Vehículo $IdVeh tiene entrada pendiente: FolMovEntVeh=$FolMovEntVehPendiente, StatusRegistro=$StatusRegistro");
                
                if ($StatusRegistro == 1) {
                    error_log("Registrando salida para vehículo pendiente...");
                    
                    $fechaEntradaVehiculo = $entradaVehiculoPendiente['FechaEntrada'];
                    $horaEntradaVehiculo = $entradaVehiculoPendiente['HoraEntrada'];
                    $UbicacionEntradaVehiculo = $entradaVehiculoPendiente['UbicacionEntrada'];
                    
                    $fechaHoraEntradaVehiculo = date('Y-m-d H:i:s', strtotime($fechaEntradaVehiculo . ' ' . $horaEntradaVehiculo));
                    
                    $sqlDiferenciaVehiculo = "SELECT DATEDIFF(MINUTE, 
                                      CAST(:FechaHoraEntrada AS DATETIME), 
                                      CAST(:FechaHoraSalida AS DATETIME)) as Minutos";
                    
                    $stmtDiferenciaVehiculo = $Conexion->prepare($sqlDiferenciaVehiculo);
                    $stmtDiferenciaVehiculo->bindParam(':FechaHoraEntrada', $fechaHoraEntradaVehiculo, PDO::PARAM_STR);
                    $stmtDiferenciaVehiculo->bindParam(':FechaHoraSalida', $fechaHoraNuevaEntrada, PDO::PARAM_STR);
                    $stmtDiferenciaVehiculo->execute();
                    
                    $diferenciaVehiculoResult = $stmtDiferenciaVehiculo->fetchAll(PDO::FETCH_ASSOC);
                    $diferenciaVehiculo = $diferenciaVehiculoResult[0];
                    
                    $minutosTotalesVehiculo = (int)$diferenciaVehiculo['Minutos'];
                    if ($minutosTotalesVehiculo < 0) {
                        $minutosTotalesVehiculo = 0;
                    }
                    
                    $horasTotalesVehiculo = floor($minutosTotalesVehiculo / 60);
                    $minutosRestantesVehiculo = $minutosTotalesVehiculo % 60;
                    
                    $tiempoFormateadoVehiculo = sprintf("%02d:%02d", $horasTotalesVehiculo, $minutosRestantesVehiculo);
                    
                    error_log("Tiempo vehículo: $tiempoFormateadoVehiculo ($minutosTotalesVehiculo minutos)");
                    
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
                    
                    $tipoVehiculoSalida = (int)$entradaVehiculoPendiente['TipoVehiculo'];
                    $observacionesSalidaVehiculo = "Salida automática - vehículo ingresando a nueva ubicación";
                    
                    $stmtSalidaVeh = $Conexion->prepare($sqlSalidaVehiculo);
                    $stmtSalidaVeh->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
                    $stmtSalidaVeh->bindParam(':IdFolEnt', $FolMovEntVehPendiente, PDO::PARAM_INT);
                    $stmtSalidaVeh->bindParam(':Ubicacion', $UbicacionEntradaVehiculo, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':DispN', $DispN, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':FechaSalida', $FechaNuevaEntrada, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':TiempoMarcaje', $HoraNuevaEntrada, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':TipoVehiculo', $tipoVehiculoSalida, PDO::PARAM_INT);
                    $stmtSalidaVeh->bindParam(':Observaciones', $observacionesSalidaVehiculo, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
                    $stmtSalidaVeh->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
                    
                    if (!$stmtSalidaVeh->execute()) {
                        $errorInfo = $stmtSalidaVeh->errorInfo();
                        error_log("Error SQL regsalveh: " . json_encode($errorInfo));
                        throw new Exception('Error al registrar salida vehículo: ' . ($errorInfo[2] ?? 'Error desconocido'));
                    }
                    
                    $IdMovSalidaVehiculo = (int)$Conexion->lastInsertId();
                    error_log("Salida vehículo registrada ID: $IdMovSalidaVehiculo");
                    
                    $sqlUpdateRegentsalveh = "UPDATE regentsalveh SET 
                                            FolMovSal = :FolMovSal,
                                            FechaSalida = :FechaSalida,
                                            Tiempo = :Tiempo,
                                            StatusRegistro = 2
                                            WHERE FolMovEnt = :FolMovEnt";
                    
                    $stmtUpdateRegentsalveh = $Conexion->prepare($sqlUpdateRegentsalveh);
                    $stmtUpdateRegentsalveh->bindParam(':FolMovSal', $IdMovSalidaVehiculo, PDO::PARAM_INT);
                    $stmtUpdateRegentsalveh->bindParam(':FechaSalida', $fechaHoraNuevaEntrada, PDO::PARAM_STR);
                    $stmtUpdateRegentsalveh->bindParam(':Tiempo', $tiempoFormateado, PDO::PARAM_STR);
                    $stmtUpdateRegentsalveh->bindParam(':FolMovEnt', $FolMovEntVehPendiente, PDO::PARAM_INT);
                    
                    if (!$stmtUpdateRegentsalveh->execute()) {
                        $errorInfo = $stmtUpdateRegentsalveh->errorInfo();
                        throw new Exception('Error al actualizar regentsalveh: ' . ($errorInfo[2] ?? 'Error desconocido'));
                    }
                    
                    $salidaVehiculoRegistrada = true;
                    error_log("Salida vehículo registrada exitosamente");
                }
            } else {
                error_log("No hay entrada pendiente para vehículo $IdVeh");
            }
            
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
                            :Fecha, 
                            :TiempoMarcaje, 
                            :TipoVehiculo, 
                            :Observaciones, 
                            :Usuario, 
                            :Notificar
                        )";
            
            $stmtEntradaVeh = $Conexion->prepare($sqlEntradaVeh);
            $stmtEntradaVeh->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
            $stmtEntradaVeh->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':DispN', $DispN, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':Fecha', $FechaNuevaEntrada, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':TiempoMarcaje', $HoraNuevaEntrada, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':TipoVehiculo', $tipoVehiculoVeh, PDO::PARAM_INT);
            $stmtEntradaVeh->bindParam(':Observaciones', $Observaciones, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
            $stmtEntradaVeh->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
            
            if (!$stmtEntradaVeh->execute()) {
                $errorInfo = $stmtEntradaVeh->errorInfo();
                throw new Exception('Error al registrar entrada vehículo: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
            
            $IdMovVeh = (int)$Conexion->lastInsertId();
            error_log("Entrada vehículo registrada ID: $IdMovVeh");
            
            $sqlRegentSalVehiculo = "INSERT INTO regentsalveh (
                                    IdVeh,
                                    IdUbicacion,
                                    FolMovEnt,
                                    FechaEntrada,
                                    StatusRegistro
                                ) VALUES (
                                    :IdVeh, 
                                    :IdUbicacion, 
                                    :FolMovEnt, 
                                    :FechaEntrada, 
                                    1
                                )";
            
            $stmtRegentSalVehiculo = $Conexion->prepare($sqlRegentSalVehiculo);
            $stmtRegentSalVehiculo->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
            $stmtRegentSalVehiculo->bindParam(':IdUbicacion', $Ubicacion, PDO::PARAM_STR);
            $stmtRegentSalVehiculo->bindParam(':FolMovEnt', $IdMovVeh, PDO::PARAM_INT);
            $stmtRegentSalVehiculo->bindParam(':FechaEntrada', $fechaHoraNuevaEntrada, PDO::PARAM_STR);
            
            if (!$stmtRegentSalVehiculo->execute()) {
                $errorInfo = $stmtRegentSalVehiculo->errorInfo();
                throw new Exception('Error al registrar en regentsalveh: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
        }
        
        $tieneVehiculo = $TipoTransporte != 0 ? ($IdMovVeh ?: 0) : 0;
        
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
                            :FechaEntrada, 
                            :tieneVehiculo, 
                            1
                        )";
        
        $stmtRegentSalPer = $Conexion->prepare($sqlRegentSalPer);
        $stmtRegentSalPer->bindParam(':IdPer', $IdPersonal, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':IdUbicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':FolMovEnt', $IdMov, PDO::PARAM_INT);
        $stmtRegentSalPer->bindParam(':FechaEntrada', $fechaHoraNuevaEntrada, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':tieneVehiculo', $tieneVehiculo, PDO::PARAM_INT);
        
        if (!$stmtRegentSalPer->execute()) {
            $errorInfo = $stmtRegentSalPer->errorInfo();
            throw new Exception('Error al registrar en regentsalper: ' . ($errorInfo[2] ?? 'Error desconocido'));
        }
        
        $Conexion->commit();
        error_log("Transacción completada exitosamente");
        
        $response['success'] = true;
        $response['message'] = $salidaRegistrada ? 
            '✅ Salida registrada y nueva entrada procesada correctamente' : 
            '✅ Acceso registrado correctamente';
        
        $response['data'] = [
            'FolMovEnt' => $IdMov,
            'fecha' => $FechaNuevaEntrada,
            'hora' => $HoraNuevaEntrada,
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
        
        if ($salidaVehiculoRegistrada) {
            $response['data']['salida_vehiculo_info'] = [
                'folio_salida_vehiculo' => $IdMovSalidaVehiculo,
                'folio_entrada_vehiculo_anterior' => $FolMovEntVehPendiente ?? null,
                'tiempo_estancia_vehiculo' => $tiempoFormateadoVehiculo ?? null
            ];
        }
        
    } catch (Exception $e) {
        $Conexion->rollBack();
        error_log("Error en transacción: " . $e->getMessage());
        throw $e;
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Error en registrarAcceso: ' . $e->getMessage());
    http_response_code(400);
} finally {
    $Conexion = null;
    error_log("Respuesta enviada: " . json_encode($response));
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>