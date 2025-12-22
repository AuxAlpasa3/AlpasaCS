<?php
header('Content-Type: text/html;  charset=UTF-8');
Include '../api/db/conexion.php';


$IdRemisionEncabezado = $_GET['IdRemisionEncabezado'];
$Almacen = $_GET['Almacen'];

		$sentencia = $Conexion->query("SELECT t1.IdRemisonLinea,t3.IdRemisionEncabezado,t3.IdRemision,
		t1.IdLinea,concat(t2.Material,t2.Shape)as MaterialShape,t1.Piezas,t2.MaterialNo, 
		t3.IdRemisionEncabezado,isnull(t1.Booking,'S/BK') as Booking
		FROM t_remision_linea as t1 
		inner join t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
		inner join t_remision_encabezado as t3 on t1.IdRemisionEncabezadoRef=t3.IdRemisionEncabezado
		WHERE t3.IdRemisionEncabezado=$IdRemisionEncabezado and t1.Almacen=$Almacen order by IdRemision;");
        $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

		if($Query)
		{
			$datos=array();
			foreach ($Query as $row) {
				array_push($datos, array(
					'IdRemisonLinea'=>$row->IdRemisonLinea,
					'IdRemisionEncabezado'=>$row->IdRemisionEncabezado,
					'IdRemision'=>$row->IdRemision,
					'IdLinea'=>$row->IdLinea,
					'Articulo'=>$row->MaterialShape,
					'MaterialNo'=>$row->MaterialNo,
					'Piezas'=>$row->Piezas,
					'Booking'=>$row->Booking
				));
			}
		}else 
		{ 
			$datos="La Remisión no esta disponible, favor de validar con el Administrador";
		}
		
		echo json_encode($datos, JSON_UNESCAPED_UNICODE);
		
?>