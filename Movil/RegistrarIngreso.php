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

    $requiredFields = ['IdPersonal', 'Ubicacion', 'NombreUbicacion', 'Usuario', 'IdUsuario', 'DispN', 'TipoTransporte'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $idPersonal = $input['IdPersonal'];
    $idUbicacion = $input['Ubicacion'];
    $nombreUbicacion = $input['NombreUbicacion'];
    $usuario = $input['Usuario'];
    $idUsuario = $input['IdUsuario'];
    $dispositivo = $input['DispN'];
    $observaciones = isset($input['Observaciones']) && $input['Observaciones'] !== 'NULL' ? $input['Observaciones'] : '';
    $notificarSupervisor = isset($input['NotificarSupervisor']) ? (bool)$input['NotificarSupervisor'] : false;
    $tipoTransporte = $input['TipoTransporte'];
    $idVehiculoTransporte = isset($input['IdVehiculoTransporte']) ? $input['IdVehiculoTransporte'] : '';
    
    $sqlPersonal = "SELECT 
                        t1.IdPer,
                        t1.NoEmpleado,
                        t1.NombreCompleto,
                        t1.Nombre,
                        t1.ApellidoPaterno,
                        t1.ApellidoMaterno,
                        t1.Email,
                        t2.NomCargo,
                        t3.NomDepto,
                        t4.NomEmpresa
                    FROM Personal t1
                    LEFT JOIN Cargos t2 ON t1.IdCargo = t2.IdCargo
                    LEFT JOIN Departamentos t3 ON t1.IdDepartamento = t3.IdDepartamento
                    LEFT JOIN Empresas t4 ON t1.IdEmpresa = t4.IdEmpresa
                    WHERE t1.IdPer = ?";
    
    $stmtPersonal = $Conexion->prepare($sqlPersonal);
    $stmtPersonal->bind_param("s", $idPersonal);
    $stmtPersonal->execute();
    $resultPersonal = $stmtPersonal->get_result();
    
    if ($resultPersonal->num_rows === 0) {
        throw new Exception('Personal no encontrado');
    }
    
    $personal = $resultPersonal->fetch_assoc();
    $stmtPersonal->close();
    
    $Conexion->begin_transaction();
    
    try {
        $folioMov = generarFolioMov($Conexion);
        $fechaActual = date('Y-m-d');
        $horaActual = date('H:i:s');
        
        $sqlRegentPer = "INSERT INTO regentper (
                            FolMov,
                            IdPer,
                            Ubicacion,
                            TipoMov,
                            DispN,
                            Fecha,
                            TiempoMarcaje,
                            Observaciones,
                            Usuario,
                            Notificar
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmtRegentPer = $Conexion->prepare($sqlRegentPer);
        $observacionesConTransporte = $observaciones . " | Transporte: " . obtenerDescripcionTransporte($tipoTransporte);
        
        $stmtRegentPer->bind_param(
            $folioMov,
            $idPersonal,
            $nombreUbicacion,
            1,
            $dispositivo,
            $fechaActual,
            $horaActual,
            $observacionesConTransporte,
            $usuario,
            $notificarSupervisor ? 'SI' : 'NO'
        );
        
        if (!$stmtRegentPer->execute()) {
            throw new Exception('Error al registrar entrada: ' . $stmtRegentPer->error);
        }
        
        $idMov = $stmtRegentPer->insert_id;
        $stmtRegentPer->close();
        
        $sqlRegentSalPer = "INSERT INTO regentsalper (
                                IdMov,
                                IdPer,
                                IdUbicacion,
                                FolMovEnt,
                                FechaEntrada,
                                StatusRegistro
                            ) VALUES (?, ?, ?, ?, GETDATE(), 1)";
        
        $stmtRegentSalPer = $Conexion->prepare($sqlRegentSalPer);
        $stmtRegentSalPer->bind_param(
            "ssss",
            $idMov,
            $idPersonal,
            $idUbicacion,
            $folioMov
        );
        
        if (!$stmtRegentSalPer->execute()) {
            throw new Exception('Error al registrar entrada en salper: ' . $stmtRegentSalPer->error);
        }
        
        $idEntSal = $stmtRegentSalPer->insert_id;
        $stmtRegentSalPer->close();
        
        $fotosProcesadas = [];
        if (isset($input['fotos']) && is_array($input['fotos']) && count($input['fotos']) > 0) {
            $sqlFotoEncabezado = "INSERT INTO T_fotografia_Encabezado (
                                    IdEntSal,
                                    FechaIngreso,
                                    Tipo,
                                    Ubicacion,
                                    Estatus
                                ) VALUES (?, GETDATE(), 'Entrada', ?, 'Activo')";
            
            $stmtFotoEncabezado = $Conexion->prepare($sqlFotoEncabezado);
            $stmtFotoEncabezado->bind_param("ss", $idEntSal, $nombreUbicacion);
            
            if (!$stmtFotoEncabezado->execute()) {
                throw new Exception('Error al crear encabezado de fotos: ' . $stmtFotoEncabezado->error);
            }
            
            $idFotografias = $stmtFotoEncabezado->insert_id;
            $stmtFotoEncabezado->close();
            
            $fotos = $input['fotos'];
            $nextIdFoto = 1; 
            $directorioFotos = '../fotos_accesos/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
            if (!file_exists($directorioFotos)) {
                mkdir($directorioFotos, 0777, true);
            }
            
            foreach ($fotos as $index => $fotoData) {
                if (isset($fotoData['base64']) && isset($fotoData['nombre'])) {
                    $base64Data = $fotoData['base64'];
                    $nombreFoto = $fotoData['nombre'] . '.jpg';
                    
                    // Decodificar base64
                    $imagenDecodificada = base64_decode($base64Data);
                    
                    if ($imagenDecodificada === false) {
                        error_log("Error decodificando foto $index para acceso $idEntSal");
                        continue;
                    }
                    
                    $rutaCompleta = $directorioFotos . $nombreFoto;
                    
                    // Guardar imagen en el servidor
                    if (file_put_contents($rutaCompleta, $imagenDecodificada)) {
                        $rutaRelativa = 'fotos_accesos/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $nombreFoto;
                        
                        $sqlFotoDetalle = "INSERT INTO T_fotografia_Detalle (
                                            IdFotoEntSal,
                                            IdFotografiaRef,
                                            NombreFoto,
                                            RutaFoto,
                                            NextIdFoto
                                        ) VALUES (?, ?, ?, ?, ?)";
                        
                        $stmtFotoDetalle = $Conexion->prepare($sqlFotoDetalle);
                        $stmtFotoDetalle->bind_param(
                            "iissi",
                            $idEntSal,
                            $idFotografias,
                            $nombreFoto,
                            $rutaRelativa,
                            $nextIdFoto
                        );
                        
                        if ($stmtFotoDetalle->execute()) {
                            $fotosProcesadas[] = $nombreFoto;
                            $nextIdFoto++; // Incrementar para la siguiente foto
                        }
                        
                        $stmtFotoDetalle->close();
                    }
                }
            }
        }
        
        if ($notificarSupervisor) {
            $sqlSupervisor = "SELECT 
                                Concat(Nombre,' ',ApPaterno,' ',ApMaterno) as NombreCompleto,
                                Email, Contacto
                                FROM t_personal where IdPersonal= ?"; 
                              
            $stmtSupervisor = $Conexion->prepare($sqlSupervisor);
            $stmtSupervisor->bind_param("s", $idPersonal);
            $stmtSupervisor->execute();
            $resultSupervisor = $stmtSupervisor->get_result();
            
            if ($resultSupervisor->num_rows > 0) {
                $supervisor = $resultSupervisor->fetch_assoc();
                
                // Enviar correo
                $asunto = "Registro de Entrada - " . $personal['NombreCompleto'];
                $mensaje = "
                    <h2>Registro de Entrada de Personal</h2>
                    <p><strong>Empleado:</strong> " . $personal['NombreCompleto'] . "</p>
                    <p><strong>Número de Empleado:</strong> " . $personal['NoEmpleado'] . "</p>
                    <p><strong>Empresa:</strong> " . $personal['NomEmpresa'] . "</p>
                    <p><strong>Departamento:</strong> " . $personal['NomDepto'] . "</p>
                    <p><strong>Cargo:</strong> " . $personal['NomCargo'] . "</p>
                    <p><strong>Ubicación:</strong> " . $nombreUbicacion . "</p>
                    <p><strong>Tipo de Transporte:</strong> " . obtenerDescripcionTransporteCompleta($tipoTransporte) . "</p>
                    <p><strong>Observaciones:</strong> " . $observaciones . "</p>
                    <p><strong>Fecha:</strong> " . $fechaActual . "</p>
                    <p><strong>Hora de Entrada:</strong> " . $horaActual . "</p>
                    <p><strong>Registrado por:</strong> " . $usuario . "</p>
                    <p><strong>Dispositivo:</strong> " . $dispositivo . "</p>
                ";
                
            }
            
            $stmtSupervisor->close();
        }
        
        $Conexion->commit();
        
        $response['success'] = true;
        $response['message'] = 'Acceso registrado correctamente';
        $response['data'] = [
            'folioMov' => $folioMov,
            'idMov' => $idMov,
            'idEntSal' => $idEntSal,
            'fecha' => $fechaActual,
            'hora' => $horaActual,
            'personal' => [
                'nombre' => $personal['NombreCompleto'],
                'noEmpleado' => $personal['NoEmpleado'],
                'empresa' => $personal['NomEmpresa']
            ],
            'ubicacion' => $nombreUbicacion,
            'tipoTransporte' => obtenerDescripcionTransporteCompleta($tipoTransporte),
            'fotosProcesadas' => count($fotosProcesadas),
            'notificacionEnviada' => $notificarSupervisor
        ];
        
    } catch (Exception $e) {
        $Conexion->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    http_response_code(400);
} finally {
    if (isset($Conexion) && $Conexion) {
        $Conexion->close();
    }
}

echo json_encode($response);

function generarFolioMov($Conexion) {
    $prefijo = 'ENT';
    $fecha = date('Ymd');
    
    $sql = "SELECT COUNT(*) as total FROM regentper WHERE DATE(Fecha) = CURDATE() AND FolMov LIKE 'ENT-%'";
    $result = $Conexion->query($sql);
    $row = $result->fetch_assoc();
    $secuencia = $row['total'] + 1;
    
    return sprintf('%s-%s-%04d', $prefijo, $fecha, $secuencia);
}



// Función para enviar correo (implementar según necesidades)
function enviarCorreo($destinatario, $asunto, $mensaje) {
    // Implementación de envío de correo
    // Puedes usar PHPMailer, mail() nativo, o un servicio de correo
    /*
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: sistema@empresa.com' . "\r\n";
    
    return mail($destinatario, $asunto, $mensaje, $headers);
    */
    return true;
}
?>