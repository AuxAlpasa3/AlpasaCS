<?php
header('Content-Type: application/json;  charset=UTF-8');
include '../api/db/conexion.php';

$IdUsuario = $_GET['IdUsuario'];
$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT t1.IdRemision, t1.IdRemisionEncabezado, t2.IdCliente, t2.NombreCliente, t1.Transportista, t1.Placas, t1.Chofer, t1.cantidad, t1.Almacen as IdAlmacen, t5.Almacen, 
            (SELECT STRING_AGG(ISNULL(Booking, 'S/BK'), ', ') FROM (SELECT DISTINCT Booking FROM t_remision_linea WHERE IdRemisionEncabezadoRef = t1.IdRemisionEncabezado) AS distinct_bookings) as Booking
            FROM t_remision_encabezado AS t1  
            INNER JOIN t_cliente AS t2 ON t1.Cliente = t2.IdCliente 
            INNER JOIN t_usuario_almacen AS t3 ON t1.Almacen = t3.IdAlmacen
            INNER JOIN t_almacen AS t5  ON t1.Almacen = t5.IdAlmacen
            where Estatus in(0,1) and tipoRemision=1 AND t3.IdUsuario=$IdUsuario AND T3.IdAlmacen=$IdAlmacen
            GROUP BY t1.IdRemision, t1.IdRemisionEncabezado, t2.IdCliente, t2.NombreCliente, t1.Transportista,
             t1.Placas, t1.Chofer, t1.cantidad, t1.Almacen, t5.Almacen;");
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
            'IdRemisionEncabezado' => $row->IdRemisionEncabezado,
            'Booking' => $row->Booking
        ]);
    }
} else {
    $datos = "No hay remisiones pendientes para Ingreso.";
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE);