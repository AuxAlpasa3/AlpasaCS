<?php
header('Content-Type: text/html;  charset=UTF-8');

		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "control_accesos";
		

		$con = mysqli_connect($servername, $username, $password, $dbname);
	

		if(!$con){
			die("Conexion fallida: ".mysqli_connect_error());
		}

		date_default_timezone_set("America/Chihuahua");

		$fecha = date('Y-m-d H:i:s');
		$fecha2 = date('YMDHis');
		$movimiento = $_POST["Movimiento"];
		$existe='';
		$existe2='';
		$Mensaje='';
		$server = "http://10.123.6.15:8080/RegUser/";

		if($movimiento == 'sesion')
		{
			$User = $_POST["User"];
			$ubicacion = $_POST["Ubicacion"];
			$dispositivo = $_POST["Dispositivo"];

			$select = mysqli_query($con,"SELECT 1 FROM reg_user_acceso WHERE idUser='$User' and Dispositivo='$dispositivo' and Ubicacion='$ubicacion'") or die ("Error consulta");
					
			while ($resul = mysqli_fetch_array($select))
			{
		 		$existe=$resul['1'];
			}
					
			if($existe=="")
			{

				$consulta = "insert into reg_user_acceso (idUser,Ubicacion,Dispositivo,FechaLogIn)
				 VALUES ('$User','$ubicacion','$dispositivo','$fecha')";

				if(mysqli_query($con,$consulta))
				{
					//$Mensaje='Sesión iniciada correctamente';
				}
				else
				{
					$Mensaje='Fallo al iniciar sesión';
							
				}			
			}
			else
			{
				$Mensaje='Ya existe un movimiento de Entrada para ese folio, ingrese una salida.';
			}
		}

		if($movimiento == 'Salida')
		{
			$folmov = $_POST["ID"];
			$id = $_POST["ID"];
			$obs = $_POST["Observaciones"];
			$ubicacion = $_POST["Ubicacion"];
			$dispositivo = $_POST["Dispositivo"];
			$foto0=$_POST["Foto0"];
			$foto1=$_POST["Foto1"];
			$foto2=$_POST["Foto2"];
			$foto3=$_POST["Foto3"];
			$foto4=$_POST["Foto4"];
			$bytesArchivo0="";
			$bytesArchivo1="";
			$bytesArchivo2="";
			$bytesArchivo3="";
			$bytesArchivo4="";	

			$select2 = mysqli_query($con,"Select 1 from reg_entsal_per where ID_Per = '$id' and MarcajeEnt is not null and  MarcajeSal is null") or die ("Error consulta");
				
				while ($resul2 = mysqli_fetch_array($select2))
				{
					$existe2=$resul2['1'];
				}
				
				if($existe2<>"")
				{	
					
					$consulta2 = "update reg_entsal_per set MarcajeSal='$fecha',  DispSal='$dispositivo',ObsSal='$obs',Completado=1, Foto0Sal='$bytesArchivo0',Foto1Sal='$bytesArchivo1',Foto2Sal='$bytesArchivo2',Foto3Sal='$bytesArchivo3',Foto4Sal='$bytesArchivo4' where ID_Per = '$id' and MarcajeEnt is not null and MarcajeSal is null ";

					if(mysqli_query($con,$consulta2))
					{
						$Mensaje='Salida agregada correctamente.';
						
					}
					else
					{
						$Mensaje='Fallo al modificar registro.';
						
					}	
				}
				else
				{
					$Mensaje='No existe un movimiento de entrada para ese folio.';
					
				}
					
		}

		echo utf8_encode(json_encode($Mensaje));
		mysqli_close($con);
?>
