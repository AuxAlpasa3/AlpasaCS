<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $idRegistroIngreso = $data['IdRegistroIngreso'] ?? '';
    $idPersonal = $data['IdPersonal'] ?? '';
    $idUbicacion = $data['IdUbicacion'] ?? '';
    $idUsuario = $data['IdUsuario'] ?? '';
    $usuario = $data['Usuario'] ?? '';
    $observaciones = $data['Observaciones'] ?? 'NULL';
    $notificarSupervisor = $data['NotificarSupervisor'] ?? false;
    $fotos = $data['fotos'] ?? [];

    if (empty($idRegistroIngreso) || empty($idPersonal) || empty($idUbicacion) || empty($idUsuario)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos requeridos'
        ]);
        exit;
    }

    $conn->begin_transaction();

    try {
        $fechaActual = date('Y-m-d H:i:s');
        
        $folioMovSal = 'SAL-' . date('YmdHis') . '-' . $idPersonal;

        $queryInsert = "INSERT INTO regsalper (IdPer, IdUsuario, FechaHora, Observaciones, Tipo, IdUbicacion, Folio) 
                        VALUES (?, ?, ?, ?, 'SALIDA', ?, ?)";
        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->bind_param("iissss", $idPersonal, $idUsuario, $fechaActual, $observaciones, $idUbicacion, $folioMovSal);
        $stmtInsert->execute();
        $idRegSalida = $conn->insert_id;

        $queryUpdate = "UPDATE regentsalper SET FechaSalida = ?, FolMovSal = ?, StatusRegistro = 0 
                        WHERE IdMovEnTSal = ? AND IdPer = ? AND IdUbicacion = ?";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->bind_param("ssiii", $fechaActual, $folioMovSal, $idRegistroIngreso, $idPersonal, $idUbicacion);
        $stmtUpdate->execute();

        foreach ($fotos as $index => $fotoBase64) {
            $nombreFoto = "SALIDA_{$idPersonal}_" . date('YmdHis') . "_" . ($index + 1) . ".jpg";
            $rutaFoto = "../fotos_salidas/" . $nombreFoto;
            
            $fotoData = base64_decode($fotoBase64);
            file_put_contents($rutaFoto, $fotoData);
            
            $queryFoto = "INSERT INTO fotossalidas (IdRegSalida, NombreFoto, RutaFoto, FechaSubida) 
                          VALUES (?, ?, ?, ?)";
            $stmtFoto = $conn->prepare($queryFoto);
            $stmtFoto->bind_param("isss", $idRegSalida, $nombreFoto, $rutaFoto, $fechaActual);
            $stmtFoto->execute();
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Salida registrada correctamente',
            'data' => [
                'IdRegSalida' => $idRegSalida,
                'FolMovSal' => $folioMovSal
            ]
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar salida: ' . $e->getMessage()
        ]);
    }

} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}

$conn->close();
?>