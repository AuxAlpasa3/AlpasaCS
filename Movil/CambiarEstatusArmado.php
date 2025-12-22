<?php
header('Content-Type: aplication/json;  charset=UTF-8');
Include '../api/db/conexion.php';

		$IdRemision = $_POST["IdRemision"];
		$IdArmado = $_POST["IdArmado"];
		$IdTarja = $_POST["IdTarja"];
		$Estatus = $_POST["Estatus"];

		$sentencia = $Conexion->prepare("UPDATE t_armado set Estatus= ? where IdRemision= ? AND IdArmado = ? AND IdTarja = ?");
		$resultado = $sentencia->execute([$Estatus,$IdRemision, $IdArmado, $IdTarja]);
        if($resultado){
        		$sentencia = $Conexion->prepare("UPDATE t_ingreso_armado set Estatus= ? where IdRemision= ? AND IdArmado = ? AND IdTarja = ?");
        		
						$resultado = $sentencia->execute([$Estatus,$IdRemision, $IdArmado, $IdTarja]);

          $mensaje = "Estatus Actualizado Correctamente.";
        }

		echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
?>