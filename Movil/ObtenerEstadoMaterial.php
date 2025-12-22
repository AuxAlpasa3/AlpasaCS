<?php
header('Content-Type: application/json; charset=UTF-8');
include '../api/db/conexion.php';

// Leer el contenido JSON del cuerpo de la solicitud
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

// Obtener los valores del JSON
$Almacen = isset($data['IdAlmacen']) ? $data['IdAlmacen'] : '';
$IdUsuario = isset($data['IdUsuario']) ? $data['IdUsuario'] : '';

// Validar que los datos necesarios estén presentes
if (empty($Almacen) || empty($IdUsuario)) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan parámetros requeridos']);
    exit;
}

try {
    $sentencia = $Conexion->prepare("SELECT IdEstadoMaterial, EstadoMaterial 
                                    FROM t_estadoMaterial as t1
                                    INNER JOIN t_usuario_almacen as t2 ON t1.Almacen = t2.IdAlmacen
                                    WHERE t1.Almacen = :almacen AND t2.IdUsuario = :idUsuario");
    
    $sentencia->bindParam(':almacen', $Almacen, PDO::PARAM_STR);
    $sentencia->bindParam(':idUsuario', $IdUsuario, PDO::PARAM_STR);
    $sentencia->execute();
    
    $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if (empty($Query)) {
        $Query = ["message" => "No hay información"];
    }
    
    echo json_encode($Query, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>