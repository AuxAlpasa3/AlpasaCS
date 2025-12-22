<?php
header('Content-Type: aplication/json;  charset=UTF-8');

Include '../api/db/conexion.php';


$Almacen=$_GET['Almacen'];

$sentencia = $Conexion->query("SELECT t1.IdArticulo, t1.MaterialNo, CONCAT(t1.Material,' ', t1.Shape) AS MaterialShape FROM t_articulo as t1
inner join t_articulo_almacen as t2 on t1.IdArticulo=t2.IdArticulo
where  t2.IdAlmacen=$Almacen
ORDER BY MaterialNo;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>