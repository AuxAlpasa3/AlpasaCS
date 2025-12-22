<?php
header('Content-Type: aplication/json;  charset=UTF-8');

Include '../api/db/conexion.php';
  $IdRemision = $_GET['IdRemision'];
  $IdArmado = $_GET['IdArmado'];
  $IdTarja = $_GET['IdTarja'];

		$sentencia = $Conexion->query("SELECT CodBarras,Piezas From t_armado 
              WHERE IdRemision=$IdRemision
              and IdArmado=$IdArmado
              and IdTarja=$IdTarja;");
        $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

		if(!$Query)
		{
          $Query="No hay códigos.";
		}
		
		echo json_encode($Query, JSON_UNESCAPED_UNICODE);
		
?>