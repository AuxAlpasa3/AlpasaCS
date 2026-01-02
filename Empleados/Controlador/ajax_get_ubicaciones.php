<?php
include '../../api/db/conexion.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
                IdUbicacion as id, 
                NomCorto as nombre 
            FROM t_ubicacion 
            ORDER BY NomCorto";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    $ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Agregar opción para "Sin Ubicación" si no existe
    $sinUbicacionExiste = false;
    foreach ($ubicaciones as $ubicacion) {
        if ($ubicacion['nombre'] == 'SinUbicacion') {
            $sinUbicacionExiste = true;
            break;
        }
    }
    
    if (!$sinUbicacionExiste) {
        array_unshift($ubicaciones, ['id' => '0', 'nombre' => 'SinUbicacion']);
    }
    
    echo json_encode($ubicaciones, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar ubicaciones: ' . $e->getMessage()]);
}
?>