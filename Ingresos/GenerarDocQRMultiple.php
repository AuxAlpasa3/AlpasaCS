<?php
include('../api/db/conexion.php');
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
require "../vendor/phpqrcode/qrlib.php";

if (!isset($_POST['IdTarja']) || !isset($_POST['Cantidad']) || !isset($_POST['NombreImpresora']) || !isset($_POST['IdAlmacen'])) {
    die("Error: Faltan parámetros necesarios (IdTarja, Cantidad, NombreImpresora, IdAlmacen)");
}

$IdTarja = $_POST['IdTarja'];
$IdAlmacen = $_POST['IdAlmacen'];
$Cantidad = intval($_POST['Cantidad']);
$NombreImpresora = $_POST['NombreImpresora'];

if ($Cantidad <= 0) {
    die("Error: La cantidad debe ser un número positivo");
}

$sentiCodigos = $Conexion->prepare("SELECT CodBarras FROM t_ingreso WHERE IdTarja = :idTarja AND Almacen = :idAlmacen");
$sentiCodigos->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
$sentiCodigos->bindParam(':idAlmacen', $IdAlmacen, PDO::PARAM_INT);
$sentiCodigos->execute();
$codigos = $sentiCodigos->fetchAll(PDO::FETCH_COLUMN);

if (empty($codigos)) {
    die("Error: No se encontraron códigos de barras para la tarja especificada");
}

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

foreach ($codigos as $CodBarras) {
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
        continue;
    }

    $Cliente = $ClienteInfo->Cliente;
    $NumRecinto = $ClienteInfo->NumRecinto;

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

    // Limpiar archivo QR temporal
    if (file_exists($filename)) {
        unlink($filename);
    }
}

// Generar PDF en memoria
$pdfContent = $pdf->Output('', 'S');
$tempPdf = tempnam(sys_get_temp_dir(), 'tarja_') . '.pdf';
file_put_contents($tempPdf, $pdfContent);

// Verificar que SumatraPDF existe
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

// Mostrar resultado
if ($return_var === 0) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded',function(){
            Swal.fire({
                icon: 'success',
                title: 'La impresión se realizó con éxito',
                showConfirmButton: false,
                timer: 1500
            }).then(function() {
                window.location = 'ImprimirQrMultiple.php';
            });
        });
    </script>";
} else {
    echo "<script>
        alert('Error al imprimir. Código: $return_var');
        window.location = 'ImprimirQrMultiple.php';
    </script>";
}
?>