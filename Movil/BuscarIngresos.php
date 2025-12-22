<?php
header('Content-Type: application/json;  charset=UTF-8');
include '../api/db/conexion.php';

$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT t2.IdTarja, t2.Transportista, t2.Placas, t2.Chofer,t2.IdRemision as IdRemisionEncabezado, t3.IdCliente, t3.NombreCliente, t5.IdAlmacen,t5.Almacen, t5.NumRecinto,t7.IdRemision 
	FROM t_ingreso t2
	LEFT JOIN t_remisionAsignada t1 ON t2.IdRemision = t1.IdRemision
	LEFT JOIN t_cliente t3 ON t3.IdCliente = t2.Cliente 
	LEFT JOIN t_almacen t5 ON t2.Almacen = t5.IdAlmacen
	INNER JOIN t_usuario_almacen t6 ON t2.Almacen = t6.IdAlmacen
	INNER JOIN t_remision_encabezado as t7 ON t2.IdRemision = t7.IdRemisionEncabezado
	WHERE t2.Estatus IN (0,1,2) 
    AND t6.IdAlmacen = ? 
    AND (t1.IdRemision IS NULL OR t1.IdUsuario = ?)  
	GROUP BY 
    t2.IdTarja, t2.Transportista, t2.Placas, t2.Chofer, t2.IdRemision, 
    t3.IdCliente, t3.NombreCliente, t1.IdUsuario, t5.Almacen, t5.NumRecinto, 
    t7.IdRemision, t5.IdAlmacen;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if ($Query) {
	$datos = array();
	foreach ($Query as $row) {
		array_push($datos, array(
			'IdTarja' => $row->IdTarja,
			'IdRemision' => $row->IdRemision,
			'IdCliente' => $row->IdCliente,
			'Cliente' => $row->NombreCliente,
			'Transportista' => $row->Transportista,
			'Placas' => $row->Placas,
			'Chofer' => $row->Chofer,
			'Almacen' => $row->Almacen,
			'IdAlmacen' => $row->Almacen,
			'NumRecinto' => $row->NumRecinto,
			'IdRemisionEncabezado' => $row->IdRemisionEncabezado
		));
	}
} else {
	$datos = "No hay ingresos pendientes.";
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);

?>