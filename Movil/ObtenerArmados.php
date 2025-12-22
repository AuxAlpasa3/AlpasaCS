<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
$sentencia = $Conexion->query("SELECT distinct(t1.IdArmado) as IdArmado,t1.IdCliente ,t5.NombreCliente , t3.NombreColaborador,t4.Estatus
FROM t_armadoNvo as t1 
INNER JOIN t_usuario as t3 on t1.Supervisor=t3.IdUsuario
INNER JOIN t_estatusrem as t4 on t1.Estatus=t4.IdEstatus 
LEFT JOIN t_cliente t5 on t5.IdCliente = t1.IdCliente
where t1.estatus in(0,1,2,3);");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay armados.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>