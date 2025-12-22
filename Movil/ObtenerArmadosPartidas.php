<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';


 $IdArmado = (!empty($_GET['IdArmado']))   ?  $_GET['IdArmado']: NULL;
$sentencia = $Conexion->query("
Select t1.IdArmado,t4.NombreCliente,t1.IdLinea,t2.MaterialNo, Concat(t2.Material,' ',t2.Shape) as Articulo, t1.Piezas, t1.Piezas-sum(isnull(t3.Piezas,0)) as Faltan,sum(isnull(t3.Piezas,0)) as totales
From t_armadoNvo as t1
INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
LEFT JOIN t_pasoSalida as t3 on t1.IdArmado=t3.IdArmado and t1.IdLinea=t3.IdLinea
LEFT JOIN t_cliente as t4 on t1.IdCliente = t4.IdCliente
WHERE t1.IdArmado =$IdArmado group by t1.IdArmado,t1.IdLinea,t2.MaterialNo,t2.Material,t2.Shape, t1.Piezas, NombreCliente;");

$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay armados (partidas).";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>