<?php
require_once '../../vendor/autoload.php';
include '../../api/db/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (!isset($_POST['IdPersonal'])) {
    die('IdPersonal requerido');
}

$IdPersonal = (int)$_POST['IdPersonal'];
$sql = "
    SELECT 
        IdPersonal,
        NoEmpleado,
        CONCAT(Nombre,' ',ApPaterno,' ',ApMaterno) AS NombreCompleto,
        CONCAT('01_',NoEmpleado) AS CodigoQR
    FROM t_personal
    WHERE IdPersonal = :id
";

$stmt = $Conexion->prepare($sql);
$stmt->bindParam(':id', $IdPersonal, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Empleado no encontrado');
}

$nombre = $data['NombreCompleto'];
$id     = $data['IdPersonal'];
$codigo = $data['CodigoQR'];
$noempleado = $data['NoEmpleado'];

$optionsQR = new QROptions([
    'eccLevel'    => QRCode::ECC_H,
    'scale'       => 20,
    'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
    'imageBase64' => true,
]);

$qr = new QRCode($optionsQR);
$qrImg = $qr->render($codigo);

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->setPaper([0, 0, 595, 963], 'portrait');

$HeaderImg = "../../dist/img/LogoAlpasaC.png";

$base64Logo = "";
if (file_exists($HeaderImg)) {
    $imageData = file_get_contents($HeaderImg);
    $base64Logo = 'data:' . mime_content_type($HeaderImg) . ';base64,' . base64_encode($imageData);
}

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0px; }
        body {
            margin: 0px;
            font-family: "Helvetica", sans-serif;
            width: 100%;
            height: 100%;
        }
        .header {
            background-color: #d9530f;
            height: 150px;
            width: 100%;
            position: absolute;
            text-align: center;
            padding-top: 25px; 
        }
        .logo-img {
            padding-top: 10px;
            width: 600px;
        }
        .content {
            text-align: center;
            padding-top: 80px;
            height: 500px;
        }
        .qr-img {
            padding-top: 100px;
            width: 900px;
        }
        .footer {
            background-color: #d9530f;
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 200px;
            color: white;
            text-align: center;
        }
        .footer-content {
            padding-top: 60px;
        }
        .name {
            font-size: 25pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .id {
            font-size: 25pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="header">
        <img src="'.$base64Logo.'" class="logo-img" />
    </div>
    <div class="content">
        <img src="'.$qrImg.'" class="qr-img">
    </div>
    <div class="footer">
        <div class="footer-content">
            <div class="name">'.$nombre.'</div>
            <div class="id">N° Empleado: '.$noempleado.'</div>
        </div>
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->render();

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="credencial_'.$id.'.pdf"');
header('Content-Length: ' . strlen($dompdf->output()));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
header('Expires: 0');

echo $dompdf->output();
exit;