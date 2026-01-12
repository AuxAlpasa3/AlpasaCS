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

    $requiredFields = ['IdPersonal', 'IdUsuario', 'TipoTransporte', 'Ubicacion', 'DispN'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $DispN = $input['DispN'];
    $IdPersonal = $input['IdPersonal'];
    $IdUsuario = $input['IdUsuario'];
    $Ubicacion = $input['Ubicacion'];
    $TipoTransporte = $input['TipoTransporte'];
    $usuario = $input['IdUsuario'];

    $IdVehiculoTransporte = isset($input['IdVehiculoTransporte']) ? $input['IdVehiculoTransporte'] : '';
    $Observaciones = isset($input['Observaciones']) && $input['Observaciones'] !== 'NULL' ? $input['Observaciones'] : '';
    $NotificarSupervisor = isset($input['NotificarSupervisor']) ? (bool)$input['NotificarSupervisor'] : false;

    // Consultar información del personal (usando PDO)
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
    
    // Iniciar transacción
    $Conexion->beginTransaction();
    
    try {
        // Obtener fecha y hora actual
        $fechaActual = date('Y-m-d');
        $horaActual = date('H:i:s');

        // Insertar en regentper
        $sqlRegentPer = "INSERT INTO regentper (
                            IdPer,
                            Ubicacion,
                            DispN,
                            Fecha,
                            TiempoMarcaje,
                            Observaciones,
                            Usuario,
                            Notificar
                        ) VALUES (:IdPer, :Ubicacion, :DispN, GETDATE(), GETDATE(), :Observaciones, :Usuario, :Notificar)";
        
        $stmtRegentPer = $Conexion->prepare($sqlRegentPer);
        $stmtRegentPer->bindParam(':IdPer', $IdPersonal, PDO::PARAM_STR);
        $stmtRegentPer->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtRegentPer->bindParam(':DispN', $DispN, PDO::PARAM_STR);
        $stmtRegentPer->bindParam(':Observaciones', $Observaciones, PDO::PARAM_STR);
        $stmtRegentPer->bindParam(':Usuario', $usuario, PDO::PARAM_STR);
        $stmtRegentPer->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
        
        if (!$stmtRegentPer->execute()) {
            throw new Exception('Error al registrar entrada: ' . implode(', ', $stmtRegentPer->errorInfo()));
        }
        
        $IdMov = $Conexion->lastInsertId();
        
        // Insertar en regentsalper
        $sqlRegentSalPer = "INSERT INTO regentsalper (
                                IdPer,
                                IdUbicacion,
                                FolMovEnt,
                                FechaEntrada,
                                StatusRegistro
                            ) VALUES (:IdPer, :IdUbicacion, :FolMovEnt, GETDATE(), 1)";
        
        $stmtRegentSalPer = $Conexion->prepare($sqlRegentSalPer);
        $stmtRegentSalPer->bindParam(':IdPer', $IdPersonal, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':IdUbicacion', $Ubicacion, PDO::PARAM_STR);
        $stmtRegentSalPer->bindParam(':FolMovEnt', $IdMov, PDO::PARAM_INT);
        
        if (!$stmtRegentSalPer->execute()) {
            throw new Exception('Error al registrar entrada en salper: ' . implode(', ', $stmtRegentSalPer->errorInfo()));
        }
        
        $IdEntSal = $Conexion->lastInsertId();
        
        // Procesar fotos
        $fotosProcesadas = [];
        if (isset($input['fotos']) && is_array($input['fotos']) && count($input['fotos']) > 0) {
            $sqlFotoEncabezado = "INSERT INTO T_fotografia_Encabezado (
                                    IdEntSal,
                                    FechaIngreso,
                                    Tipo,
                                    TipoMov,
                                    IdUsuario,
                                    Estatus
                                ) VALUES (:IdEntSal, GETDATE(), 'Personal', 1, :IdUsuario, 1)";
            
            $stmtFotoEncabezado = $Conexion->prepare($sqlFotoEncabezado);
            $stmtFotoEncabezado->bindParam(':IdEntSal', $IdEntSal, PDO::PARAM_INT);
            $stmtFotoEncabezado->bindParam(':IdUsuario', $IdUsuario, PDO::PARAM_STR);
            
            if (!$stmtFotoEncabezado->execute()) {
                throw new Exception('Error al crear encabezado de fotos: ' . implode(', ', $stmtFotoEncabezado->errorInfo()));
            }
            
            $idFotografias = $Conexion->lastInsertId();
            
            $fotos = $input['fotos'];
            $nextIdFoto = 1; 
            $directorioFotos = '../fotos_accesos/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
            if (!file_exists($directorioFotos)) {
                mkdir($directorioFotos, 0777, true);
            }
            
            foreach ($fotos as $index => $fotoData) {
                if (isset($fotoData['base64']) && isset($fotoData['nombre'])) {
                    $base64Data = $fotoData['base64'];
                    // Eliminar el encabezado base64 si existe
                    $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
                    $nombreFoto = $fotoData['nombre'] . '.jpg';
                    
                    $imagenDecodificada = base64_decode($base64Data);
                    
                    if ($imagenDecodificada === false) {
                        error_log("Error decodificando foto $index para acceso $IdEntSal");
                        continue;
                    }
                    
                    $rutaCompleta = $directorioFotos . $nombreFoto;
                    
                    if (file_put_contents($rutaCompleta, $imagenDecodificada)) {
                        $rutaRelativa = 'fotos_accesos/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $nombreFoto;
                        
                        $sqlFotoDetalle = "INSERT INTO T_fotografia_Detalle (
                                            IdEntSal,
                                            IdFotografiaRef,
                                            NombreFoto,
                                            RutaFoto,
                                            NextIdFoto
                                        ) VALUES (:IdEntSal, :IdFotografiaRef, :NombreFoto, :RutaFoto, :NextIdFoto)";
                        
                        $stmtFotoDetalle = $Conexion->prepare($sqlFotoDetalle);
                        $stmtFotoDetalle->bindParam(':IdEntSal', $IdEntSal, PDO::PARAM_INT);
                        $stmtFotoDetalle->bindParam(':IdFotografiaRef', $idFotografias, PDO::PARAM_INT);
                        $stmtFotoDetalle->bindParam(':NombreFoto', $nombreFoto, PDO::PARAM_STR);
                        $stmtFotoDetalle->bindParam(':RutaFoto', $rutaRelativa, PDO::PARAM_STR);
                        $stmtFotoDetalle->bindParam(':NextIdFoto', $nextIdFoto, PDO::PARAM_INT);
                        
                        if ($stmtFotoDetalle->execute()) {
                            $fotosProcesadas[] = $nombreFoto;
                            $nextIdFoto++; 
                        }
                    }
                }
            }
        }

        // Registrar vehículo si es necesario
        if ($TipoTransporte == 2 && !empty($IdVehiculoTransporte)) {
            $sqlRegentVeh = "INSERT INTO regentveh (
                                IdVeh,
                                Ubicacion,
                                DispN,
                                Fecha,
                                TiempoMarcaje,
                                Observaciones,
                                Usuario,
                                Notificar
                            ) VALUES (:IdVeh, :Ubicacion, :DispN, GETDATE(), GETDATE(), :Observaciones, :Usuario, :Notificar)";
            
            $stmtRegentVeh = $Conexion->prepare($sqlRegentVeh);
            $stmtRegentVeh->bindParam(':IdVeh', $IdVehiculoTransporte, PDO::PARAM_STR);
            $stmtRegentVeh->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
            $stmtRegentVeh->bindParam(':DispN', $DispN, PDO::PARAM_STR);
            $stmtRegentVeh->bindParam(':Observaciones', $Observaciones, PDO::PARAM_STR);
            $stmtRegentVeh->bindParam(':Usuario', $usuario, PDO::PARAM_STR);
            $stmtRegentVeh->bindParam(':Notificar', $NotificarSupervisor, PDO::PARAM_INT);
            
            if (!$stmtRegentVeh->execute()) {
                throw new Exception('Error al registrar entrada de vehiculo: ' . implode(', ', $stmtRegentVeh->errorInfo()));
            }
            
            $IdMovVehiculo = $Conexion->lastInsertId();
        }

        // Notificar al supervisor si es necesario
        if ($NotificarSupervisor) {
            $sqlSupervisor = "SELECT 
                                CONCAT(Nombre,' ',ApPaterno,' ',ApMaterno) as NombreCompleto,
                                Email, Contacto
                                FROM t_personal 
                                WHERE IdPersonal = 
                                (SELECT IdSupervisor FROM t_personal WHERE IdPersonal = :IdPersonal)"; 
                              
            $stmtSupervisor = $Conexion->prepare($sqlSupervisor);
            $stmtSupervisor->bindParam(':IdPersonal', $IdPersonal, PDO::PARAM_STR);
            $stmtSupervisor->execute();
            
            if ($stmtSupervisor->rowCount() > 0) {
                $supervisor = $stmtSupervisor->fetch(PDO::FETCH_ASSOC);
                
                $asunto = "Registro de Entrada - " . $personal['NombreCompleto'];
                $mensaje = "
                    <h2>Registro de Entrada de Personal</h2>
                    <p><strong>Empleado:</strong> " . $personal['NombreCompleto'] . "</p>
                    <p><strong>Número de Empleado:</strong> " . $personal['NoEmpleado'] . "</p>
                    <p><strong>Empresa:</strong> " . $personal['NomEmpresa'] . "</p>
                    <p><strong>Departamento:</strong> " . $personal['NomDepto'] . "</p>
                    <p><strong>Cargo:</strong> " . $personal['NomCargo'] . "</p>
                    <p><strong>Ubicación:</strong> " . $Ubicacion . "</p>
                    <p><strong>Observaciones:</strong> " . $Observaciones . "</p>
                    <p><strong>Fecha:</strong> " . $fechaActual . "</p>
                    <p><strong>Hora de Entrada:</strong> " . $horaActual . "</p>
                    <p><strong>Registrado por:</strong> " . $usuario . "</p>
                    <p><strong>DispN:</strong> " . $DispN . "</p>
                ";
                
                if (!empty($fotosProcesadas)) {
                    $mensaje .= "<p><strong>Fotos tomadas:</strong> " . implode(', ', $fotosProcesadas) . "</p>";
                }
                
                // Envío de correo (implementar según necesidades)
                // enviarCorreo($supervisor['Email'], $asunto, $mensaje);
            }
        }
        
        // Confirmar transacción
        $Conexion->commit();
        
        $response['success'] = true;
        $response['message'] = 'Acceso registrado correctamente';
        $response['data'] = [
            'folioMov' => $IdMov,
            'IdMov' => $IdMov,
            'IdEntSal' => $IdEntSal,
            'fecha' => $fechaActual,
            'hora' => $horaActual,
            'personal' => [
                'nombre' => $personal['NombreCompleto'],
                'noEmpleado' => $personal['NoEmpleado'],
                'empresa' => $personal['NomEmpresa']
            ],
            'ubicacion' => $Ubicacion,
            'fotos_procesadas' => $fotosProcesadas,
            'notificacion_supervisor' => $NotificarSupervisor
        ];
        
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

function enviarCorreo($destinatario, $asunto, $mensaje) {
    // Implementación de envío de correo
    /*
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: sistema@empresa.com' . "\r\n";
    
    return mail($destinatario, $asunto, $mensaje, $headers);
    */
    return true;
}
?>