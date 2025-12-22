<?php
header('Content-Type: application/json;  charset=UTF-8');

include '../api/db/conexion.php';


$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("WITH SalidasUnicas AS (
                    SELECT 
                        t2.IdTarja,
                        t3.NombreCliente,
                        t5.NumRecinto,
                        t5.IdAlmacen,
                        t1.Transportista,
                        t1.Placas,
                        t1.Chofer,
                        t1.Contenedor,
                        t1.Caja,
                        t1.Tracto,
                        t1.Sellos,
                        t1.IdRemision
                    FROM t_remision_encabezado as t1 
                    INNER JOIN t_salida as t2 on t1.IdRemisionEncabezado = t2.IdRemision
                    LEFT JOIN t_cliente t3 ON t3.IdCliente = t2.Cliente
                    LEFT JOIN t_almacen t5 ON t5.IdAlmacen = t2.Almacen
                    WHERE t2.Estatus IN (0,1,2) AND t1.Almacen = 40
                    GROUP BY 
                        t2.IdTarja,
                        t3.NombreCliente,
                        t5.NumRecinto,
                        t5.IdAlmacen,
                        t1.Transportista,
                        t1.Placas,
                        t1.Chofer,
                        t1.Contenedor,
                        t1.Caja,
                        t1.Tracto,
                        t1.Sellos,
                        t1.IdRemision
                )
                SELECT 
                    IdTarja, 
                    NombreCliente,
                    NumRecinto,
                    STRING_AGG(IdRemision, ', ') as IdRemision,
                    IdAlmacen,
                    Transportista,
                    Placas,
                    Chofer,
                    Contenedor,
                    Caja,
                    Tracto,
                    Sellos
                FROM SalidasUnicas
                GROUP BY 
                    IdTarja,
                    NombreCliente,
                    NumRecinto,
                    IdAlmacen,
                    Transportista,
                    Placas,
                    Chofer,
                    Contenedor,
                    Caja,
                    Tracto,
                    Sellos");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if (!$Query) {
  $Query = "No hay salidas.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>