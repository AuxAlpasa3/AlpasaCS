<?php
header('Content-Type: application/json;  charset=UTF-8');

include '../api/db/conexion.php';
$IdRemision = $_GET['IdRemision'];
$IdAlmacen = $_GET['IdAlmacen'];


$sentencia = $Conexion->query("SELECT t1.Comentarios,t1.IdTarja AS IdTarja, t1.IdTarja as IdTarjaNum ,t1.CodBarras AS CodBarras,t1.CodBarras as CodBarrasNum, CONVERT(DATE,t1.FechaIngreso) as FechaIngreso, t1.FechaProduccion,t1.IdArticulo,t2.MaterialNo, trim(Concat(t2.Material,' ',t2.Shape)) as MaterialShape, t1.Piezas,t1.NumPedido,t1.NetWeight,t1.GrossWeight,t1.IdUbicacion,t3.Ubicacion,STUFF(( SELECT ', ' + tem.EstadoMaterial FROM STRING_SPLIT(t1.EstadoMercancia, ',') estado INNER JOIN t_estadoMaterial tem ON CAST(estado.value AS INT) = tem.IdEstadoMaterial FOR XML PATH('') ), 1, 2, '') as EstadoMercancia,t1.EstadoMercancia as EstadosIds,t1.PaisOrigen,t1.Origen,t1.NoTarima,t1.Cliente,t4.NombreCliente,t1.IdRemision,t1.IdLinea,t1.Transportista,t1.Placas,
                                 t1.Chofer,t1.Checador,t1.Supervisor, t1.Completado,t7.NumRecinto,t1.Almacen,t1.Alto,t1.Ancho,t1.Largo,t8.Contenedor,t8.Caja,t8.Sellos,t8.Tracto
                                FROM t_ingreso as t1 
                                INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
                                LEFT JOIN t_ubicacion as t3 on t1.IdUbicacion=t3.IdUbicacion
                                INNER JOIN t_cliente as t4 on t1.Cliente=t4.IdCliente
                                INNER JOIN t_almacen as t7 on t1.Almacen=t7.IdAlmacen
								INNER JOIN t_remision_encabezado as t8 on t1.IdRemision=t8.IdRemisionEncabezado
                                WHERE t1.ESTATUS IN (0,1,2) 
                                AND t1.IdRemision =$IdRemision and t7.IdAlmacen=$IdAlmacen
                                order by t1.IdRemision, t1.CodBarras");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if (!$Query) {
     $Query = "No hay ingresos pendientes.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>