<?php
include '../../api/db/conexion.php';
require_once '../librerias/PHPMailer/PHPMailer.php';
require_once '../librerias/PHPMailer/SMTP.php';
require_once '../librerias/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$IdVisita = $_POST['IdVisita'] ?? 0;

try {
    $db = new Database();
    $Conexion = $db->getConnection();
    
    // Obtener datos de la visita
    $sql = "SELECT v.*, p.NombreProveedor, p.Email as EmailProveedor,
                   CONCAT(pp.Nombre, ' ', pp.ApPaterno) as NombrePersonal,
                   a.NombreArea
            FROM visitas_proveedores v
            LEFT JOIN proveedores p ON v.IdProveedor = p.IdProveedor
            LEFT JOIN proveedor_personal pp ON v.IdProveedorPersonal = pp.IdProveedorPersonal
            LEFT JOIN areas a ON v.IdDepartamento = a.IdDepartamento
            WHERE v.IdVisita = :IdVisita";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->bindParam(':IdVisita', $IdVisita, PDO::PARAM_INT);
    $stmt->execute();
    
    $visita = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$visita || !$visita['EmailProveedor']) {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró email del proveedor'
        ]);
        return;
    }
    
    // Generar QR como imagen
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . 
             urlencode($visita['QrCode']);
    
    // Configurar PHPMailer
    $mail = new PHPMailer(true);
    
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Cambiar por tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'tu_email@gmail.com'; // Cambiar por tu email
    $mail->Password = 'tu_password'; // Cambiar por tu contraseña
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Destinatarios
    $mail->setFrom('sistema@empresa.com', 'Sistema de Visitas');
    $mail->addAddress($visita['EmailProveedor'], $visita['NombreProveedor']);
    
    // Contenido del email
    $mail->isHTML(true);
    $mail->Subject = 'QR de Acceso - Visita Programada';
    
    $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #d94f00; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; border: 1px solid #ddd; }
                .qr-container { text-align: center; margin: 20px 0; }
                .info { margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>QR de Acceso - Sistema de Visitas</h2>
                </div>
                <div class="content">
                    <h3>Detalles de la visita:</h3>
                    <div class="info"><strong>Proveedor:</strong> ' . $visita['NombreProveedor'] . '</div>
                    <div class="info"><strong>Personal:</strong> ' . $visita['NombrePersonal'] . '</div>
                    <div class="info"><strong>Área:</strong> ' . $visita['NombreArea'] . '</div>
                    <div class="info"><strong>Fecha:</strong> ' . $visita['FechaVisita'] . '</div>
                    <div class="info"><strong>Hora:</strong> ' . $visita['HoraVisita'] . '</div>
                    <div class="info"><strong>Motivo:</strong> ' . $visita['Motivo'] . '</div>
                    
                    <div class="qr-container">
                        <h4>Código QR de acceso:</h4>
                        <img src="' . $qrUrl . '" alt="QR Code" width="200" height="200">
                        <p><small>Presente este QR en la entrada</small></p>
                    </div>
                    
                    <div class="info">
                        <strong>Instrucciones:</strong>
                        <ul>
                            <li>Presente este QR en la entrada principal</li>
                            <li>El QR es válido hasta: ' . date('H:i', strtotime($visita['FechaExpiracion'])) . '</li>
                            <li>Mantenga este correo para futuras referencias</li>
                        </ul>
                    </div>
                </div>
            </div>
        </body>
        </html>
    ';
    
    $mail->AltBody = "QR de acceso para " . $visita['NombreProveedor'] . 
                     "\nPersonal: " . $visita['NombrePersonal'] . 
                     "\nFecha: " . $visita['FechaVisita'] . 
                     "\nHora: " . $visita['HoraVisita'] . 
                     "\nCódigo QR: " . $visita['QrCode'];
    
    if ($mail->send()) {
        // Actualizar fecha de envío
        $sqlUpdate = "UPDATE visitas_proveedores SET FechaEnvioQR = NOW() WHERE IdVisita = :IdVisita";
        $stmtUpdate = $Conexion->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':IdVisita', $IdVisita, PDO::PARAM_INT);
        $stmtUpdate->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'QR enviado exitosamente a ' . $visita['EmailProveedor']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al enviar email: ' . $mail->ErrorInfo
        ]);
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>