<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once '../api/db/conexion.php';

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data)) {
        $data = $_POST;
    }
    
    $required_fields = ['IdPersonal', 'IdUbicacion', 'IdUsuario'];
    foreach ($required_fields as $field) {
        if (empty($data[$field])) {
            throw new Exception("Campo requerido: $field");
        }
    }
    
    $idPersonal = $data['IdPersonal'];
    $idUbicacion = $data['IdUbicacion'];
    $idUsuario = $data['IdUsuario'];
    
    
    $query = "
        SELECT 
            ri.IdRegistro,
            ri.Fecha,
            ri.HoraEntrada,
            ri.IdPersonal,
            ri.IdUbicacion,
            u.NombreUbicacion,
            p.NombreCompleto
        FROM RegistrosIngreso ri
        INNER JOIN Ubicaciones u ON ri.IdUbicacion = u.IdUbicacion
        INNER JOIN Personal p ON ri.IdPersonal = p.IdPersonal
        WHERE ri.IdPersonal = :idPersonal
        AND ri.IdUbicacion = :idUbicacion
        AND ri.HoraSalida IS NULL
        AND ri.Estado = 'ACTIVO'
        ORDER BY ri.Fecha DESC, ri.HoraEntrada DESC
        LIMIT 1
    ";
    
    $stmt = $Conexion->prepare($query);
    $stmt->bindParam(':idPersonal', $idPersonal);
    $stmt->bindParam(':idUbicacion', $idUbicacion);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response['success'] = true;
        $response['message'] = 'Ingreso pendiente encontrado';
        $response['data'] = [
            'IdRegistro' => $row['IdRegistro'],
            'Fecha' => $row['Fecha'],
            'HoraEntrada' => $row['HoraEntrada'],
            'NombreUbicacion' => $row['NombreUbicacion'],
            'NombreCompleto' => $row['NombreCompleto']
        ];
    } else {
        $response['success'] = true;
        $response['message'] = 'No hay ingreso pendiente en esta ubicación';
        $response['data'] = null;
    }
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
} catch (PDOException $e) {
    $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
}

echo json_encode($response);
?>