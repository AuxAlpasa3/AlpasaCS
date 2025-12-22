<?php
require_once "../api/db/conexion.php"; 

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$ZonaHoraria = getenv('ZonaHoraria');
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION');

$uploadDir = "C:\\xampp\\htdocs\\".$RutaLocal."\\Salidas\\FirmasTransportistas\\";
$URL = "https://intranet.alpasamx.com/".$RutaLocal."/Salidas/FirmasTransportistas/";

// Función para validar datos de entrada
function validarDatosEntrada($data) {
    $errores = [];
    
    if (!isset($data['image']) || empty(trim($data['image']))) {
        $errores[] = 'La imagen en base64 es requerida';
    }
    
    if (!isset($data['mime']) || empty(trim($data['mime']))) {
        $errores[] = 'El tipo MIME es requerido';
    } else {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (!in_array($data['mime'], $allowedMimes)) {
            $errores[] = 'Tipo de imagen no soportado: ' . $data['mime'];
        }
    }
    
    if (!isset($data['IdTarja']) || empty(trim($data['IdTarja']))) {
        $errores[] = 'El ID de tarja es requerido';
    } elseif (!is_numeric($data['IdTarja'])) {
        $errores[] = 'El ID de tarja debe ser numérico';
    }
    
    if (!isset($data['IdAlmacen']) || empty(trim($data['IdAlmacen']))) {
        $errores[] = 'El ID de almacén es requerido';
    } elseif (!is_numeric($data['IdAlmacen'])) {
        $errores[] = 'El ID de almacén debe ser numérico';
    }
    
    if (!isset($data['fileName']) || empty(trim($data['fileName']))) {
        $errores[] = 'El nombre de archivo es requerido';
    }
    
    return $errores;
}

// Función para verificar si la tarja existe
function verificarTarjaExistente($conexion, $IdTarja, $IdAlmacen) {
    try {
        $sql = "SELECT COUNT(*) as total_registros 
                FROM t_ingreso 
                WHERE IdTarja = ? AND Almacen = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$IdTarja, $IdAlmacen]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'existe' => $result && $result['total_registros'] > 0,
            'total_registros' => $result['total_registros'] ?? 0
        ];
    } catch (Exception $e) {
        error_log('Error verificando tarja: ' . $e->getMessage());
        return ['existe' => false, 'total_registros' => 0];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar que se recibió contenido
        $input = file_get_contents('php://input');
        if (empty($input)) {
            throw new Exception('No se recibieron datos');
        }
        
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON inválido: ' . json_last_error_msg());
        }
        
        // Validar datos de entrada
        $erroresValidacion = validarDatosEntrada($data);
        if (!empty($erroresValidacion)) {
            throw new Exception('Datos incompletos o inválidos: ' . implode(', ', $erroresValidacion));
        }
        
        $base64Image = $data['image'];
        $mimeType = $data['mime'];
        $IdTarja = intval($data['IdTarja']);
        $IdAlmacen = intval($data['IdAlmacen']);
        $fileName = $data['fileName'];
        
        // Verificar que la tarja existe
        $infoTarja = verificarTarjaExistente($Conexion, $IdTarja, $IdAlmacen);
        if (!$infoTarja['existe']) {
            throw new Exception("La tarja $IdTarja no existe en el almacén $IdAlmacen");
        }
        
        // Crear directorio si no existe
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Error al crear la carpeta: " . $uploadDir);
            }
        }
        
        // Verificar permisos de escritura
        if (!is_writable($uploadDir)) {
            throw new Exception("El directorio no tiene permisos de escritura: " . $uploadDir);
        }
        
        // Decodificar imagen
        $imageData = base64_decode($base64Image, true);
        if ($imageData === false) {
            throw new Exception('Error al decodificar la imagen base64 - datos corruptos');
        }
        
        // Validar que es una imagen válida
        $imageInfo = getimagesizefromstring($imageData);
        if ($imageInfo === false) {
            throw new Exception('Los datos decodificados no representan una imagen válida');
        }
        
        // Limpiar y validar nombre del archivo
        $cleanFileName = preg_replace('/[^a-zA-Z0-9._-]/', '-', $fileName);
        $cleanFileName = substr($cleanFileName, 0, 255);
        
        // Agregar timestamp para evitar colisiones
        $timestamp = time();
        $uniqueFileName = pathinfo($cleanFileName, PATHINFO_FILENAME) . "_" . $timestamp . "." . pathinfo($cleanFileName, PATHINFO_EXTENSION);
        
        $filePath = $uploadDir . $uniqueFileName;
        $filePublicURL = $URL . $uniqueFileName;
        
        // Verificar si el archivo ya existe
        if (file_exists($filePath)) {
            throw new Exception('El archivo ya existe: ' . $uniqueFileName);
        }
        
        // Guardar archivo
        $bytesWritten = file_put_contents($filePath, $imageData);
        if ($bytesWritten === false || $bytesWritten === 0) {
            throw new Exception('Error al guardar la imagen en el servidor - cero bytes escritos');
        }
        
        // Verificar que el archivo se guardó correctamente
        if (!file_exists($filePath) || filesize($filePath) === 0) {
            throw new Exception('El archivo no se guardó correctamente');
        }
        
        // Actualizar base de datos con transacción
        $Conexion->beginTransaction();
        
        try {
            // Actualizar TODOS los registros con la misma IdTarja
            $sqlUpdate = $Conexion->prepare("UPDATE t_ingreso SET Firma = ? WHERE IdTarja = ? AND Almacen = ?");
            
            if (!$sqlUpdate) {
                throw new Exception('Error preparando la consulta: ' . implode(', ', $Conexion->errorInfo()));
            }
            
            $resultado = $sqlUpdate->execute([$filePublicURL, $IdTarja, $IdAlmacen]);
            
            if (!$resultado) {
                throw new Exception('Error al ejecutar la actualización: ' . implode(', ', $sqlUpdate->errorInfo()));
            }
            
            // Verificar si se actualizaron filas
            $filasAfectadas = $sqlUpdate->rowCount();
            if ($filasAfectadas === 0) {
                throw new Exception("No se encontraron registros de la tarja $IdTarja en el almacén $IdAlmacen para actualizar");
            }
            
            $Conexion->commit();
            
            // Log exitoso
            error_log("Firma guardada exitosamente - Tarja: $IdTarja, Archivo: $uniqueFileName, Registros actualizados: $filasAfectadas");
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => "Firma guardada correctamente en $filasAfectadas registro(s)",
                'filePath' => $filePublicURL,
                'fileName' => $uniqueFileName,
                'fileSize' => filesize($filePath),
                'rowsAffected' => $filasAfectadas,
                'totalRecords' => $infoTarja['total_registros'],
                'timestamp' => $timestamp
            ]);
            
        } catch (Exception $dbError) {
            $Conexion->rollBack();
            // Eliminar archivo si la transacción falló
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            throw $dbError;
        }
        
    } catch (Exception $e) {
        error_log('Error guardando firma: ' . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'errorType' => 'validation_error',
            'timestamp' => time()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido. Se esperaba POST',
        'allowedMethods' => ['POST', 'OPTIONS']
    ]);
}
?>