<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

try {
        $query = "SELECT DISTINCT 
            CASE 
                WHEN t1.Cargo IS NULL THEN 'Sin Limite de Acceso'
                ELSE t2.NomLargo 
            END AS Cargo
    FROM t_personal_externo as t1 
    INNER JOIN t_ubicacion_interna as t2 ON t1.AreaVisita = t2.IdUbicacion
    ORDER BY NomLargo ASC";
        
    $stmt = $Conexion->query($query);
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [['id' => '', 'nombre' => 'Todas']];
    foreach($areas as $area) {
        $data[] = [
            'id' => $area['nombre'],
            'nombre' => $area['nombre']
        ];
    }
    
    echo json_encode($data);
    
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error al cargar áreas de visita']);
}
?>