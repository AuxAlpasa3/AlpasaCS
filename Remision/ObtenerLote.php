<?php
header('Content-Type: application/json; charset=UTF-8');
include '../api/db/conexion.php';


$IdAlmacen = $_GET['IdAlmacen'];
$IdCliente = $_GET['IdCliente'];

$sql = "SELECT distinct(t3.CodBarras) AS CodBarras, 
            t5.NombreCliente, 
            t4.MaterialNo, 
            CONCAT(t4.Material, ' ', t4.Shape) AS Articulo, 
			t3.Origen as Destino,
            (t3.Piezas) AS Piezas,
            CASE WHEN t3.Cliente = t5.IdCliente THEN 1 ELSE 2 END AS OrdenCliente,
                ISNULL(t6.Ubicacion,0) as Ubicacion,T9.NumRecinto, t4.IdArticulo,t3.Cliente,t10.IdRemision
        FROM t_inventario AS t3 
            INNER JOIN t_articulo AS t4 ON t4.IdArticulo = t3.IdArticulo
            INNER JOIN t_cliente AS t5 ON t3.Cliente = t5.IdCliente
            LEFT JOIN t_ubicacion as t6 on t3.IdUbicacion=t6.IdUbicacion
            INNER JOIN t_usuario_almacen as t8 on t3.Almacen=t8.IdAlmacen
            INNER JOIN t_Almacen as t9 on t3.Almacen=t9.IdAlmacen
            INNER JOIN t_remision_encabezado as t10 on t3.IdRemision=t10.IdRemisionEncabezado
        WHERE    
             t3.Piezas  > 0 
            AND t3.EnProceso= 0
            AND t8.IdAlmacen= ?
			AND t3.Cliente= ?
			ORDER BY t3.CodBarras ";

$stmt = $Conexion->prepare($sql);
$stmt->execute([$IdAlmacen, $IdCliente]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($productos);
?>