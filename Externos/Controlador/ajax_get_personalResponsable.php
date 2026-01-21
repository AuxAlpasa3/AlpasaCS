<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json');

try {
        $query = "SELECT t1.IdPersonal as id, 
                        CONCAT(t1.Nombre, ' ', t1.ApPaterno, ' ', t1.ApMaterno) as nombre
                FROM t_personal t1 inner join t_personal_externo as t2 
                on t1.IdPersonal=t2.IdPersonalResponsable
                WHERE t1.Status = 1
                ORDER BY t1.Nombre  ASC";
    
    $stmt = $Conexion->query($query);
    $responsables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [['id' => '', 'nombre' => 'Todos']];
    foreach($responsables as $responsable) {
        $data[] = [
            'id' => $responsable['id'],
            'nombre' => $responsable['nombre']
        ];
    }
    
    echo json_encode($data);
    
} catch(PDOException $e) {
    echo json_encode(['error' => 'Error al cargar personal responsable']);
}
?>