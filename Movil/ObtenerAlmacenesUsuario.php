<?php
header('Content-Type: application/json; charset=UTF-8');
include '../api/db/conexion.php';

$IdUsuario = isset($_GET['IdUsuario']) ? $_GET['IdUsuario'] : '';

if (empty($IdUsuario)) {
    echo json_encode(['error' => 'IdUsuario es requerido']);
    exit;
}

try {
    $sentencia = $Conexion->prepare("SELECT t1.IdAlmacen, t1.Almacen
                FROM t_almacen AS t1 
                INNER JOIN t_usuario_almacen as t2 ON t1.IdAlmacen = t2.IdAlmacen
                WHERE t2.IdUsuario = :IdUsuario");
    
    $sentencia->bindParam(':IdUsuario', $IdUsuario, PDO::PARAM_STR);
    $sentencia->execute();
    
    $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if ($Query && count($Query) > 0) {
        $datos = [];
        foreach ($Query as $row) {
            array_push($datos, [
                'IdAlmacen' => $row->IdAlmacen,
                'NombreAlmacen' => $row->Almacen
            ]);
        }
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['mensaje' => 'No hay almacenes asignados para este usuario.'], JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>