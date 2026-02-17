<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../api/db/conexion.php';

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

    error_log('Payload para registrar salida: ' . json_encode($input));

    $requiredFields = ['IdPersonal', 'IdUsuario', 'Ubicacion'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $IdPersonal = $input['IdPersonal'];
    $IdUsuario = $input['IdUsuario'];
    $Ubicacion = $input['Ubicacion'];
    
    // Manejar IdVehiculo - asegurar que sea 0 si no hay vehículo
    $IdVehiculo = isset($input['IdVehiculo']) ? (int)$input['IdVehiculo'] : 0;
    
    $IdRegistroIngreso = isset($input['IdRegistroIngreso']) ? (int)$input['IdRegistroIngreso'] : null;
    $DispN = isset($input['DispN']) ? $input['DispN'] : 'APP_MOVIL';
    $Observaciones = isset($input['Observaciones']) && $input['Observaciones'] !== 'NULL' ? $input['Observaciones'] : 'Salida registrada desde móvil';
    
    $NotificarSupervisor = isset($input['NotificarSupervisor']) ? (int)$input['NotificarSupervisor'] : 0;
    if ($NotificarSupervisor > 1) $NotificarSupervisor = 1;
    if ($NotificarSupervisor < 0) $NotificarSupervisor = 0;
    
    $TipoTransporte = isset($input['TipoTransporte']) ? (int)$input['TipoTransporte'] : 0;
    $LibreUso = isset($input['LibreUso']) ? (int)$input['LibreUso'] : 0;
    
    $FechaSalida = isset($input['FechaSalida']) ? $input['FechaSalida'] : date('Y-m-d');
    $HoraSalida = isset($input['HoraSalida']) ? $input['HoraSalida'] : date('H:i:s');
    
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $FechaSalida)) {
        throw new Exception("Formato de fecha de salida inválido. Debe ser YYYY-MM-DD");
    }
    
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $HoraSalida)) {
        if (preg_match('/^\d{2}:\d{2}$/', $HoraSalida)) {
            $HoraSalida .= ':00';
        } else {
            throw new Exception("Formato de hora de salida inválido. Debe ser HH:MM:SS");
        }
    }
    
    // IMPORTANTE: Crear fecha completa de salida
    $fechaHoraSalidaCompleta = $FechaSalida . ' ' . $HoraSalida;
    
    // Verificar si hay una entrada activa del personal
    $sqlVerificarEntrada = "SELECT 
                                t1.FolMov as FolMovEnt,
                                t1.IdPer,
                                t1.Ubicacion as UbicacionEntrada,
                                t1.Fecha as FechaEntrada,
                                t1.TiempoMarcaje as HoraEntrada,
                                -- Construir fecha completa de entrada (asumiendo que Fecha ya incluye la hora o es solo fecha)
                                CASE 
                                    WHEN t1.Fecha LIKE '%:%' THEN t1.Fecha
                                    ELSE CONVERT(VARCHAR, t1.Fecha, 120) + ' ' + ISNULL(t1.TiempoMarcaje, '00:00:00')
                                END as FechaHoraEntradaCompleta,
                                t1.TipoVehiculo,
                                COALESCE(t1.IdVeh, 0) as IdVeh,
                                COALESCE(t2.tieneVehiculo, 0) as tieneVehiculo,
                                t2.IdMovEnTSal
                            FROM regentper t1
                            LEFT JOIN regentsalper t2 ON t1.FolMov = t2.FolMovEnt
                            WHERE t1.IdPer = :IdPersonal 
                            AND (t2.StatusRegistro IS NULL OR t2.StatusRegistro = 1)
                            ORDER BY t1.Fecha DESC, t1.TiempoMarcaje DESC";
    
    $stmtVerificarEntrada = $Conexion->prepare($sqlVerificarEntrada);
    $stmtVerificarEntrada->bindParam(':IdPersonal', $IdPersonal, PDO::PARAM_STR);
    $stmtVerificarEntrada->execute();
    
    $entradaActiva = $stmtVerificarEntrada->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($entradaActiva) === 0) {
        throw new Exception('No se encontró una entrada activa para este personal');
    }
    
    $entrada = $entradaActiva[0];
    $FolMovEntActual = (int)$entrada['FolMovEnt'];
    $tieneVehiculo = (int)$entrada['tieneVehiculo'];
    $IdVeh = (int)$entrada['IdVeh'];
    $IdMovEnTSal = $entrada['IdMovEnTSal'] ? (int)$entrada['IdMovEnTSal'] : null;
    $TipoVehiculoEntrada = (int)$entrada['TipoVehiculo'];
    $UbicacionEntrada = $entrada['UbicacionEntrada'];
    
    // Obtener la fecha completa de entrada
    $fechaHoraEntradaCompleta = $entrada['FechaHoraEntradaCompleta'];
    
    error_log("Entrada activa encontrada: FolMovEnt=$FolMovEntActual, tieneVehiculo=$tieneVehiculo, IdVeh=$IdVeh");
    error_log("Fecha/Hora Entrada completa: $fechaHoraEntradaCompleta");
    error_log("Fecha/Hora Salida completa: $fechaHoraSalidaCompleta");
    
    // Si el TipoTransporte es 0, asegurar que IdVeh sea 0
    if ($TipoTransporte == 0) {
        $IdVeh = 0;
        $tieneVehiculo = 0;
    }
    
    // ============ CÁLCULO CORREGIDO DEL TIEMPO DE ESTANCIA ============
    // Convertir a timestamp para cálculo preciso
    $timestampEntrada = strtotime($fechaHoraEntradaCompleta);
    $timestampSalida = strtotime($fechaHoraSalidaCompleta);
    
    if ($timestampEntrada === false || $timestampSalida === false) {
        throw new Exception("Error al convertir las fechas a timestamp");
    }
    
    error_log("Timestamp Entrada: $timestampEntrada");
    error_log("Timestamp Salida: $timestampSalida");
    
    // Si la hora de salida es menor que la de entrada, asumimos que pasó la medianoche
    if ($timestampSalida < $timestampEntrada) {
        $timestampSalida = strtotime('+1 day', $timestampSalida);
        error_log("Se detectó cruce de medianoche, ajustando fecha de salida");
    }
    
    $diferenciaSegundos = $timestampSalida - $timestampEntrada;
    $minutosTotales = floor($diferenciaSegundos / 60);
    
    if ($minutosTotales < 0) {
        $minutosTotales = 0;
    }
    
    $horas = floor($minutosTotales / 60);
    $minutos = $minutosTotales % 60;
    
    // Formato HH:MM (solo horas y minutos)
    $tiempoFormateado = sprintf("%02d:%02d", $horas, $minutos);
    
    error_log("Cálculo de tiempo:");
    error_log("Diferencia en segundos: $diferenciaSegundos");
    error_log("Minutos totales: $minutosTotales");
    error_log("Tiempo formateado (HH:MM): $tiempoFormateado");
    // ============ FIN CÁLCULO CORREGIDO ============
    
    $Conexion->beginTransaction();
    
    try {
        // Registrar salida del personal - Usar fechaHoraSalidaCompleta
        $sqlSalidaPersonal = "INSERT INTO regsalper (
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
        
        $stmtSalidaPersonal = $Conexion->prepare($sqlSalidaPersonal);
        $stmtSalidaPersonal->bindParam(':IdPer', $IdPersonal, PDO::PARAM_STR);
        $stmtSalidaPersonal->bindParam(':IdFolEnt', $FolMovEntActual, PDO::PARAM_INT);
        $stmtSalidaPersonal->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtSalidaPersonal->bindParam(':DispN', $DispN, PDO::PARAM_STR);
        $stmtSalidaPersonal->bindParam(':FechaSalida', $fechaHoraSalidaCompleta, PDO::PARAM_STR);
        $stmtSalidaPersonal->bindParam(':TiempoMarcaje', $HoraSalida, PDO::PARAM_STR);
        $stmtSalidaPersonal->bindParam(':TipoVehiculo', $TipoVehiculoEntrada, PDO::PARAM_INT);
        $stmtSalidaPersonal->bindParam(':Observaciones', $Observaciones, PDO::PARAM_STR);
        $stmtSalidaPersonal->bindParam(':Usuario', $IdUsuario, PDO::PARAM_STR);
        $stmtSalidaPersonal->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
        $stmtSalidaPersonal->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
        
        if (!$stmtSalidaPersonal->execute()) {
            $errorInfo = $stmtSalidaPersonal->errorInfo();
            throw new Exception('Error al registrar salida de personal: ' . ($errorInfo[2] ?? 'Error desconocido'));
        }

        $IdMovSalidaPersonal = (int)$Conexion->lastInsertId();
        error_log("Salida personal registrada ID: $IdMovSalidaPersonal");

        if ($IdMovSalidaPersonal <= 0) {
            throw new Exception("No se pudo obtener el ID de la inserción");
        }
        
        // Actualizar/Insertar en regentsalper con el tiempo calculado correctamente
        if ($IdMovEnTSal) {
            $sqlActualizarRegentSalPer = "UPDATE regentsalper SET 
                                          FolMovSal = :FolMovSal,
                                          FechaSalida = :FechaSalida,
                                          Tiempo = :Tiempo,
                                          StatusRegistro = 2
                                          WHERE IdMovEnTSal = :IdMovEnTSal";
            
            $stmtActualizarRegentSalPer = $Conexion->prepare($sqlActualizarRegentSalPer);
            $stmtActualizarRegentSalPer->bindParam(':FolMovSal', $IdMovSalidaPersonal, PDO::PARAM_INT);
            $stmtActualizarRegentSalPer->bindParam(':FechaSalida', $fechaHoraSalidaCompleta, PDO::PARAM_STR);
            $stmtActualizarRegentSalPer->bindParam(':Tiempo', $tiempoFormateado, PDO::PARAM_STR);
            $stmtActualizarRegentSalPer->bindParam(':IdMovEnTSal', $IdMovEnTSal, PDO::PARAM_INT);
            
            if (!$stmtActualizarRegentSalPer->execute()) {
                $errorInfo = $stmtActualizarRegentSalPer->errorInfo();
                throw new Exception('Error al actualizar regentsalper: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
        } else {
            // Si no hay IdMovEnTSal, insertar en regentsalper
            $sqlInsertRegentSalPer = "INSERT INTO regentsalper (
                                        IdPer,
                                        IdUbicacion,
                                        FolMovEnt,
                                        FolMovSal,
                                        FechaEntrada,
                                        FechaSalida,
                                        Tiempo,
                                        StatusRegistro,
                                        tieneVehiculo
                                    ) VALUES (
                                        :IdPer,
                                        :IdUbicacion,
                                        :FolMovEnt,
                                        :FolMovSal,
                                        :FechaEntrada,
                                        :FechaSalida,
                                        :Tiempo,
                                        2,
                                        :tieneVehiculo
                                    )";
            
            $stmtInsertRegentSalPer = $Conexion->prepare($sqlInsertRegentSalPer);
            $stmtInsertRegentSalPer->bindParam(':IdPer', $IdPersonal, PDO::PARAM_STR);
            $stmtInsertRegentSalPer->bindParam(':IdUbicacion', $UbicacionEntrada, PDO::PARAM_STR);
            $stmtInsertRegentSalPer->bindParam(':FolMovEnt', $FolMovEntActual, PDO::PARAM_INT);
            $stmtInsertRegentSalPer->bindParam(':FolMovSal', $IdMovSalidaPersonal, PDO::PARAM_INT);
            $stmtInsertRegentSalPer->bindParam(':FechaEntrada', $fechaHoraEntradaCompleta, PDO::PARAM_STR);
            $stmtInsertRegentSalPer->bindParam(':FechaSalida', $fechaHoraSalidaCompleta, PDO::PARAM_STR);
            $stmtInsertRegentSalPer->bindParam(':Tiempo', $tiempoFormateado, PDO::PARAM_STR);
            $stmtInsertRegentSalPer->bindParam(':tieneVehiculo', $tieneVehiculo, PDO::PARAM_INT);
            
            if (!$stmtInsertRegentSalPer->execute()) {
                $errorInfo = $stmtInsertRegentSalPer->errorInfo();
                throw new Exception('Error al insertar en regentsalper: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }
        }
        
        // Procesar salida de vehículo si aplica (solo si IdVeh > 0)
        $IdMovSalidaVehiculo = null;
        $tiempoFormateadoVehiculo = null;
        $FolMovEntVeh = null;
        
        if ($tieneVehiculo == 1 && $IdVeh > 0) {
            error_log("Procesando salida de vehículo ID: $IdVeh");
            
            // Verificar si hay entrada activa del vehículo
            $sqlVerificarEntradaVehiculo = "SELECT 
                                                t1.FolMov as FolMovEntVeh,
                                                t1.Ubicacion as UbicacionEntrada,
                                                t1.Fecha as FechaEntrada,
                                                t1.TiempoMarcaje as HoraEntrada,
                                                -- Construir fecha completa de entrada del vehículo
                                                CASE 
                                                    WHEN t1.Fecha LIKE '%:%' THEN t1.Fecha
                                                    ELSE CONVERT(VARCHAR, t1.Fecha, 120) + ' ' + ISNULL(t1.TiempoMarcaje, '00:00:00')
                                                END as FechaHoraEntradaCompleta,
                                                t1.TipoVehiculo,
                                                t2.FolMovEnt,
                                                t2.FolMovSal,
                                                t2.StatusRegistro
                                            FROM regentveh t1
                                            LEFT JOIN regentsalveh t2 ON t1.FolMov = t2.FolMovEnt
                                            WHERE t1.IdVeh = :IdVeh 
                                            AND (t2.StatusRegistro IS NULL OR t2.StatusRegistro = 1)
                                            ORDER BY t1.Fecha DESC, t1.TiempoMarcaje DESC";
            
            $stmtVerificarEntradaVehiculo = $Conexion->prepare($sqlVerificarEntradaVehiculo);
            $stmtVerificarEntradaVehiculo->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
            $stmtVerificarEntradaVehiculo->execute();
            
            $entradaVehiculoActiva = $stmtVerificarEntradaVehiculo->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($entradaVehiculoActiva) > 0) {
                $entradaVeh = $entradaVehiculoActiva[0];
                $FolMovEntVeh = (int)$entradaVeh['FolMovEntVeh'];
                $StatusRegistroVeh = isset($entradaVeh['StatusRegistro']) ? (int)$entradaVeh['StatusRegistro'] : null;
                $TipoVehiculoVeh = (int)$entradaVeh['TipoVehiculo'];
                $UbicacionEntradaVeh = $entradaVeh['UbicacionEntrada'];
                $fechaHoraEntradaVehCompleta = $entradaVeh['FechaHoraEntradaCompleta'];
                
                error_log("Entrada activa vehículo encontrada: FolMovEntVeh=$FolMovEntVeh");
                
                // Calcular tiempo de estancia del vehículo
                $timestampEntradaVeh = strtotime($fechaHoraEntradaVehCompleta);
                $timestampSalidaVeh = strtotime($fechaHoraSalidaCompleta);
                
                if ($timestampSalidaVeh < $timestampEntradaVeh) {
                    $timestampSalidaVeh = strtotime('+1 day', $timestampSalidaVeh);
                }
                
                $diferenciaSegundosVeh = $timestampSalidaVeh - $timestampEntradaVeh;
                $minutosTotalesVeh = floor($diferenciaSegundosVeh / 60);
                
                if ($minutosTotalesVeh < 0) {
                    $minutosTotalesVeh = 0;
                }
                
                $horasVeh = floor($minutosTotalesVeh / 60);
                $minutosVeh = $minutosTotalesVeh % 60;
                
                $tiempoFormateadoVehiculo = sprintf("%02d:%02d", $horasVeh, $minutosVeh);
                
                error_log("Tiempo vehículo: $tiempoFormateadoVehiculo");
                
                // Registrar salida del vehículo
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
                
                $observacionesSalidaVehiculo = "Salida de vehículo registrada desde móvil";
                
                $stmtSalidaVeh = $Conexion->prepare($sqlSalidaVehiculo);
                $stmtSalidaVeh->bindParam(':IdVeh', $IdVeh, PDO::PARAM_INT);
                $stmtSalidaVeh->bindParam(':IdFolEnt', $FolMovEntVeh, PDO::PARAM_INT);
                $stmtSalidaVeh->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
                $stmtSalidaVeh->bindParam(':DispN', $DispN, PDO::PARAM_STR);
                $stmtSalidaVeh->bindParam(':FechaSalida', $fechaHoraSalidaCompleta, PDO::PARAM_STR);
                $stmtSalidaVeh->bindParam(':TiempoMarcaje', $HoraSalida, PDO::PARAM_STR);
                $stmtSalidaVeh->bindParam(':TipoVehiculo', $TipoVehiculoVeh, PDO::PARAM_INT);
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
                
                // Actualizar regentsalveh
                $sqlUpdateRegentsalveh = "UPDATE regentsalveh SET 
                                        FolMovSal = :FolMovSal,
                                        FechaSalida = :FechaSalida,
                                        Tiempo = :Tiempo,
                                        StatusRegistro = 2
                                        WHERE FolMovEnt = :FolMovEnt";
                
                $stmtUpdateRegentsalveh = $Conexion->prepare($sqlUpdateRegentsalveh);
                $stmtUpdateRegentsalveh->bindParam(':FolMovSal', $IdMovSalidaVehiculo, PDO::PARAM_INT);
                $stmtUpdateRegentsalveh->bindParam(':FechaSalida', $fechaHoraSalidaCompleta, PDO::PARAM_STR);
                $stmtUpdateRegentsalveh->bindParam(':Tiempo', $tiempoFormateadoVehiculo, PDO::PARAM_STR);
                $stmtUpdateRegentsalveh->bindParam(':FolMovEnt', $FolMovEntVeh, PDO::PARAM_INT);
                
                if (!$stmtUpdateRegentsalveh->execute()) {
                    $errorInfo = $stmtUpdateRegentsalveh->errorInfo();
                    throw new Exception('Error al actualizar regentsalveh: ' . ($errorInfo[2] ?? 'Error desconocido'));
                }
                
                error_log("Salida vehículo registrada exitosamente");
            } else {
                error_log("No se encontró entrada activa para vehículo $IdVeh");
            }
        }
        
        $Conexion->commit();
        error_log("Salida completada exitosamente");
        
        // Obtener información del personal para la respuesta
        $sqlPersonal = "SELECT 
                            t1.IdPersonal,
                            t1.NoEmpleado,
                            CONCAT(t1.Nombre,' ',t1.ApPaterno,' ',t1.ApMaterno) as NombreCompleto
                        FROM t_personal t1
                        WHERE t1.IdPersonal = :IdPersonal";
        
        $stmtPersonal = $Conexion->prepare($sqlPersonal);
        $stmtPersonal->bindParam(':IdPersonal', $IdPersonal, PDO::PARAM_STR);
        $stmtPersonal->execute();
        
        $personalResult = $stmtPersonal->fetchAll(PDO::FETCH_ASSOC);
        $personal = count($personalResult) > 0 ? $personalResult[0] : ['NombreCompleto' => 'Desconocido', 'NoEmpleado' => ''];
        
        $response['success'] = true;
        $response['message'] = '✅ Salida registrada correctamente';
        
        $response['data'] = [
            'FolMovSalida' => $IdMovSalidaPersonal,
            'fecha_salida' => $FechaSalida,
            'hora_salida' => $HoraSalida,
            'folio_entrada' => $FolMovEntActual,
            'tiempo_estancia' => $tiempoFormateado,
            'personal' => [
                'nombre' => $personal['NombreCompleto'],
                'noEmpleado' => $personal['NoEmpleado']
            ]
        ];
        
        if ($IdMovSalidaVehiculo) {
            $response['data']['vehiculo'] = [
                'id_vehiculo' => $IdVeh,
                'folio_salida_vehiculo' => $IdMovSalidaVehiculo,
                'folio_entrada_vehiculo' => $FolMovEntVeh,
                'tiempo_estancia_vehiculo' => $tiempoFormateadoVehiculo
            ];
        }
        
    } catch (Exception $e) {
        $Conexion->rollBack();
        error_log("Error en transacción de salida: " . $e->getMessage());
        throw $e;
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log('Error en registrarSalida: ' . $e->getMessage());
    http_response_code(400);
} finally {
    $Conexion = null;
    error_log("Respuesta salida enviada: " . json_encode($response));
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>