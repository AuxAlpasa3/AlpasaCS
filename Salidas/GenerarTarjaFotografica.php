<?php
ini_set('memory_limit', '1024M');
include('../api/db/conexion.php');

require_once '../vendor/autoload.php';
require "../lib/phpqrcode/qrlib.php"; 

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true); 
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);

$RutaLocal = getenv('VERSION');
$IdTarja = $_GET['IdTarja'];

$sentencia = $Conexion->prepare("SELECT IdTarja as Tarja, IdRemision FROM t_salida WHERE idTarja = ? GROUP BY IdTarja, IdRemision");
$sentencia->execute([$IdTarja]);
$datosTarja = $sentencia->fetch(PDO::FETCH_OBJ);

if (!$datosTarja) {
    die("No se encontró la tarja especificada");
}

$Tarja = $datosTarja->Tarja;
$IdRemision = $datosTarja->IdRemision;

function procesarImagenParaPDF($rutaArchivo, $esHorizontal) {
    if (!file_exists($rutaArchivo)) return false;
    
    $maxWidth = $esHorizontal ? 800 : 400;
    $calidad = 75; 
    
    $info = getimagesize($rutaArchivo);
    if (!$info) return false;
    
    list($ancho, $alto) = $info;
    $mime = $info['mime'];
    
    $nuevoAncho = min($ancho, $maxWidth);
    $nuevoAlto = intval($alto * ($nuevoAncho / $ancho));
    
    switch ($mime) {
        case 'image/jpeg': $original = imagecreatefromjpeg($rutaArchivo); break;
        case 'image/png': $original = imagecreatefrompng($rutaArchivo); break;
        case 'image/gif': $original = imagecreatefromgif($rutaArchivo); break;
        default: return false;
    }
    
    if (!$original) return false;
    
    $imagen = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
    
    if ($mime == 'image/png') {
        imagealphablending($imagen, false);
        imagesavealpha($imagen, true);
        $transparente = imagecolorallocatealpha($imagen, 255, 255, 255, 127);
        imagefilledrectangle($imagen, 0, 0, $nuevoAncho, $nuevoAlto, $transparente);
    }
    
    imagecopyresampled($imagen, $original, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
    
    ob_start();
    imagejpeg($imagen, null, $calidad);
    $contenido = ob_get_clean();
    
    imagedestroy($original);
    imagedestroy($imagen);
    
    return 'data:image/jpeg;base64,'.base64_encode($contenido);
}

try {
    $stmt = $Conexion->query("SELECT RutaFoto, NombreFoto FROM t_fotografias WHERE IdTarja = $IdTarja AND tipo = 3 ORDER BY IdFoto ASC");
    $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $horizontalImages = [];
    $verticalImages = [];
    
    foreach ($imagenes as $imagen) {
        $baseUrl = 'https://intranet.alpasamx.com/'.$RutaLocal.'/Salidas/';
        $basePath = ''; 
        $rutaLocal = str_replace($baseUrl, $basePath, $imagen['RutaFoto']);
        
        if (file_exists($rutaLocal)) {
            list($width, $height) = getimagesize($rutaLocal);
            $esHorizontal = $width > $height;
            
            $imagenProcesada = procesarImagenParaPDF($rutaLocal, $esHorizontal);
            
            if ($imagenProcesada) {
                if ($esHorizontal) {
                    $horizontalImages[] = $imagenProcesada;
                } else {
                    $verticalImages[] = $imagenProcesada;
                }
            }
        }
    }
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            @page { margin: 100px 25px; }
            .header {
                position: fixed;
                top: -120px;
                left: -30px;
                height: 10px;
            }
            .remision {
                position: fixed;
                top: -80px;
                left: 660px;
                right: 110px;
                height: 20px;
            }
            .tarja {
                position: fixed;
                top: -20px;
                left: 620px;
                height: 20px;
            }
            body { 
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 10px;
            }
            .tabla-imagenes {
                width: 100%;
                border-collapse: collapse;
                border: none;
            }
            .celda-imagen-horizontal {
                width: 50%;
                vertical-align: top;
                padding: 5px;
                border: none;
                page-break-inside: avoid;
            }
            .celda-imagen-vertical {
                width: 25%;
                vertical-align: top;
                padding: 5px;
                border: none;
                page-break-inside: avoid;
            }
            .imagen-horizontal {
                max-width: 100%;
                max-height: 200px;
                height: auto;
                display: block;
                margin: 0 auto;
            }
            .imagen-vertical {
                width: 100%;
                height: 300px; 
                object-fit: cover;
                display: block;
                margin: 0 auto;
            }
            .contenedor-imagen-vertical {
                width: 100%;
                height: 300px;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 5px;
            }
        </style>
    </head>
    <body>
        <div class="header">';

    $Header = "ProcesoFotografias/Header.jpg";
    if (file_exists($Header)) {
        $imageData = file_get_contents($Header);
        $base64 = 'data:'.mime_content_type($Header).';base64,'.base64_encode($imageData);
        $html .= '<img class="header2" src="'.$base64.'" width="810" height="188" />';
    }

    $html .= '<div class="text-overlay"><h4 class="remision">'.$IdRemision.'</h4></div>
              <div class="text-overlay"><h5 class="tarja"> ALPSV-SAL-'.$IdTarja.'</h5></div>
              </div>';
    
    if (!empty($horizontalImages)) {
        $html .= '<br><br><br><table class="tabla-imagenes">';
        
        for ($i = 0; $i < count($horizontalImages); $i += 2) {
            $html .= '<tr>';
            $html .= '<td class="celda-imagen-horizontal">';
            if (isset($horizontalImages[$i])) {
                $html .= '<img class="imagen-horizontal" src="'.$horizontalImages[$i].'" />';
            }
            $html .= '</td>';
            
            $html .= '<td class="celda-imagen-horizontal">';
            if (isset($horizontalImages[$i+1])) {
                $html .= '<img class="imagen-horizontal" src="'.$horizontalImages[$i+1].'" />';
            }
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
    }
    
    if (!empty($verticalImages)) {
        $html .= '<table class="tabla-imagenes">';
        
        for ($i = 0; $i < count($verticalImages); $i += 4) {
            $html .= '<tr>';
            
            for ($j = 0; $j < 4; $j++) {
                $html .= '<td class="celda-imagen-vertical">';
                if (isset($verticalImages[$i+$j])) {
                    $html .= '<div class="contenedor-imagen-vertical">
                                <img class="imagen-vertical" src="'.$verticalImages[$i+$j].'" />
                              </div>';
                }
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</table>';
    }
    
    $html .= '</body></html>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    header('Content-Type: application/pdf');
    header("Content-Disposition: inline; filename=reporte_fotografico_$IdTarja.pdf");
    echo $dompdf->output();
    exit;
    
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
} catch (Exception $e) {
    die("Error general: " . $e->getMessage());
}
?>