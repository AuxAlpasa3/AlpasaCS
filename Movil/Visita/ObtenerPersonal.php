<?php
// obtenerPersonal.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../../api/db/conexion.php';

$idUbicacion = isset($_GET['idubicacion']) ? $_GET['idubicacion'] : '';

if (empty($idUbicacion)) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de ubicación no proporcionado'
    ]);
    exit;
}
try {
    $query = "SELECT 
                t1.IdPersonal,
                t1.Nombre,
                t1.ApPaterno,
                t1.ApMaterno,
                CONCAT(t1.Nombre, ' ', t1.ApPaterno, ' ', t1.ApMaterno) as NombreCompleto
              FROM t_personal as t1
              INNER JOIN regentsalper as t2 on t1.IdPersonal= t2.IdPer
              WHERE t2.IdUbicacion = 2 and t2.FechaSalida is null
              AND t1.Status = :idUbicacion 
              ORDER BY t1.Nombre, t1.ApPaterno";

    $stmt = $Conexion->prepare($query);
    $stmt->bindParam(':idUbicacion', $idUbicacion);
    $stmt->execute();
    
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $personal,
        'ubicacion_id' => $idUbicacion,
        'total' => count($personal)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>