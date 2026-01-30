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
    $TipoTransporte = $input['TipoTransporte'];
    $DispN = isset($input['DispN']) ? $input['DispN'] : 'APP_MOVIL';
    $Observaciones = isset($input['Observaciones']) && $input['Observaciones'] !== 'NULL' ? $input['Observaciones'] : '';
    $NotificarSupervisor = isset($input['NotificarSupervisor']) ? (bool)$input['NotificarSupervisor'] : false;
    
    $MovimientoPendiente = isset($input['MovimientoPendiente']) ? $input['MovimientoPendiente'] : null;
    $HoraSalidaManual = isset($input['MovimientoPendiente']['HoraSalidaManual']) ? $input['MovimientoPendiente']['HoraSalidaManual'] : null;

    // Validar que el personal existe
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
    
    if ($stmtPersonal->rowCount() === 0) {
        throw new Exception('Personal no encontrado');
    }
    
    $personal = $stmtPersonal->fetch(PDO::FETCH_ASSOC);
    
    $salidaRegistrada = false;
    $IdMovSalida = null;
    
    $Conexion->beginTransaction();
    
    try {
        if ($MovimientoPendiente && isset($MovimientoPendiente['IdMovEnTSal']) && isset($MovimientoPendiente['FolMovEnt'])) {
            $IdMovEnTSal = $MovimientoPendiente['IdMovEnTSal'];
            $FolMovEnt = $MovimientoPendiente['FolMovEnt'];
            $ObservacionesSalida = isset($MovimientoPendiente['ObservacionesSalida']) ? 
                $MovimientoPendiente['ObservacionesSalida'] : 'Salida manual registrada';
            
            $sqlMovPendiente = "SELECT IdPer, IdUbicacion, fecha as FechaEntrada, TiempoMarcaje as HoraEntrada
                                FROM regentper 
                                WHERE  FolMovEnt = :FolMovEnt";
            
            $stmtMovPendiente = $Conexion->prepare($sqlMovPendiente);
            $stmtMovPendiente->bindParam(':IdMovEnTSal', $IdMovEnTSal, PDO::PARAM_INT);
            $stmtMovPendiente->bindParam(':FolMovEnt', $FolMovEnt, PDO::PARAM_INT);
            $stmtMovPendiente->execute();
            
            if ($stmtMovPendiente->rowCount() > 0) {
                $movimiento = $stmtMovPendiente->fetch(PDO::FETCH_ASSOC);
                
                // Determinar hora de salida
                $fechaActual = date('Y-m-d');
                $horaSalida = $HoraSalidaManual ?: date('H:i:s');
                
                // Calcular tiempo transcurrido
                $fechaEntrada = $movimiento['FechaEntrada'];
                $horaEntrada = isset($movimiento['HoraEntrada']) ? $movimiento['HoraEntrada'] : '00:00:00';
                
                // Crear fecha/hora completa de entrada
                $fechaHoraEntrada = $fechaEntrada . ' ' . $horaEntrada;
                $fechaHoraSalida = $fechaActual . ' ' . $horaSalida;
                
                // Calcular diferencia en minutos
                $sqlDiferencia = "SELECT DATEDIFF(MINUTE, :FechaHoraEntrada, :FechaHoraSalida) as Minutos";
                $stmtDiferencia = $Conexion->prepare($sqlDiferencia);
                $stmtDiferencia->bindParam(':FechaHoraEntrada', $fechaHoraEntrada);
                $stmtDiferencia->bindParam(':FechaHoraSalida', $fechaHoraSalida);
                $stmtDiferencia->execute();
                $diferencia = $stmtDiferencia->fetch(PDO::FETCH_ASSOC);
                
                $minutosTotales = $diferencia['Minutos'];
                $horasTotales = floor($minutosTotales / 60);
                $minutosRestantes = $minutosTotales % 60;
                
                // Formatear tiempo como HH:MM:SS
                $tiempoFormateado = sprintf("%02d:%02d:%02d", $horasTotales, $minutosRestantes, 0);
                
                // Insertar en regsalper (SALIDA)
                $sqlSalida = "INSERT INTO regsalper (
                                IdPer,
                                Ubicacion,
                                DispN,
                                Fecha,
                                TiempoMarcaje,
                                TipoVehiculo,
                                Observaciones,
                                Usuario,
                                Notificar
                            ) VALUES (
                                :IdPer, 
                                :Ubicacion, 
                                :DispN, 
                                GETDATE(), 
                                :TiempoMarcaje, 
                                :TipoVehiculo, 
                                :Observaciones, 
                                :Usuario, 
                                :Notificar
                            )";
                
                $stmtSalida = $Conexion->prepare($sqlSalida);
                $stmtSalida->bindParam(':IdPer', $movimiento['IdPer'], PDO::PARAM_STR);
                $stmtSalida->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
                $stmtSalida->bindParam(':DispN', $DispN, PDO::PARAM_STR);
                $stmtSalida->bindParam(':TiempoMarcaje', $horaSalida, PDO::PARAM_STR);
                $stmtSalida->bindParam(':TipoVehiculo', $TipoTransporte, PDO::PARAM_INT);
                $stmtSalida->bindParam(':Observaciones', $ObservacionesSalida, PDO::PARAM_STR);
                $stmtSalida->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
                $stmtSalida->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
                
                if (!$stmtSalida->execute()) {
                    throw new Exception('Error al registrar salida: ' . implode(', ', $stmtSalida->errorInfo()));
                }
                
                $IdMovSalida = $Conexion->lastInsertId();
                
                // Actualizar regentsalper
                $sqlActualizar = "UPDATE regentsalper SET 
                                  FolMovSal = :FolMovSal,
                                  FechaSalida = :FechaSalida,
                                  HoraSalida = :HoraSalida,
                                  Tiempo = :Tiempo,
                                  TiempoTotalMinutos = :TiempoTotalMinutos,
                                  TiempoTotalHoras = :TiempoTotalHoras,
                                  StatusRegistro = 0
                                  WHERE IdMovEnTSal = :IdMovEnTSal";
                
                $stmtActualizar = $Conexion->prepare($sqlActualizar);
                $stmtActualizar->bindParam(':FolMovSal', $IdMovSalida, PDO::PARAM_INT);
                $stmtActualizar->bindParam(':FechaSalida', $fechaActual, PDO::PARAM_STR);
                $stmtActualizar->bindParam(':HoraSalida', $horaSalida, PDO::PARAM_STR);
                $stmtActualizar->bindParam(':Tiempo', $tiempoFormateado, PDO::PARAM_STR);
                $stmtActualizar->bindParam(':TiempoTotalMinutos', $minutosTotales, PDO::PARAM_INT);
                $stmtActualizar->bindParam(':TiempoTotalHoras', $horasTotales, PDO::PARAM_INT);
                $stmtActualizar->bindParam(':IdMovEnTSal', $IdMovEnTSal, PDO::PARAM_INT);
                
                if (!$stmtActualizar->execute()) {
                    throw new Exception('Error al actualizar movimiento: ' . implode(', ', $stmtActualizar->errorInfo()));
                }
                
                $salidaRegistrada = true;
            }
        }
        
        // 2. Registrar la nueva ENTRADA en regentper
        $fechaActual = date('Y-m-d');
        $horaActual = date('H:i:s');
        
        
        // Insertar en regentper
        $sqlEntrada = "INSERT INTO regentper (
                        IdPer,
                        Ubicacion,
                        DispN,
                        Fecha,
                        TiempoMarcaje,
                        TipoVehiculo,
                        Observaciones,
                        Usuario,
                        Notificar" . 
                        ($IdFolEnt ? ", IdFolEnt" : "") . "
                    ) VALUES (
                        :IdPer, 
                        :Ubicacion, 
                        :DispN, 
                        GETDATE(), 
                        :TiempoMarcaje, 
                        :TipoVehiculo, 
                        :Observaciones, 
                        :Usuario, 
                        :Notificar" .
                        ($IdFolEnt ? ", :IdFolEnt" : "") . "
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
        
        if ($IdFolEnt) {
            $stmtEntrada->bindParam(':IdFolEnt', $IdFolEnt, PDO::PARAM_INT);
        }
        
        if (!$stmtEntrada->execute()) {
            throw new Exception('Error al registrar entrada: ' . implode(', ', $stmtEntrada->errorInfo()));
        }
        
        $IdMov = $Conexion->lastInsertId();
        
        // 3. Registrar en regentsalper
        $sqlRegentSalPer = "INSERT INTO regentsalper (
                            IdPer,
                            IdUbicacion,
                            FolMovEnt,
                            FechaEntrada,
                            StatusRegistro
                        ) VALUES (
                            :IdPer, 
                            :IdUbicacion, 
                            :FolMovEnt, 
                            :FechaEntrada, 
                            1
                        )";
        
        $stmtRegentSalPer = $Conexion->prepare($sqlRegentSalPer);
        $stmtRegentSalPer->bindParam(':IdPer', $IdPersonal, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':IdUbicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':FolMovEnt', $IdMov, PDO::PARAM_INT);
        $stmtRegentSalPer->bindParam(':FechaEntrada', $fechaActual, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':HoraEntrada', $horaActual, PDO::PARAM_STR);
        
        if (!$stmtRegentSalPer->execute()) {
            throw new Exception('Error al registrar en regentsalper: ' . implode(', ', $stmtRegentSalPer->errorInfo()));
        }
        
        $IdEntSal = $Conexion->lastInsertId();
        
        
        $Conexion->commit();
        
        $response['success'] = true;
        $response['message'] = $salidaRegistrada ? 
            'Salida registrada y nueva entrada procesada correctamente' : 
            'Acceso registrado correctamente';
        
        $response['data'] = [
            'idMovimiento' => $IdMov,
            'IdEntSal' => $IdEntSal,
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
                'hora_salida' => $HoraSalidaManual
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