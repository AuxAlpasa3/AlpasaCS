<?php
header('Content-Type: text/html;  charset=UTF-8');

		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "alpasaapp";
		

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
		$server = "http://10.123.6.15:8080/regentsalper/";


		

		if($movimiento == 'Entrada')
		{

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


			$select = mysqli_query($con,"Select 1 from regentsalveh where ID_Veh = '$id' and MarcajeEnt is not null and  MarcajeSal is null") or die ("Error consulta");
					
			while ($resul = mysqli_fetch_array($select))
			{
		 		$existe=$resul['1'];
			}
					

			if($existe=="")
			{

				if($foto0<>"V")
				{
					$pathFot0 = "imagenes/vehiculos/entsal_veh/F0_Veh$id$movimiento$fecha2.jpg";
					file_put_contents($pathFot0, base64_decode($foto0));
					$bytesArchivo0= $server.$pathFot0;		
				}
				 if($foto1<>"V")
				{
					$pathFot1 = "imagenes/vehiculos/entsal_veh/F1_Veh$id$movimiento$fecha2.jpg";
					file_put_contents($pathFot1, base64_decode($foto1));
					$bytesArchivo1= $server.$pathFot1;			
				}
				 if($foto2<>"V")
				{
					$pathFot2 = "imagenes/vehiculos/entsal_veh/F2_Veh$id$movimiento$fecha2.jpg";
					file_put_contents($pathFot2, base64_decode($foto2));
					$bytesArchivo2= $server.$pathFot2;			
				}
				 if($foto3<>"V")
				{
					$pathFot3 = "imagenes/vehiculos/entsal_veh/F3_Veh$id$movimiento$fecha2.jpg";
					file_put_contents($pathFot3, base64_decode($foto3));
					$bytesArchivo3= $server.$pathFot3;			
				}	
				 if($foto4<>"V")
				{
					$pathFot4 = "imagenes/vehiculos/entsal_veh/F4_Veh$id$movimiento$fecha2.jpg";
					file_put_contents($pathFot4, base64_decode($foto4));
					$bytesArchivo4= $server.$pathFot4;			
				}

				$consulta = "insert into regentsalveh (ID_Veh,Ubicacion,MarcajeEnt,DispEnt,ObsEnt,Foto0Ent,Foto1Ent,Foto2Ent,Foto3Ent,Foto4Ent) VALUES ('$id','$ubicacion','$fecha','$dispositivo','$obs','$bytesArchivo0','$bytesArchivo1','$bytesArchivo2','$bytesArchivo3','$bytesArchivo4')";

				if(mysqli_query($con,$consulta))
				{
					$Mensaje='Entrada agregada correctamente';
				}
				else
				{
					$Mensaje='Fallo al registrar entrada.';
							
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

			$select2 = mysqli_query($con,"Select 1 from regentsalveh where ID_Veh = '$id' and MarcajeEnt is not null and  MarcajeSal is null") or die ("Error consulta");
				
				while ($resul2 = mysqli_fetch_array($select2))
				{
					$existe2=$resul2['1'];
				}
				

				if($existe2<>"")
				{	
					if($foto0<>"V")
					{
						$pathFot0 = "imagenes/vehiculos/entsal_veh/F0_Veh$id$movimiento$fecha2.jpg";
						file_put_contents($pathFot0, base64_decode($foto0));
						$bytesArchivo0= $server.$pathFot0;		
					}
					 if($foto1<>"V")
					{
						$pathFot1 = "imagenes/vehiculos/entsal_veh/F1_Veh$id$movimiento$fecha2.jpg";
						file_put_contents($pathFot1, base64_decode($foto1));
						$bytesArchivo1= $server.$pathFot1;			
					}
					 if($foto2<>"V")
					{
						$pathFot2 = "imagenes/vehiculos/entsal_veh/F2_Veh$id$movimiento$fecha2.jpg";
						file_put_contents($pathFot2, base64_decode($foto2));
						$bytesArchivo2= $server.$pathFot2;			
					}
					 if($foto3<>"V")
					{
						$pathFot3 = "imagenes/vehiculos/entsal_veh/F3_Veh$id$movimiento$fecha2.jpg";
						file_put_contents($pathFot3, base64_decode($foto3));
						$bytesArchivo3= $server.$pathFot3;			
					}	
					 if($foto4<>"V")
					{
						$pathFot4 = "imagenes/vehiculos/entsal_veh/F4_Veh$id$movimiento$fecha2.jpg";
						file_put_contents($pathFot4, base64_decode($foto4));
						$bytesArchivo4= $server.$pathFot4;			
					}
					

					$consulta2 = "update regentsalveh set MarcajeSal='$fecha',  DispSal='$dispositivo',ObsSal='$obs',Completado='Si', Foto0Sal='$bytesArchivo0',Foto1Sal='$bytesArchivo1',Foto2Sal='$bytesArchivo2',Foto3Sal='$bytesArchivo3',Foto4Sal='$bytesArchivo4' where ID_Veh = '$id' and MarcajeEnt is not null and MarcajeSal is null ";

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
