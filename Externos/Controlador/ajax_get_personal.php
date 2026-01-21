<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
                IdPersonalExterno as id, 
                CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) as nombre,
                NumeroIdentificacion as codigo
            FROM t_personal_externo 
            WHERE Status = 1 
            ORDER BY Nombre, ApPaterno, ApMaterno";
    
    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $resultado = array();
    foreach ($personal as $p) {
        $resultado[] = [
            'id' => $p['id'],
            'nombre' => $p['nombre'],
            'codigo' => $p['codigo']
        ];
    }
    
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al cargar personal: ' . $e->getMessage()]);
} finally {
    $Conexion = null;
}
?>