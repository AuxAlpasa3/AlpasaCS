<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
       $sql = "SELECT 
                DISTINCT(IdPersonal) as id, 
                CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) as nombre,
                t1.RutaFoto as Foto,
                IdPersonal as codigo
            FROM t_personal as t1 INNER JOIN
            regentsalper as t2 on t1.IdPersonal=T2.IdPer
            WHERE Status = 1 ";

    $stmt = $Conexion->prepare($sql);
    $stmt->execute();
    $personal = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($personal);
    
} catch (PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Error al cargar el personal: ' . $e->getMessage()
    ]);
}
?>