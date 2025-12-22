<?php
header('Content-Type: application/json;  charset=UTF-8');
Include '../api/db/conexion.php';

$IdRevision = $_GET['IdRevision'];
$sentencia = $Conexion->query("SELECT t1.IdRevision,t1.IdUbicacion,t2.Ubicacion FROM t_revisionUbicaciones t1
LEFT JOIN t_ubicacion t2 on t1.IdUbicacion = t2.IdUbicacion
WHERE t1.IdRevision = $IdRevision;");
$datos = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$datos)
{
  $datos="No hay revisiones.";
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);

?>