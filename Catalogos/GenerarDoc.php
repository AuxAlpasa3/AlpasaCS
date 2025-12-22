<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); 
if (!isset($_GET['ID']) || !is_numeric($_GET['ID']) || $_GET['ID'] <= 0) {
    http_response_code(400);
    die('ID inválido o no proporcionado');
}

$personalId = (int)$_GET['ID'];

  Include_once "../templates/SesionP.php";
// Verificar conexión
if (!$mysqli) {
    http_response_code(500);
    die('Error en la conexión a la base de datos');
}
define('BASE_PATH', dirname(__DIR__));
define('TEMPLATE_PATH', BASE_PATH . '/Credenciales/CredPersonal.docx');
define('OUTPUT_DIR', 'C:/Users/kbarrios/Desktop/Credenciales/Documentos/');
define('QR_DIR', 'QR/');

// Crear directorios si no existen
$directories = [QR_DIR, OUTPUT_DIR];
foreach ($directories as $dir) {
    if (!file_exists($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        http_response_code(500);
        die("No se pudo crear el directorio: $dir");
    }
}


require_once dirname(__FILE__) . '/PHPWord-master/src/PhpWord/Autoloader.php';
\PhpOffice\PhpWord\Autoloader::register();
use PhpOffice\PhpWord\TemplateProcessor;

if (!file_exists(TEMPLATE_PATH)) {
    http_response_code(500);
    die('Plantilla Word no encontrada');
}

if (!file_exists('phpqrcode/qrlib.php')) {
    http_response_code(500);
    die('Librería QR no encontrada');
}
require_once "phpqrcode/qrlib.php";

try {
        $query = "SELECT 
                    RIGHT('00000' + CAST(IdPersonal AS VARCHAR(5)), 5) as IdPersonal,
                    Nombre,
                    CONCAT(ApPaterno, ' ', ApMaterno) as Apellidos,
                    (CASE 
                        WHEN t1.Departamento = 0 THEN 'SinDepto' 
                        ELSE t4.NomDepto 
                    END) AS NomDepto
                FROM t_personal as t1 
                LEFT JOIN t_departamento as t4 ON t4.IdDepartamento = t1.Departamento 
                WHERE IdPersonal = ?";
    
    $stmt = $mysqli->prepare($query);
    
    if (!$stmt) {
        throw new Exception('Error preparando la consulta: ' . $mysqli->error);
    }
    
    $stmt->bind_param('i', $personalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        die('Personal no encontrado');
    }
    
    $personal = $result->fetch_assoc();
    $stmt->close();
    
    $idPersonal = $personal['IdPersonal'];
    $nombre = $personal['Nombre'];
    $apellidos = $personal['Apellidos'];
    $puesto = $personal['NomDepto'];
    
} catch (Exception $e) {
    http_response_code(500);
    die('Error en la consulta: ' . $e->getMessage());
}

$qrFilename = QR_DIR . 'QRCredencialPersonal' . $idPersonal . '.png';

try {
    // Generar QR
    QRcode::png(
        $idPersonal,        // Contenido
        $qrFilename,        // Archivo de salida
        'L',               // Nivel de corrección (L, M, Q, H)
        11,                // Tamaño de pixel
        1                  // Tamaño del margen
    );
    
    if (!file_exists($qrFilename)) {
        throw new Exception('No se pudo generar el código QR');
    }
    
} catch (Exception $e) {
    error_log('Error generando QR: ' . $e->getMessage());
}

try {
    
    $templateWord = new TemplateProcessor(TEMPLATE_PATH);
    $templateWord->setValue('Nombre', htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'));
    $templateWord->setValue('Apellidos', htmlspecialchars($apellidos, ENT_QUOTES, 'UTF-8'));
    $templateWord->setValue('Puesto', htmlspecialchars($puesto, ENT_QUOTES, 'UTF-8'));
    $templateWord->setValue('ID', htmlspecialchars($idPersonal, ENT_QUOTES, 'UTF-8'));
    
    if (file_exists($qrFilename)) {
        $templateWord->setImageValue('qr', [
            'path' => $qrFilename,
            'width' => 100,    // Ancho en puntos
            'height' => 100,   // Alto en puntos
            'ratio' => true    // Mantener proporción
        ]);
    }
    
    $outputFilename = 'CredencialPersonal' . $idPersonal . '.docx';
    $outputPath = OUTPUT_DIR . $outputFilename;
    
    $templateWord->saveAs($outputPath);
    
    if (!file_exists($outputPath)) {
        throw new Exception('No se pudo guardar el documento');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    die('Error procesando el documento: ' . $e->getMessage());
}

try {
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . $outputFilename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($outputPath));
    
    readfile($outputPath);
    unlink($outputPath);
    
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    die('Error enviando el archivo: ' . $e->getMessage());
}

function limpiarArchivosAntiguos($directorio, $dias = 7) {
    if (!is_dir($directorio)) return;
    
    $archivos = glob($directorio . 'CredencialPersonal*.docx');
    $ahora = time();
    
    foreach ($archivos as $archivo) {
        if (is_file($archivo)) {
            if ($ahora - filemtime($archivo) >= 60 * 60 * 24 * $dias) {
                unlink($archivo);
            }
        }
    }
}

function limpiarQRsAntiguos($directorio, $dias = 7) {
    if (!is_dir($directorio)) return;
    
    $archivos = glob($directorio . 'QRCredencialPersonal*.png');
    $ahora = time();
    
    foreach ($archivos as $archivo) {
        if (is_file($archivo)) {
            if ($ahora - filemtime($archivo) >= 60 * 60 * 24 * $dias) {
                unlink($archivo);
            }
        }
    }
}

if (rand(1, 100) === 1) {
    limpiarArchivosAntiguos(OUTPUT_DIR, 7);
    limpiarQRsAntiguos(QR_DIR, 7);
}