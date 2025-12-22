<?php
  include ('../api/db/conexion.php');
try {
    $sentUbicaciones = $Conexion->query("SELECT NombreColaborador, IdUsuario FROM dbo.t_usuario WHERE TipoUsuario NOT IN(1,6,4) and estatus=1");
    $ubicaciones = $sentUbicaciones->fetchAll(PDO::FETCH_OBJ);
    
    header('Content-Type: application/json');
    echo json_encode($ubicaciones);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>