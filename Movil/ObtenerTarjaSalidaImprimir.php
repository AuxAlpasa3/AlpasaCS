<?php
header('Content-Type: application/json;  charset=UTF-8');

include '../api/db/conexion.php';
$IdUsuario = $_GET['IdUsuario'];
$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT distinct TOP 50 (Idtarja) as IdTarja,IdTarja as IdTarjaNum,t2.NombreCliente , t4.Almacen  ,t4.NumRecinto,t4.IdAlmacen
from t_Salida AS t1 
INNER JOIN dbo.t_cliente AS t2 ON t1.Cliente=t2.IdCliente 
INNER JOIN t_usuario_almacen as t3 on t1.Almacen=t3.IdAlmacen
INNER JOIN t_almacen as t4 on t1.Almacen=t4.IdAlmacen
where t3.IdUsuario=$IdUsuario and t3.IdAlmacen=$IdAlmacen order by IdTarja desc;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if (!$Query) {
    $Query = "No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>