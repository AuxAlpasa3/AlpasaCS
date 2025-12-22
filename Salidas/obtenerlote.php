<?php
header('Content-Type: application/json; charset=UTF-8');
 Include '../api/db/conexion.php';


$idLote = $_GET['id'];
$idRemision = $_GET['idRemision'];
$MaterialNo = $_GET['MaterialNo'];
$IdAlmacen = $_GET['IdAlmacen'];

$sql = "SELECT distinct(t3.CodBarras) AS CodBarras, 
            t5.NombreCliente, 
            CONVERT(varchar, t3.FechaProduccion, 103) AS FechaProduccion, 
            (CASE 
                WHEN t3.Almacenaje < t3.dias THEN 'VIGENTE'  
                WHEN t3.dias = 0 THEN 'SIN PERIODICIDAD' 
                WHEN t3.Almacenaje > t3.dias THEN 'VENCIDO'  
             END) AS Periodicidad, 
            t4.MaterialNo, 
            CONCAT(t4.Material, ' ', t4.Shape) AS Articulo, 
            (t3.Piezas) AS Piezas,
            t3.EsArmado,
            CASE WHEN t1.Cliente = t5.IdCliente THEN 1 ELSE 2 END AS OrdenCliente,
            ISNULL(t6.Ubicacion,0) as Ubicacion,
             t1.IdRemisionEncabezado, t3.EstadoMaterial,T9.NumRecinto
        FROM 
            t_remision_encabezado AS t1 
            LEFT JOIN t_remision_linea AS t2 ON t1.IdRemision = t2.IdRemision 
            LEFT JOIN t_inventario AS t3 ON t2.IdArticulo = t3.IdArticulo and t1.Almacen=t3.Almacen
            INNER JOIN t_articulo AS t4 ON t4.IdArticulo = t3.IdArticulo
            INNER JOIN t_cliente AS t5 ON t3.Cliente = t5.IdCliente
            LEFT JOIN t_ubicacion as t6 on t3.IdUbicacion=t6.IdUbicacion
            INNER JOIN t_usuario_almacen as t8 on t3.Almacen=t8.IdAlmacen
            INNER JOIN t_Almacen as t9 on t3.Almacen=t9.IdAlmacen
        WHERE    
             t3.Piezas  > 0 
            AND t2.IdLinea = ?
            AND t1.IdRemisionEncabezado = ?
            AND t4.MaterialNo = ?
            AND t3.EnProceso= 0
            AND t8.IdAlmacen=?";

$stmt = $Conexion->prepare($sql);
$stmt->execute([$idLote, $idRemision, $MaterialNo,$IdAlmacen]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($productos);
?>