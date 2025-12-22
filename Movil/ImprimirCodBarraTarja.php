<?php
header('Content-Type: application/json; charset=UTF-8');
include('../api/db/conexion.php');
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
require "../vendor/phpqrcode/qrlib.php";

// Configuración de respuesta JSON
$response = ['success' => false, 'message' => ''];

try {
    // Validar parámetros requeridos
    if (!isset($_POST['CodBarrasNum']) || !isset($_POST['NombreImpresora']) || 
        !isset($_POST['CantidadCopias']) || !isset($_POST['IdAlmacen'])) {
        throw new Exception('Faltan parámetros necesarios (CodBarrasNum, NombreImpresora, CantidadCopias, IdAlmacen)');
    }

    $CodBarras = $_POST['CodBarrasNum'];
    $IdAlmacen = $_POST['IdAlmacen'];
    $Cantidad = intval($_POST['CantidadCopias']);
    $NombreImpresora = $_POST['NombreImpresora'];

    if ($Cantidad <= 0) {
        throw new Exception('La cantidad debe ser un número positivo');
    }

    // Obtener información del cliente
    $sentiCliente = $Conexion->prepare("SELECT DISTINCT(t2.NombreCliente) as Cliente, t3.NumRecinto
                                      FROM t_ingreso AS t1 
                                      INNER JOIN dbo.t_cliente AS t2 ON t1.Cliente=t2.IdCliente 
                                      INNER JOIN t_almacen as t3 on t1.Almacen=t3.IdAlmacen
                                      WHERE t1.CodBarras = :codBarras AND t1.Almacen = :idAlmacen");
    $sentiCliente->bindParam(':codBarras', $CodBarras, PDO::PARAM_STR);
    $sentiCliente->bindParam(':idAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $sentiCliente->execute();
    $ClienteInfo = $sentiCliente->fetch(PDO::FETCH_OBJ);

    if (!$ClienteInfo) {
        throw new Exception('No se encontró información para el código de barras especificado');
    }

    $Cliente = $ClienteInfo->Cliente;
    $NumRecinto = $ClienteInfo->NumRecinto;

    // Configurar zona horaria
    $ZonaHoraria = getenv('ZonaHoraria') ?: 'America/Mexico_City';
    date_default_timezone_set($ZonaHoraria);

    $dir = 'QR/';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    $pageWidth = 102 * 2.835; 
    $pageHeight = 76 * 2.835; 

    $pdf = new TCPDF('L', 'pt', array($pageWidth, $pageHeight), true, 'UTF-8', false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetAutoPageBreak(FALSE, 0);
    $pdf->SetFont('helvetica', 'B', 15);

    $filename = $dir . 'CodBarras_' . $CodBarras . '_' . $IdAlmacen . '.png';
    QRcode::png($CodBarras, $filename, 'L', 15, 0);

    for ($i = 0; $i < $Cantidad; $i++) {
        $pdf->AddPage();

        $centerX = $pageWidth / 2;
        $centerY = $pageHeight / 2;
        
        $qrSize = 170; 
        $qrX = $centerX - ($qrSize / 2);
        $qrY = $centerY - ($qrSize / 2) - 10; 

        $pdf->Image($filename, $qrX, $qrY, $qrSize, $qrSize, 'PNG', '', '', false, 300);

        $textStartY = $qrY + $qrSize + 5;
        $pdf->SetXY(10, $textStartY);
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell($pageWidth - 20, 0, $NumRecinto . '-' . sprintf("%06d", $CodBarras), 0, 1, 'C');

        $pdf->SetXY(10, $textStartY + 15);
        $pdf->SetFont('helvetica', 'B', 15);
        
        if (strlen($Cliente) > 25) {
            $partes = wordwrap($Cliente, 25, "|", true);
            $lineas = explode("|", $partes);
            
            $pdf->SetX(10);
            $pdf->Cell($pageWidth - 20, 0, $lineas[0], 0, 1, 'C');
            
            if (isset($lineas[1])) {
                $pdf->SetXY(10, $textStartY + 24);
                $pdf->Cell($pageWidth - 20, 0, $lineas[1], 0, 1, 'C');
            }
        } else {
            $pdf->SetX(10);
            $pdf->Cell($pageWidth - 20, 0, $Cliente, 0, 1, 'C');
        }
    }

    if (file_exists($filename)) {
        unlink($filename);
    }

    $pdfContent = $pdf->Output('', 'S');
    $tempPdf = tempnam(sys_get_temp_dir(), 'tarja_') . '.pdf';
    file_put_contents($tempPdf, $pdfContent);

    $sumatraPath = 'C:\\Users\\Administrador\\AppData\\Local\\SumatraPDF\\SumatraPDF.exe';
    if (!file_exists($sumatraPath)) {
        // Intentar rutas alternativas
        $alternativePaths = [
            'C:\\Program Files\\SumatraPDF\\SumatraPDF.exe',
            'C:\\Program Files (x86)\\SumatraPDF\\SumatraPDF.exe',
        ];
        
        $sumatraFound = false;
        foreach ($alternativePaths as $altPath) {
            if (file_exists($altPath)) {
                $sumatraPath = $altPath;
                $sumatraFound = true;
                break;
            }
        }
        
        if (!$sumatraFound) {
            throw new Exception("SumatraPDF no encontrado. Verifique la instalación.");
        }
    }

    // Verificar que la impresora existe
    $printers = shell_exec('wmic printer get name');
    if (strpos($printers, $NombreImpresora) === false) {
        throw new Exception("La impresora '$NombreImpresora' no está instalada o no existe");
    }

    // Intentar impresión con SumatraPDF
    $command = '"' . $sumatraPath . '" -print-to "' . $NombreImpresora . '" "' . $tempPdf . '" 2>&1';
    exec($command, $output, $return_var);

    // Si falla, intentar método alternativo
    if ($return_var !== 0) {
        $alternativeCommand = 'rundll32.exe printui.dll,PrintUIEntry /k /n "' . $NombreImpresora . '"';
        exec($alternativeCommand, $altOutput, $altReturn);
        
        if ($altReturn === 0) {
            $printCommand = 'print /d:"' . $NombreImpresora . '" "' . $tempPdf . '"';
            exec($printCommand, $printOutput, $printReturn);
            
            if ($printReturn === 0) {
                $return_var = 0; 
            }
        }
    }

    // Limpiar archivo temporal
    unlink($tempPdf);

    if ($return_var === 0) {
        $response['success'] = true;
        $response['message'] = "La impresión se realizó con éxito. Se imprimieron $Cantidad copias.";
        $response['data'] = [
            'cliente' => $Cliente,
            'codigo_barras' => $CodBarras,
            'almacen' => $IdAlmacen,
            'recinto' => $NumRecinto
        ];
    } else {
        throw new Exception("Error al imprimir. Código de retorno: $return_var");
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    // Devolver respuesta JSON
    echo json_encode($response);
    exit;
}
?>