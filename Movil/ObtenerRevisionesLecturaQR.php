<?php
header('Content-Type: application/json;  charset=UTF-8');
Include '../api/db/conexion.php';

$IdRevision = $_GET['IdRevision'];
$IdUbicacion = $_GET['IdUbicacion'];

$sentencia = $Conexion->query("select t1.IdRevision, t1.CodBarras ,  t1.IdUbicacion, t2.Estatus as EstatusRevisionUbicacion
FROM t_lecturaQR t1
LEFT JOIN t_revisionUbicaciones t2 ON t1.IdRevision  = t2.IdRevision AND t2.IdUbicacion = t1.IdUbicacion
where t1.IdRevision  = $IdRevision AND t1.IdUbicacion = $IdUbicacion;");
$datos = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$datos)
{
  $datos="No hay revisiones.";
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);

?>