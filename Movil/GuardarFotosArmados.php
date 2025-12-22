<?php
require_once "../api/db/conexion.php";

header('Content-Type: application/json');

$ZonaHoraria = getenv('ZonaHoraria');
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION');
$fecha = date('Ymd');
$response = ['success' => false, 'message' => ''];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);


    if (isset($data['image']) && isset($data['mime'])) {
        $base64Image = $data['image'];
        $mimeType = $data['mime'];
        $IdArmado = $data['IdArmado'];
        $uploadDir = "C:\\xampp\\htdocs\\" . $RutaLocal . "\\Armados\\Fotografias\\Tarja" . $IdArmado . "\\";
        $URL = "https://intranet.alpasamx.com/" . $RutaLocal . "/Armados/Fotografias/Tarja" . $IdArmado . "/";

        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                $response['message'] = "Error al crear la carpeta en Windows.";
                echo json_encode($response);
                exit;
            }
        }

        // Determinar la extensión del archivo
        $extension = '';
        switch ($mimeType) {
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            // Añade más tipos si es necesario
            default:
                echo json_encode(['error' => 'Tipo de imagen no soportado']);
                exit;
        }
        // Decodificar la imagen base64
        $imageData = base64_decode($base64Image);

        // $sqlGetMax = "SELECT MAX(isnull(IdFoto,0)+1) AS next_id FROM t_fotografias";
        // $stmtGetMax = $Conexion->query($sqlGetMax);
        $nextId = $data['nextId'];//$stmtGetMax->fetch(PDO::FETCH_ASSOC)['next_id'];

        // Construimos el nombre del archivo

        $nombreFoto = "Foto" . $nextId . '_Tarja' . $IdTarja . "." . $extension;

        $fileURL = $uploadDir . $nombreFoto;
        $filePublicURL = $URL . $nombreFoto;

        // Guardar la imagen en el servidor
        if (file_put_contents($fileURL, $imageData)) {
            //INSERTAR Información de fotografia a tabla t_fotografias.

            $sqlUpdate = $Conexion->prepare("INSERT INTO t_fotografias (IdTarja ,NombreFoto ,RutaFoto ,FechaFoto ,Tipo, NextIdFoto) VALUES (?,?,?,?,?,?);");
            $resultado = $sqlUpdate->execute([$IdArmado, $nombreFoto, $filePublicURL, $fecha, 2, $nextId]);
            echo json_encode([
                'success' => true,
                'message' => 'Imagen subida correctamente',
                'filePath' => $filePublicURL
            ]);
        } else {
            echo json_encode(['error' => 'Error al guardar la imagen']);
        }
    } else {
        echo json_encode(['error' => 'Datos de imagen no proporcionados']);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}