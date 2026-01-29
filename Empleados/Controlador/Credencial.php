<?php
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Arial');

$dompdf = new Dompdf($options);

$foto_base64 = 'data:image/svg+xml;base64,' . base64_encode('
<svg width="100" height="120" xmlns="http://www.w3.org/2000/svg">
    <rect width="100" height="120" fill="#ecf0f1"/>
    <circle cx="50" cy="50" r="30" fill="#bdc3c7"/>
    <rect x="30" y="85" width="40" height="30" fill="#bdc3c7" rx="5"/>
    <text x="50" y="120" text-anchor="middle" font-family="Arial" font-size="12" fill="#7f8c8d">FOTO</text>
</svg>');

$html = '
<!DOCTYPE html>
<html>
<body>
    <div style="width: 300px; height: 200px; border: 1px solid #000; padding: 10px;">
        <div style="text-align: center; margin-bottom: 10px;">
            <h2 style="margin: 0;">Credencial</h2>
        </div>
        
        <div style="display: flex;">
            <div style="width: 100px;">
                <img src="' . $foto_base64 . '" style="width: 80px; height: 100px; border: 1px solid #ccc;">
            </div>
            
            <div style="flex: 1;">
                <p><strong>Nombre:</strong> Juan Pérez</p>
                <p><strong>ID:</strong> 12345</p>
                <p><strong>Cargo:</strong> Desarrollador</p>
                <p><strong>Fecha:</strong> ' . date('d/m/Y') . '</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 20px; font-size: 10px;">
            <hr>
            Documento oficial - Válido con foto
        </div>
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper(array(0, 0, 300, 200));
$dompdf->render();

header('Content-Type: application/pdf');
echo $dompdf->output();
?>