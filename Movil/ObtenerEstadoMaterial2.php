<?php
header('Content-Type: application/json; charset=UTF-8');
include '../api/db/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);
$IdUsuario = isset($data['IdUsuario']) ? $data['IdUsuario'] : '';
$IdAlmacen = isset($data['IdAlmacen']) ? $data['IdAlmacen'] : '';

$sentencia = $Conexion->query("SELECT 
    IdEstadoMaterial,
    EstadoMaterial
FROM t_estadoMaterial
ORDER BY EstadoMaterial");

$datos = $sentencia->fetchAll(PDO::FETCH_OBJ);

if ($datos) {
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
} else {
    echo json_encode(
        array(),
        JSON_UNESCAPED_UNICODE
    );
}
?>