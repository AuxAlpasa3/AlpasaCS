<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
		$IdTarja = $_GET['IdTarja'];
		$IdRemision = $_GET['IdRemision'];
		$IdArticulo = $_GET['IdArticulo'];
		$CodBarras = $_GET['CodBarras'];

		$sentencia = $Conexion->query("select * from t_ingreso
                               where IdTarja = $IdTarja and IdRemision = $IdRemision
                               and IdArticulo = $IdArticulo and CodBarras = $CodBarras;");
        $Query = $sentencia->fetch(PDO::FETCH_OBJ);

		if(!$Query)
		{
          $Query="No hay ingresos pendientes.";
		}
		
		echo json_encode($Query, JSON_UNESCAPED_UNICODE);
		
?>