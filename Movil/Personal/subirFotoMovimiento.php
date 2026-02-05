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

    $requiredFields = ['IdMovimiento', 'NombreArchivo', 'IdUsuario', 'Ubicacion', 'FotoBase64'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || (is_string($input[$field]) && trim($input[$field]) === '')) {
            throw new Exception("Campo requerido faltante: $field");
        }
    }

    $IdMovimiento = $input['IdMovimiento'];
    $NombreArchivo = $input['NombreArchivo'];
    $Ubicacion = $input['Ubicacion'];
    $IdUsuario = $input['IdUsuario'];
    $base64Image = $input['FotoBase64'];
    $nextId = isset($input['nextId']) ? $input['nextId'] : 1;
    
    $RutaLocal = getenv('VERSION');

    $Conexion->beginTransaction();
    
    try {
        $sqlCheckEncabezado = "SELECT IdFotografias FROM t_fotografias_Encabezado 
                               WHERE IdEntSal = :IdEntSal AND Tipo = 1";
        $stmtCheck = $Conexion->prepare($sqlCheckEncabezado);
        $stmtCheck->bindParam(':IdEntSal', $IdMovimiento, PDO::PARAM_STR);
        $stmtCheck->execute();
        
        $existingEncabezado = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($existingEncabezado) {
            $IdFotografias = $existingEncabezado['IdFotografias'];
        } else {
            $sqlEncabezado = "INSERT INTO t_fotografias_Encabezado (
                                IdEntSal,
                                FechaIngreso,
                                Tipo,
                                TipoMov,
                                Ubicacion,
                                Estatus
                            ) VALUES (
                                :IdEntSal,
                                GETDATE(),
                                1,
                                1,
                                :Ubicacion,
                                1
                            )";
            
            $stmtEncabezado = $Conexion->prepare($sqlEncabezado);
            $stmtEncabezado->bindParam(':IdEntSal', $IdMovimiento, PDO::PARAM_STR);
            $stmtEncabezado->bindParam(':Ubicacion', $Ubicacion, PDO::PARAM_STR);
            
            if (!$stmtEncabezado->execute()) {
                $errorInfo = $stmtEncabezado->errorInfo();
                throw new Exception('Error al registrar encabezado: ' . ($errorInfo[2] ?? 'Error desconocido'));
            }

            $IdFotografias = (int)$Conexion->lastInsertId();

            if ($IdFotografias <= 0) {
                throw new Exception("No se pudo obtener el ID de la inserción");
            }
        }

        // Detectar tipo de imagen
        $imageHeader = substr($base64Image, 0, 20);
        if (strpos($imageHeader, '/9j/') !== false) {
            $extension = 'jpg';
            $mimeType = 'image/jpeg';
        } elseif (strpos($imageHeader, 'iVBORw0KGgo') !== false) {
            $extension = 'png';
            $mimeType = 'image/png';
        } else {
            $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
            $mimeType = 'image/jpeg';
            $extension = $allowedTypes[$mimeType] ?? 'jpg';
        }

        $directoryName = "Ubicacion{$Ubicacion}Movimiento{$IdMovimiento}";
        $uploadDir = "C:\\xampp\\htdocs\\{$RutaLocal}\\Empleados\\Fotografias\\{$directoryName}\\";

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                throw new Exception("Error creando directorio: {$uploadDir}");
            }
        }

        $nombreFoto = "Foto{$nextId}_{$NombreArchivo}.{$extension}";
        $filePath = $uploadDir . $nombreFoto;

        $imageData = base64_decode($base64Image, true);
        if ($imageData === false) {
            throw new Exception('Error decodificando imagen base64');
        }

        $bytesWritten = file_put_contents($filePath, $imageData);
        if ($bytesWritten === false || $bytesWritten === 0) {
            throw new Exception('Error guardando archivo en servidor');
        }

        if ($extension === 'jpg') {
            optimizarJpegAsync($filePath);
        }

        $rutaPublica = "https://intranet.alpasamx.com/{$RutaLocal}/Empleados/Fotografias/{$directoryName}/{$nombreFoto}";

        $sqlDetalle = "INSERT INTO t_fotografias_Detalle (
                            IdFotografiaRef,
                            NombreFoto,
                            RutaFoto,
                            NextIdFoto
                        ) VALUES (
                            :IdFotografiaRef,
                            :NombreFoto,
                            :RutaFoto,
                            :NextIdFoto
                        )";
        
        $stmtDetalle = $Conexion->prepare($sqlDetalle);
        $stmtDetalle->bindParam(':IdFotografiaRef', $IdFotografias, PDO::PARAM_INT);
        $stmtDetalle->bindParam(':NombreFoto', $nombreFoto, PDO::PARAM_STR);
        $stmtDetalle->bindParam(':RutaFoto', $rutaPublica, PDO::PARAM_STR);
        $stmtDetalle->bindParam(':NextIdFoto', $nextId, PDO::PARAM_INT);

        if (!$stmtDetalle->execute()) {
            $errorInfo = $stmtDetalle->errorInfo();
            throw new Exception('Error al registrar Fotografía: ' . ($errorInfo[2] ?? 'Error desconocido'));
        }

        $Conexion->commit();

        $response['success'] = true;
        $response['message'] = "Imagen subida exitosamente";
        $response['data'] = [
            'filePath' => $rutaPublica,
            'nextId' => $nextId,
            'IdFotografias' => $IdFotografias,
            'nombreFoto' => $nombreFoto
        ];

    } catch (Exception $e) {
        $Conexion->rollBack();
        
        if (isset($filePath) && file_exists($filePath)) {
            @unlink($filePath);
        }
        
        throw $e;
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    http_response_code(400);
} finally {
    if (isset($Conexion)) {
        $Conexion = null;
    }
}

echo json_encode($response);

function optimizarJpegAsync($filePath, $quality = 75)
{
    if (function_exists('shell_exec') && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        @shell_exec("jpegoptim --max=75 --strip-all --overwrite " . escapeshellarg($filePath) . " > /dev/null 2>&1 &");
    } else {
        register_shutdown_function(function () use ($filePath, $quality) {
            try {
                if (file_exists($filePath)) {
                    $image = imagecreatefromjpeg($filePath);
                    if ($image) {
                        imagejpeg($image, $filePath, $quality);
                        imagedestroy($image);
                    }
                }
            } catch (Exception $e) {
                // Silenciar errores de optimización
            }
        });
    }
}
?>