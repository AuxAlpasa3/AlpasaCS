<?php
header('Content-Type: application/json;  charset=UTF-8');
Include '../api/db/conexion.php';

$sentencia = $Conexion->query("SELECT t1.IdRevision , t2.IdTipoRevision , t2.TipoRevision
FROM t_Revision t1 
LEFT JOIN t_tipoRevision t2 ON t1.TipoRevision = t2.IdTipoRevision
WHERE t1.Estatus IN(0,1);");
$datos = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$datos)
{
  $datos="No hay revisiones.";
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);

?>