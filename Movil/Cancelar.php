<?php
header('Content-Type: text/html;  charset=UTF-8');
Include '../api/db/conexion.php';

			$IdRemision = $_POST["IdRemision"];
			//$usuario=$_POST["User"];

		$sentencia7 = $Conexion->prepare("
		UPDATE t_remision_encabezado set Estatus=0 where Estatus in(2)  and tipoRemision=1 and IdRemision= ?");
		$resultado7 = $sentencia7->execute([$IdRemision]);

		
?>