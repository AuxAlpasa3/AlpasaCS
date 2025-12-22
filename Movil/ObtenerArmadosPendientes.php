<?php
header('Content-Type: application/json;  charset=UTF-8');

include '../api/db/conexion.php';
$sentencia = $Conexion->query("SELECT distinct(NvoCodBarras) as Nuevo ,t3.NombreCliente, IdArmado as IdArmado, t1.Estatus,
CONVERT(varchar, FechaArmado, 103) AS FechaArmado, t2.NombreColaborador as Armador,
t4.Ubicacion, t4.IdUbicacion
From t_armado as t1 
LEFT JOIN t_usuario as t2 on t1.Armador=t2.IdUsuario
LEFT JOIN t_cliente as t3 on t1.IdCliente=t3.IdCliente 
LEFT JOIN t_ubicacion t4 on t1.IdUbicacion = t4.IdUbicacion
WHERE t1.estatus in(0,1,2,3) ;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if (!$Query) {
  $Query = "No hay armados.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>