<?php
header('Content-Type: aplication/json;  charset=UTF-8');

Include '../api/db/conexion.php';

$IdRemisionAgrupada=$_GET['IdRemisionAgrupada'];
$IdRemisionEncabezado=$_GET['IdRemisionEncabezado'];
$Almacen=$_GET['Almacen'];

$sentencia = $Conexion->query("SELECT CASE WHEN isnull(max(t1.IdLinea),0)=0 then 1 else Max(t1.IdLinea)+1 end as IdLinea 
FROM t_remision_linea as t1
INNER JOIN t_remision_encabezado AS t2 on t1.IdRemisionEncabezadoRef=t2.IdRemisionEncabezado
WHERE t2.IdRemisionAgrupada=$IdRemisionAgrupada and t1.IdRemisionEncabezadoRef=$IdRemisionEncabezado and t1.Almacen=$Almacen ");
$Query = $sentencia->fetch(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>