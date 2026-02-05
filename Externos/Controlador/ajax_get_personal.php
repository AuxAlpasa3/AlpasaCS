<?php
include '../../api/db/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
       $sql = "SELECT 
                DISTINCT(IdPersonalExterno) as id, 
                CONCAT(Nombre, ' ', ApPaterno, ' ', ApMaterno) as nombre,
                t1.RutaFoto as Foto,
                IdPersonalExterno as codigo
            FROM t_personal_externo as t1 INNER JOIN
            regentsalext as t2 on t1.IdPersonalExterno=T2.IdExt
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