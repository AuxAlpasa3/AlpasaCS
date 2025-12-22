<?php
header('Content-Type: application/json; charset=UTF-8');
include '../api/db/conexion.php';

$IdRemision = $_GET['IdRemision'];
$IdAlmacen = $_GET['IdAlmacen'];

$sentencia_count = $Conexion->query("SELECT COUNT(*) as total 
FROM t_ingreso t1
INNER JOIN t_inventario t2 ON t2.CodBarras = t1.CodBarras
INNER JOIN t_remision_encabezado as t3 on t1.Idremision=t3.IdRemisionEncabezado
WHERE t3.IdRemision ='$IdRemision' AND t1.Almacen = $IdAlmacen AND t2.EnProceso = 0");

$count_data = $sentencia_count->fetch(PDO::FETCH_OBJ);
$total_items = $count_data->total;

if ($total_items > 1) {
    $sentencia = $Conexion->query("SELECT 
        t1.IdTarja as IdTarja,
        t1.CodBarras,
        t3.MaterialNo,
        CONCAT(t3.Material, t3.Shape) as MaterialShape,
        t2.Piezas,
        ISNULL(t2.NetWeight,0) as NetWeight,
        ISNULL(t2.GrossWeight,0) as GrossWeight,
        t4.Ubicacion,
        t1.EstadoMercancia,
        t1.FechaIngreso,
        t5.NombreCliente
    FROM t_ingreso t1
    INNER JOIN t_inventario t2 ON t2.CodBarras = t1.CodBarras
    LEFT JOIN t_articulo t3 ON t3.IdArticulo = t1.IdArticulo
    LEFT JOIN t_ubicacion t4 ON t1.IdUbicacion = t4.IdUbicacion
    LEFT JOIN t_cliente t5 ON t1.Cliente = t5.IdCliente
    INNER JOIN t_remision_encabezado as t6 on t1.IdRemision=T6.IdRemisionEncabezado
     WHERE t6.IdRemision = '$IdRemision' AND t1.Almacen =  '$IdAlmacen' AND t2.EnProceso = 0 and t2.Piezas>0
    ORDER BY t1.FechaIngreso DESC");

    $datos = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if ($datos) {
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        echo json_encode(
            array(
                'Mensaje' => 'No se encontró información',
            ),
            JSON_UNESCAPED_UNICODE
        );
    }
} else {
    $sentencia = $Conexion->query("SELECT 
        t1.IdTarja as IdRecord, 
        t1.IdLinea, 
        t1.IdRemision, 
        t1.IdArticulo, 
        t2.Piezas, 
        t1.CodBarras, 
        t1.FechaIngreso as FechaOperacion, 
        t1.FechaProduccion, 
        t1.IdArticulo, 
        ISNULL(t1.NumPedido,'N/A') as NumPedido, 
        ISNULL(t2.NetWeight,0) as NetWeight, 
        ISNULL(t2.GrossWeight,0) as GrossWeight, 
        t1.Origen, 
        t1.Cliente, 
        t1.PaisOrigen, 
        t1.Origen,
        t1.NoTarima, 
        t5.NombreCliente, 
        t1.Transportista, 
        t1.Placas, 
        t1.Chofer, 
        t1.Checador, 
        t1.Supervisor, 
        t1.Comentarios, 
        t4.IdUbicacion,
        CONCAT(t3.Material, t3.Shape) as MaterialShape, 
        t3.MaterialNo, 
        t4.Ubicacion as Ubicacion, 
        t2.Almacen,
        t1.alto,
        t1.Ancho,
        t1.Largo,
        t1.EstadoMercancia,
        t7.NumRecinto,
        t7.Almacen,
        t1.Almacen,
        t6.IdRemisionEncabezado,
        t5.NombreCliente
    FROM t_ingreso t1
    INNER JOIN t_inventario t2 ON t2.CodBarras = t1.CodBarras
    LEFT JOIN t_articulo t3 ON t3.IdArticulo = t1.IdArticulo
    LEFT JOIN t_ubicacion t4 ON t1.IdUbicacion = t4.IdUbicacion
    LEFT JOIN t_cliente t5 ON t1.Cliente = t5.IdCliente
    INNER JOIN t_remision_encabezado as t6 on t1.IdRemision=t6.IdRemisionEncabezado
    INNER JOIN t_almacen as t7 on t1.Almacen=t7.IdAlmacen
    WHERE t6.IdRemision ='$IdRemision' AND t1.Almacen = '$IdAlmacen'  AND t2.EnProceso = 0");

    $datos = $sentencia->fetch(PDO::FETCH_OBJ);

    if ($datos) {
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        echo json_encode(
            array(
                'Mensaje' => 'No se encontró información',
            ),
            JSON_UNESCAPED_UNICODE
        );
    }
}
?>