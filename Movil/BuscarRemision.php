<?php
header('Content-Type: application/json;  charset=UTF-8');
include '../api/db/conexion.php';

$IdUsuario = $_GET['IdUsuario'];
$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT t1.IdRemision, t1.IdRemisionEncabezado, t2.IdCliente, t2.NombreCliente, t1.Transportista, t1.Placas, t1.Chofer, t1.cantidad,
    t1.Almacen as IdAlmacen, t5.Almacen,
    t4.IdUsuario as IdUsuarioAsignado
FROM t_remision_encabezado AS t1 
INNER JOIN t_cliente AS t2 ON t1.Cliente = t2.IdCliente 
INNER JOIN t_almacen as t5 ON t1.Almacen = t5.IdAlmacen
LEFT JOIN dbo.t_remisionAsignada AS t4 ON t1.IdRemisionEncabezado = t4.IdRemision
WHERE t1.Estatus IN (0,1) 
    AND t1.tipoRemision = 1 
    AND t1.Almacen = $IdAlmacen
    AND (t4.IdUsuario IS NULL OR t4.IdUsuario = $IdUsuario);");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if ($Query) {
	$datos = [];
	foreach ($Query as $row) {
		array_push($datos, [
			'IdRemision' => $row->IdRemision,
			'Cliente' => $row->NombreCliente,
			'IdCliente' => $row->IdCliente,
			'IdAlmacen' => $row->IdAlmacen,
			'Almacen' => $row->Almacen,
			'IdRemisionEncabezado' => $row->IdRemisionEncabezado
		]);
	}
} else {
	$datos = "No hay remisiones pendientes para Ingreso.";
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);