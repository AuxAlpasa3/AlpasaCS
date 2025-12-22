<?php
header('Content-Type: text/html;  charset=UTF-8');
Include '../api/db/conexion.php';

			$IdRemisionEncabezado = $_POST["IdRemisionEncabezado"];
			$IdUsuario=$_POST["IdUsuario"];

		$sentencia = $Conexion->prepare("UPDATE t1 set t1.Estatus=1 
			FROM t_remision_encabezado as t1 
			INNER JOIN t_usuario_almacen as t2 on t1.Almacen=t2.IdAlmacen
			where t1.tipoRemision=1 and t1.IdRemisionEncabezado= ? and t2.IdUsuario=?");
		$resultado = $sentencia->execute([$IdRemisionEncabezado,$IdUsuario]);
        if($resultado){
          $mensaje = "Actualizado correctamente";
        }

		echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
?>