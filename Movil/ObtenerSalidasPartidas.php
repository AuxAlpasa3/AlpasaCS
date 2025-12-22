<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';

		$IdTarja = $_GET['IdTarja'];
		$IdAlmacen = $_GET['IdAlmacen'];

		$sentencia = $Conexion->query("SELECT * FROM t_salida as t1 
                                INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
                                INNER JOIN t_cliente as t4 on t1.Cliente=t4.IdCliente
                                INNER JOIN t_almacen as t7 on t1.Almacen=t7.IdAlmacen
								INNER JOIN t_remision_encabezado as t8 on t1.IdRemision=t8.IdRemisionEncabezado
                                WHERE t1.ESTATUS IN (0,1,2) 
                                AND t1.IdTarja =$IdTarja and t7.IdAlmacen=$IdAlmacen
                                order by  t1.CodBarras");
        $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

		if(!$Query)
		{
          $Query="No hay salidas.";
		}
		
		echo json_encode($Query, JSON_UNESCAPED_UNICODE);
		
?>