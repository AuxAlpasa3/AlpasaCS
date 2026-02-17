<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../api/db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPersonal = $_POST['IdPersonal'] ?? '';
    $idUbicacion = $_POST['IdUbicacion'] ?? '';
    $idUsuario = $_POST['IdUsuario'] ?? '';

    if (empty($idPersonal) || empty($idUbicacion) || empty($idUsuario)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos requeridos'
        ]);
        exit;
    }

    try {
        $query = "SELECT IdMovEnTSal FROM regentsalper 
                  WHERE IdPer = ? AND IdUbicacion = ? AND StatusRegistro = 1 AND FechaSalida IS NULL";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $idPersonal, $idUbicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'IdMovEnTSal' => $row['IdMovEnTSal']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No hay ingreso pendiente'
            ]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la consulta: ' . $e->getMessage()
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