<?php
include_once "../templates/SesionP.php";
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$ZonaHoraria = getenv('ZonaHoraria') ?: 'America/Mexico_City';
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION') ?: '';

// Validate input
if (!isset($_POST['IdTarja']) || !is_numeric($_POST['IdTarja'])) {
    die('Invalid Tarja ID');
}

$IdTarja = $_POST['IdTarja'];
$IdAlmacen = $_POST['IdAlmacen'];

$sentencia = $Conexion->prepare("SELECT t1.IdTarja, t3.IdRemision ,t2.Direccion,CONCAT(t2.Municipio,', ',t2.Estado,', ',t2.Pais) as Direccion2, t2.CodPostal,t2.NumRecinto
    FROM t_ingreso as t1 
    inner join t_almacen as t2 on t1.Almacen=t2.IdAlmacen
    inner join t_remision_encabezado as t3 on t1.IdRemision=t3.IdRemisionEncabezado
    WHERE IdTarja =? and t1.Almacen=? GROUP BY t1.IdTarja, t3.IdRemision ,t2.NumRecinto,t2.Direccion,t2.Municipio,t2.Estado,t2.Pais,t2.CodPostal,t2.NumRecinto");
$sentencia->execute([$IdTarja,$IdAlmacen]);
$datosTarja = $sentencia->fetch(PDO::FETCH_OBJ);

if (!$datosTarja) {
    die("No se encontró la tarja especificada");
}

$Tarja = $datosTarja->IdTarja;
$IdRemision = $datosTarja->IdRemision;
$Direccion2 = $datosTarja->Direccion2;
$Direccion = $datosTarja->Direccion;
$NumRecinto = $datosTarja->NumRecinto;
$CodPostal = $datosTarja->CodPostal;

function comprimirImagenDesdeUrl($url, $calidad = 50) {
    $imageData = file_get_contents($url);
    if ($imageData === false) {
        return false;
    }
    
    $imagen = imagecreatefromstring($imageData);
    if (!$imagen) {
        return false;
    }
    
    $rutaComprimida = tempnam(sys_get_temp_dir(), 'compressed_');
    
    imagejpeg($imagen, $rutaComprimida, $calidad);
    imagedestroy($imagen);
    
    return $rutaComprimida;
}

function obtenerDimensionesDesdeUrl($url) {
    $headers = get_headers($url, 1);
    if (isset($headers['Content-Length'])) {
        $imageInfo = getimagesize($url);
        if ($imageInfo !== false) {
            return ['width' => $imageInfo[0], 'height' => $imageInfo[1]];
        }
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RANGE, '0-10000'); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $imageData = curl_exec($ch);
    curl_close($ch);
    
    if ($imageData) {
        $image = imagecreatefromstring($imageData);
        if ($image) {
            $width = imagesx($image);
            $height = imagesy($image);
            imagedestroy($image);
            return ['width' => $width, 'height' => $height];
        }
    }
    
    return ['width' => 0, 'height' => 0];
}

try {
    $stmt = $Conexion->query("SELECT RutaFoto, NombreFoto FROM t_fotografias_Detalle as t1 inner join t_fotografias_Encabezado as t2
on t1.IdFotografiaRef=t2.IdFotografias
WHERE IdTarja = $IdTarja AND tipo = 1 and Almacen =$IdAlmacen ORDER BY IdFoto ASC");
    $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $horizontalImages = [];
    $verticalImages = [];
    
    foreach ($imagenes as $imagen) {
        $urlImagen = $imagen['RutaFoto'];
        
        // Verificar si la URL es accesible
        $headers = @get_headers($urlImagen);
        if ($headers && strpos($headers[0], '200') !== false) {
            $dimensiones = obtenerDimensionesDesdeUrl($urlImagen);
            $width = $dimensiones['width'];
            $height = $dimensiones['height'];

            $rutaComprimida = comprimirImagenDesdeUrl($urlImagen);
            
            if ($rutaComprimida) {
                $imagen['RutaComprimida'] = $rutaComprimida;
                
                if ($width > $height) {
                    $horizontalImages[] = $imagen;
                } else {
                    $verticalImages[] = $imagen;
                }
            }
        }
    }
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Tarja de Ingreso ALP'.$NumRecinto.'-ING-'.sprintf("%04d", $Tarja) . '</title>
        <style>
          @import url("https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap");
            @page {
                margin: 20px;
            }
            .header {
                text-align: center;
                margin-top: 0px;
            }

            .header2 {
                position: absolute;
                top: 50px;
            }

            .EncabezadosRem {
                position: absolute;
                top: 65px;
                margin: 0;
                padding: 0;
                right: 1px;
            }

            .EncabezadosTar {
                position: absolute;
                top: 105px;
                margin: 0;
                padding: 0;
                right: 1px;
            }

            .remisiontext {
                position: fixed;
                top: 75px;
                left: 630px;  
                margin: 0;
                padding: 0;
                color:darkorange;
                font-size: 10px;
                font-weight: bold;
            }
            .tarjatext {
                position: fixed;
                top: 115px;
                left: 630px; 
                color:darkorange;  
                margin: 0;
                padding: 0;
                font-size: 10px;
                font-weight: bold;
            }
             

            .remision {
                position: fixed;
                top: 85px;
                left: 630px;  
                margin: 0;
                padding: 0;
                font-size: 16px;
                font-weight: bold;
            }
            .tarja {
                position: fixed;
                top: 125px;
                left: 630px;   
                margin: 0;
                padding: 0;
                font-size: 16px;
                font-weight: bold;
            }
             
            .company {
                 font-family: "Arimo", sans-serif;
                font-weight: bold;
            }
            .TipoTarja {
                font-family: "Arimo", sans-serif;
                font-weight: bold;
                font-size: 28px;
                top: 55px;
            }
            
            body { 
                font-family: Arial, sans-serif;
                font-size: 11px;
                margin: 0;
                padding: 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
                font-size: 8px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 4px;
                text-align: left;
                font-size: 9px;
                text-align: center;
            }
            th {
                background-color: darkorange;
                color: white;
                text-align: center;
            }
            .footer {
                margin-top: 30px;
                page-break-inside: avoid;
            }
            .info-section {
                margin-bottom: 20px;
                margin-top: 5px;
            }
            .info-label {
                font-weight: bold;
                display: inline-block;
                margin-top: 10px;
            }
            .info-texto {
                display: inline-block;
                text-align: center;
                border-bottom: 1px solid black;
                padding-bottom: 1px;
            }
            .signature-line {
                border-top: 1px solid black;
                width: 90%;
                margin: 0 auto;
                padding-top: 5px;
                text-align: center;
            }
            .cuadro-comentarios {
                width: 740px;
                height: 50px;
                border: 3px solid darkorange; 
                background-color: transparent; 
                padding: 15px;
                margin-top: 10px;
                font-family: Arial, sans-serif;
            }
            
            .cuadro-comentarios h3 {
                margin-top: 0;
                color: darkorange;
            }
            .signature-img {
                max-height: 100px;
                max-width: 200px;
                margin-bottom: -20px;
            }
            
            /* Estilos para imágenes */
            .imagen-horizontal {
                max-width: 300px;
                max-height: 200px;
                width: auto;
                height: auto;
            }
            .imagen-vertical {
                max-width: 150px;
                max-height: 200px;
                width: auto;
                height: auto;
            }
            .celda-imagen-horizontal, .celda-imagen-vertical {
                text-align: center;
                vertical-align: middle;
            }
        </style>
    </head>
    <body>
        <div class="header">
        <div class="company"><b>ALMACENAMIENTO Y LOGISTICA PORTUARIA DE ALTAMIRA, S.A DE C.V</b></div>
        <div class="company"><b>'.$Direccion.'</b></div>
        <div class="company"><b>'.$Direccion2.'</b></div>
        <div class="company"><b>C.P. '.$CodPostal.' TEL. (833) 260 64 51 Y (833) 260 94 54</b></div>
        <div class="company"><b><a href="https://www.alpasa.mx/">www.alpasa.com.mx</a></b></div>
        <div class="company"><b>R.F.C. ALP-070126-EV4</b></div>
        <div class="TipoTarja"><b>TARJA FOTOGRAFIAS</b></div>
    </div>';

    $Header = "../dist/img/logoalpasa.png";
    if (file_exists($Header)) {
        $imageData = file_get_contents($Header);
        $base64 = 'data:' . mime_content_type($Header) . ';base64,' . base64_encode($imageData);
        $html .= '<img class="header2" src="' . $base64 . '"width="230" height="80" />';
    }

    $html .= '
            <div class="EncabezadosRem" style="border-top: 7px solid darkorange; width: 20%; margin: 0;"></div>
             <div class="text-overlay"><h6 class="remisiontext">#Remision</h6></div>

            <div class="text-overlay"><h4 class="remision"> '. htmlspecialchars($IdRemision) .'</h4></div>

            <div class="EncabezadosTar" style="border-top: 7px solid darkorange; width: 20%; margin: 0;"></div>

             <div class="text-overlay"><h6 class="tarjatext">#Tarja</h6></div>
             <div class="text-overlay"><h5 class="tarja">ALP'.$NumRecinto.'-ING-'.sprintf("%04d", $Tarja). '</h5></div>
            </div>

            <div style="border-top: 7px solid darkorange; width: 100%; margin: 0;"></div>';

    // Mostrar imágenes horizontales (2 columnas)
    if (!empty($horizontalImages)) {
        $html .= '<br><br><br><table class="tabla-imagenes">';
        
        for ($i = 0; $i < count($horizontalImages); $i += 2) {
            $html .= '<tr>';
            
            $html .= '<td class="celda-imagen-horizontal">';
            if (isset($horizontalImages[$i])) {
                $imageData = file_get_contents($horizontalImages[$i]['RutaComprimida']);
                $base64 = 'data:image/jpeg;base64,'.base64_encode($imageData);
                
                $html .= '<img class="imagen-horizontal" src="'.$base64.'" />';
                unlink($horizontalImages[$i]['RutaComprimida']);
            }
            $html .= '</td>';
            
            $html .= '<td class="celda-imagen-horizontal">';
            if (isset($horizontalImages[$i+1])) {
                $imageData = file_get_contents($horizontalImages[$i+1]['RutaComprimida']);
                $base64 = 'data:image/jpeg;base64,'.base64_encode($imageData);
                
                $html .= '<img class="imagen-horizontal" src="'.$base64.'" />';
                unlink($horizontalImages[$i+1]['RutaComprimida']);
            }
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
    }
    
    // Mostrar imágenes verticales (4 columnas)
    if (!empty($verticalImages)) {
        $html .= '<table class="tabla-imagenes">';
        
        for ($i = 0; $i < count($verticalImages); $i += 4) {
            $html .= '<tr>';
            
            for ($j = 0; $j < 4; $j++) {
                $html .= '<td class="celda-imagen-vertical">';
                if (isset($verticalImages[$i+$j])) {
                    $imageData = file_get_contents($verticalImages[$i+$j]['RutaComprimida']);
                    $base64 = 'data:image/jpeg;base64,'.base64_encode($imageData);
                    
                    $html .= '<div class="contenedor-imagen-vertical">
                                <img class="imagen-vertical" src="'.$base64.'" />
                              </div>';
                    unlink($verticalImages[$i+$j]['RutaComprimida']);
                }
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</table>';
    }
    
    $html .= '</body></html>';
    
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();

    // CORRECCIÓN: Usar output() para generar el PDF
    $output = $dompdf->output();
    
    if (empty($output)) {
        throw new Exception("Error al generar el PDF: el contenido está vacío");
    }

    // Configurar headers para descarga
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="TarjaIngreso_' . $Tarja . '.pdf"');
    header('Content-Length: ' . strlen($output));
    
    // Enviar el PDF generado
    echo $output;
    exit; 

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>