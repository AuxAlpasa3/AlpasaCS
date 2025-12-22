<?php
header('Content-Type: application/json;  charset=UTF-8');
include '../api/db/conexion.php';

//$IdUsuario = $_GET['IdUsuario'];

$sentencia = $Conexion->query("SELECT t1.IdAlmacen,t1.Almacen
			FROM t_almacen AS t1 
			INNER JOIN t_usuario_almacen as t2 on t1.IdAlmacen=t2.IdAlmacen
			;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if ($Query) {
	$datos = [];
	foreach ($Query as $row) {
		array_push($datos, [
			'IdAlmacen' => $row->IdAlmacen,
			'Almacen' => $row->Almacen
		]);
	}
} else {
	$datos = "No hay remisiones pendientes para Ingreso.";
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);