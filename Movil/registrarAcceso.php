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

    $requiredFields = ['IdPersonal', 'IdUsuario', 'TipoTransporte', 
    'Ubicacion', 'IdUsuario', 'DispN'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $DispN = $input['DispN'];
    $IdPersonal = $input['IdPersonal'];
    $IdUsuario = $input['IdUsuario'];
    $Ubicacion = $input['Ubicacion'];
    $TipoTransporte = $input['TipoTransporte'];

    $IdVehiculoTransporte = isset($input['IdVehiculoTransporte']) ? $input['IdVehiculoTransporte'] : '';
    $Observaciones = isset($input['Observaciones']) && $input['Observaciones'] !== 'NULL' ? $input['Observaciones'] : '';
    $NotificarSupervisor = isset($input['NotificarSupervisor']) ? (bool)$input['NotificarSupervisor'] : false;

    
    $sqlPersonal = "SELECT 
                        t1.IdPersonal,
                        t1.NoEmpleado,
                        concat(t1.Nombre,' ',t1.ApPaterno,' ',t1.ApMaterno) as NombreCompleto,
                        t1.Email,
                        t2.NomCargo,
                        t3.NomDepto,
                        t4.NomEmpresa
                    FROM t_personal t1
                    LEFT JOIN t_cargo t2 ON t1.Cargo = t2.IdCargo
                    LEFT JOIN t_departamento t3 ON t1.Departamento = t3.IdDepartamento
                    LEFT JOIN t_empresa t4 ON t1.Empresa = t4.IdEmpresa
                    WHERE t1.IdPersonal ?";
    
    $stmtPersonal = $Conexion->prepare($sqlPersonal);
    $stmtPersonal->bind_param("s", $IdPersonal);
    $stmtPersonal->execute();
    $resultPersonal = $stmtPersonal->get_result();
    
    if ($resultPersonal->num_rows === 0) {
        throw new Exception('Personal no encontrado');
    }
    
    $personal = $resultPersonal->fetch_assoc();
    $stmtPersonal->close();
    
    $Conexion->begin_transaction();
    
    try {

        $sqlRegentPer = "INSERT INTO regentper (
                            IdPer,
                            Ubicacion,
                            DispN,
                            Fecha,
                            TiempoMarcaje,
                            Observaciones,
                            Usuario,
                            Notificar
                        ) VALUES (?, ?, ?, GETDATE(), GETDATE(), ?, ?, ?)";
        
        $stmtRegentPer = $Conexion->prepare($sqlRegentPer);
        $stmtRegentPer->bind_param(
            $IdPersonal,
            $Ubicacion,
            $DispN,
            $Observaciones,
            $usuario,
            $NotificarSupervisor ? 1 : 0
        );
        
        if (!$stmtRegentPer->execute()) {
            throw new Exception('Error al registrar entrada: ' . $stmtRegentPer->error);
        }
        
        $IdMov = $stmtRegentPer->insert_id;
        $stmtRegentPer->close();
        
        $sqlRegentSalPer = "INSERT INTO regentsalper (
                                IdPer,
                                IdUbicacion,
                                FolMovEnt,
                                FechaEntrada,
                                StatusRegistro
                            ) VALUES (?, ?, ?, GETDATE(), 1)";
        
        $stmtRegentSalPer = $Conexion->prepare($sqlRegentSalPer);
        $stmtRegentSalPer->bind_param(
            $IdPersonal,
            $Ubicacion,
            $IdMov
        );
        
        if (!$stmtRegentSalPer->execute()) {
            throw new Exception('Error al registrar entrada en salper: ' . $stmtRegentSalPer->error);
        }
        
        $IdEntSal = $stmtRegentSalPer->insert_id;
        $stmtRegentSalPer->close();
        
        $fotosProcesadas = [];
        if (isset($input['fotos']) && is_array($input['fotos']) && count($input['fotos']) > 0) {
            $sqlFotoEncabezado = "INSERT INTO T_fotografia_Encabezado (
                                    IdEntSal,
                                    FechaIngreso,
                                    Tipo,
                                    TipoMov,
                                    IdUsuario,
                                    Estatus
                                ) VALUES (?, GETDATE(), 'Personal', 1, ?, 1)";
            
            $stmtFotoEncabezado = $Conexion->prepare($sqlFotoEncabezado);
            $stmtFotoEncabezado->bind_param($IdEntSal, $IdUsuario);
            
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
                    
                    $imagenDecodificada = base64_decode($base64Data);
                    
                    if ($imagenDecodificada === false) {
                        error_log("Error decodificando foto $index para acceso $IdEntSal");
                        continue;
                    }
                    
                    $rutaCompleta = $directorioFotos . $nombreFoto;
                    
                    if (file_put_contents($rutaCompleta, $imagenDecodificada)) {
                        $rutaRelativa = 'fotos_accesos/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $nombreFoto;
                        
                        $sqlFotoDetalle = "INSERT INTO T_fotografia_Detalle (
                                            IdFotografiaRef,
                                            NombreFoto,
                                            RutaFoto,
                                            NextIdFoto
                                        ) VALUES (?, ?, ?, ?, ?)";
                        
                        $stmtFotoDetalle = $Conexion->prepare($sqlFotoDetalle);
                        $stmtFotoDetalle->bind_param(
                            $IdEntSal,
                            $idFotografias,
                            $nombreFoto,
                            $rutaRelativa,
                            $nextIdFoto
                        );
                        
                        if ($stmtFotoDetalle->execute()) {
                            $fotosProcesadas[] = $nombreFoto;
                            $nextIdFoto++; 
                        }
                        
                        $stmtFotoDetalle->close();
                    }
                }
            }
        }

        if ($TipoTransporte === 2 && !empty($IdVehiculoTransporte)) {
            $sqlRegentVeh = "INSERT INTO regentveh (
                                IdVeh,
                                Ubicacion,
                                DispN,
                                Fecha,
                                TiempoMarcaje,
                                Observaciones,
                                Usuario,
                                Notificar
                            ) VALUES (?, ?, ?, GETDATE(), GETDATE(), ?, ?, ?)";
            
            $stmtRegentVeh = $Conexion->prepare($sqlRegentVeh);
            $stmtRegentVeh->bind_param(
                $IdVehiculoTransporte,
                $Ubicacion,
                $DispN,
                $Observaciones,
                $usuario,
                $NotificarSupervisor ? 1 : 0
            );
            
            if (!$stmtRegentVeh->execute()) {
                throw new Exception('Error al registrar entrada de vehiculo: ' . $stmtRegentVeh->error);
            }
            
            $IdMovVehiculo = $stmtRegentVeh->insert_id;
            $stmtFotoEncabezado->close();
        }

        if ($NotificarSupervisor === 1) {
            $sqlSupervisor = "SELECT 
                                Concat(Nombre,' ',ApPaterno,' ',ApMaterno) as NombreCompleto,
                                Email, Contacto
                                FROM t_personal where IdPersonal = 
                                (Select IdSupervisor from t_personal where IdPersonal = ?)"; 
                              
            $stmtSupervisor = $Conexion->prepare($sqlSupervisor);
            $stmtSupervisor->bind_param( $IdPersonal);
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
                    <p><strong>Ubicación:</strong> " . $Ubicacion . "</p>
                    <p><strong>Observaciones:</strong> " . $Observaciones . "</p>
                    <p><strong>Fecha:</strong> " . $fechaActual . "</p>
                    <p><strong>Hora de Entrada:</strong> " . $horaActual . "</p>
                    <p><strong>Registrado por:</strong> " . $usuario . "</p>
                    <p><strong>DispN:</strong> " . $DispN . "</p>
                ";
                
            }
            
            $stmtSupervisor->close();
        }
        
        
        $Conexion->commit();
        
        $response['success'] = true;
        $response['message'] = 'Acceso registrado correctamente';
        $response['data'] = [
            'folioMov' => $folioMov,
            'IdMov' => $IdMov,
            'IdEntSal' => $IdEntSal,
            'fecha' => $fechaActual,
            'hora' => $horaActual,
            'personal' => [
                'nombre' => $personal['NombreCompleto'],
                'noEmpleado' => $personal['NoEmpleado'],
                'empresa' => $personal['NomEmpresa']
            ],
            'IdUsuario' => $Ubicacion,
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