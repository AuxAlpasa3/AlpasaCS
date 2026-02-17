<?php
require_once '../../vendor/autoload.php';
include '../../api/db/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (!isset($_GET['id'])) {
    die('IdPersonalExterno requerido');
}

$IdPersonalExterno = (int)$_GET['id'];
$sql = "
    SELECT 
       IdPersonalExterno,
       NumeroIdentificacion,
       CONCAT(Nombre,' ',ApPaterno,' ',ApMaterno) AS NombreCompleto,
       CONCAT('02_0',IdPersonalExterno) AS CodigoQR
   FROM t_personal_Externo
   WHERE IdPersonalExterno = :id
";

$stmt = $Conexion->prepare($sql);
$stmt->bindParam(':id', $IdPersonalExterno, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Empleado no encontrado');
}

$nombre = $data['NombreCompleto'];
$id     = $data['IdPersonalExterno'];
$codigo = $data['CodigoQR'];
$numeroidentificacion = $data['NumeroIdentificacion'];

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
$dompdf->setPaper([0, 0, 595, 963], 'portrait'); // 210mm x 340mm

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
            <div class="id">N° IDENTIFICACIÓN: '.$numeroidentificacion.'</div>
        </div>
    </div>

</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->render();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="credencial_'.$id.'.pdf"');
echo $dompdf->output();
exit;