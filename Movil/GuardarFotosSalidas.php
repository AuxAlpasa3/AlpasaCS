<?php
require_once "../api/db/conexion.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$ZonaHoraria = getenv('ZonaHoraria') ?: 'America/Mexico_City';
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION') ?: 'V2';
$fecha = date('Y-m-d H:i:s');

$inicio = microtime(true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

$jsonInput = file_get_contents('php://input');
if (empty($jsonInput)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Datos vacíos']);
    exit;
}

$data = json_decode($jsonInput, true);
if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'JSON inválido']);
    exit;
}

if (empty($data['image']) || empty($data['IdTarja']) || empty($data['IdAlmacen'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Campos requeridos faltantes']);
    exit;
}

$base64Image = $data['image'];
$IdTarja = (int) $data['IdTarja'];
$IdAlmacen = (int) $data['IdAlmacen'];
$nextId = isset($data['nextId']) ? (int) $data['nextId'] : 1;

try {
    $sqlEncabezado = $Conexion->prepare("SELECT IdFotografias 
                                        FROM t_Fotografias_Encabezado 
                                        WHERE IdTarja = ? AND Tipo = 3 AND Almacen = ?");
    $sqlEncabezado->execute([$IdTarja, $IdAlmacen]);
    $encabezado = $sqlEncabezado->fetch(PDO::FETCH_ASSOC);
    
    if (!$encabezado) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'No se encontró un encabezado activo para esta tarja y almacén']);
        exit;
    }
    
    $IdFotografiaRef = (int)$encabezado['IdFotografias'];
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al buscar el encabezado de fotografías: ' . $e->getMessage()]);
    exit;
}

$imageHeader = substr($base64Image, 0, 20);
if (strpos($imageHeader, '/9j/') !== false) {
    $extension = 'jpg';
    $mimeType = 'image/jpeg';
} elseif (strpos($imageHeader, 'iVBORw0KGgo') !== false) {
    $extension = 'png';
    $mimeType = 'image/png';
} else {
    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    $mimeType = $data['mime'] ?? 'image/jpeg';
    $extension = $allowedTypes[$mimeType] ?? 'jpg';
}

$directoryName = "Almacen{$IdAlmacen}Tarja{$IdTarja}";
$uploadDir = "C:\\xampp\\htdocs\\{$RutaLocal}\\Salidas\\Fotografias\\{$directoryName}\\";

if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => "Error creando directorio: {$uploadDir}"]);
        exit;
    }
}

$nombreFoto = "Foto{$nextId}_Tarja{$IdTarja}_Almacen{$IdAlmacen}.{$extension}";
$filePath = $uploadDir . $nombreFoto;

try {
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

    // Insertar en base de datos de manera optimizada (Tipo 3 para Salidas)
    $sql = "INSERT INTO t_fotografias_detalle 
       (IdFotografiaRef, NombreFoto, RutaFoto,NextIdFoto)
        VALUES (?, ?, ?, ?)";

    $stmt = $Conexion->prepare($sql);
    $rutaPublica = "https://intranet.alpasamx.com/{$RutaLocal}/Salidas/Fotografias/{$directoryName}/{$nombreFoto}";

    $resultado = $stmt->execute([
        $IdFotografiaRef,
        $nombreFoto,
        $rutaPublica,
        $nextId
    ]);

    if (!$resultado) {
        @unlink($filePath);
        throw new Exception('Error insertando en base de datos: ' . implode(', ', $stmt->errorInfo()));
    }

    $tiempoTotal = round((microtime(true) - $inicio) * 1000, 2);

    echo json_encode([
        'success' => true,
        'message' => "Imagen subida exitosamente en {$tiempoTotal}ms",
        'filePath' => $rutaPublica,
        'nextId' => $nextId,
        'IdFotografiaRef' => $IdFotografiaRef,
        'timestamp' => $fecha
    ]);

} catch (Exception $e) {
    if (isset($filePath) && file_exists($filePath)) {
        @unlink($filePath);
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => $fecha
    ]);
}

/**
 * Optimizar JPEG de manera asíncrona para no bloquear la respuesta
 */
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
            }
        });
    }
}

/**
 * Función alternativa de optimización síncrona
 */
function optimizarJpeg($filePath, $quality = 75)
{
    try {
        if (!file_exists($filePath))
            return false;

        $image = imagecreatefromjpeg($filePath);
        if (!$image)
            return false;

        // Redimensionar si es muy grande (máximo 1920px de ancho)
        $anchoOriginal = imagesx($image);
        $altoOriginal = imagesy($image);

        if ($anchoOriginal > 1920) {
            $nuevoAncho = 1920;
            $nuevoAlto = intval($altoOriginal * ($nuevoAncho / $anchoOriginal));

            $imageNueva = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
            imagecopyresampled($imageNueva, $image, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);
            imagedestroy($image);
            $image = $imageNueva;
        }

        // Guardar optimizada
        imagejpeg($image, $filePath, $quality);
        imagedestroy($image);
        return true;

    } catch (Exception $e) {
        return false;
    }
}