<?php
header('Content-Type: application/json;  charset=UTF-8');

$IdUsuario = $_GET['IdUsuario'];
$IdAlmacen = $_GET['IdAlmacen'];

include '../api/db/conexion.php';
$sentencia = $Conexion->query("SELECT t1.IdRemision,t1.IdRemisionEncabezado,t2.IdCliente, t2.NombreCliente, t1.Transportista,
            t1.Placas,t1.Chofer,t1.cantidad,t1.Almacen as IdAlmacen,t5.Almacen
            FROM t_remision_encabezado AS t1 
            INNER JOIN t_cliente AS t2 on t1.Cliente=t2.IdCliente 
            INNER JOIN t_usuario_almacen as t3 on t1.Almacen=t3.IdAlmacen
            INNER JOIN t_almacen as t5 on t1.Almacen=t5.IdAlmacen
            where Estatus in(0,1) and tipoRemision=2 AND t3.IdUsuario=$IdUsuario AND T3.IdAlmacen=$IdAlmacen;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if (!$Query) {
    $Query = "No hay salidas.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);