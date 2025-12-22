<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT distinct tc.NombreCliente, tc.IdCliente from t_inventario ti 
 join t_cliente tc on ti.Cliente = tc.IdCliente 
 inner join t_cliente_almacen as t1 on t1.IdCliente=Tc.IdCliente
 where t1.IdAlmacen=$IdAlmacen order by tc.NombreCliente;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>