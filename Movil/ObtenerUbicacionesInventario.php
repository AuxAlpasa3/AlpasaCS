<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';

$MaterialNo = $_GET['MaterialNo'] ?? null;
$MaterialNo = ($MaterialNo === 'undefined' || $MaterialNo === '') ? null : $MaterialNo;

$IdUbicacion = $_GET['IdUbicacion'] ?? null;
$IdUbicacion = ($IdUbicacion === 'undefined' || $IdUbicacion === '') ? null : $IdUbicacion;

$IdCliente = $_GET['IdCliente'] ?? null;
$IdCliente = ($IdCliente === 'undefined' || $IdCliente === '') ? null : $IdCliente;

$IdAlmacen = $_GET['IdAlmacen'] ?? null;
$IdAlmacen = ($IdAlmacen === 'undefined' || $IdAlmacen === '') ? null : $IdAlmacen;

$params = [];
$sql = "SELECT t2.Ubicacion,t2.IdUbicacion , Count(t1.CodBarras) as Total from t_inventario as t1 
inner join t_ubicacion as t2 on t1.IdUbicacion=t2.IdUbicacion 
where t1.Piezas > 0 and t1.EnProceso=0 and t1.Almacen=$IdAlmacen";

if ($MaterialNo !=null){
  $sql .= ' AND t1.MaterialNo = :MaterialNo';
  $params[':MaterialNo'] = $MaterialNo;
}

if ($IdUbicacion !=null){
  $sql .= ' AND t1.IdUbicacion = :IdUbicacion';
  $params[':IdUbicacion'] = $IdUbicacion;
}

if ($IdCliente !=null){
  $sql .= ' AND t1.Cliente = :IdCliente';
  $params[':IdCliente'] = $IdCliente;
}

$sql .= ' GROUP BY t2.IdUbicacion , t2.Ubicacion order by t2.Ubicacion;';

$sentencia = $Conexion->prepare($sql);
$sentencia->execute($params);
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);


if(!$Query)
{
  $Query="No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>