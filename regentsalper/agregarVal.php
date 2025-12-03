<?php

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

		$folmov = '';
		$movimiento = $_POST["Movimiento"];
		$existe='';
		$existe2='';
		$Mensaje='';
		$Mensaje2='';
		$server = "http://10.123.6.15:8080/regentsalper/";

	

/*
		$folmov = '';
		$movimiento = 'Entrada';
		$ubicacion = 'PolA';
		$dispositivo = 'Server';
		$nombre= 'Ricardo';
		$apellido = 'Sanchez';
		$empresa = 'Alpasa';
		$visita= 'Jorge Sagahon';
		$motivo= 'Internet';
		$placas= '';
		$modelo= '';
		$color= '';
		$descripcion= 'Pruebas';
		$fotoper= '';
		$fotoveh= '';
		$fotoHerr= '';
		$existe='';
		$existe2='';
		$Mensaje='';
		$Mensaje2='';
*/

		if($movimiento == 'Entrada')
		{
			$ubicacion = $_POST["Ubicacion"];
			$dispositivo = $_POST["Dispositivo"];
			$nombre= $_POST["Nombre"];
			$apellido = $_POST["Apellido"];
			$empresa = $_POST["Empresa"];
			$visita=$_POST["Visita"];
			$motivo=$_POST["Motivo"];
			$placas=$_POST["Placas"];
			$modelo=$_POST["Modelo"];
			$color=$_POST["Color"];			
			$descripcion=$_POST["Observaciones"];
			$foto0=$_POST["Foto0"];
			$foto1=$_POST["Foto1"];
			$foto2=$_POST["Foto2"];
			$foto3=$_POST["Foto3"];
			$foto4=$_POST["Foto4"];
			$foto5=$_POST["Foto5"];
			$foto6=$_POST["Foto6"];
			$foto7=$_POST["Foto7"];
			$foto8=$_POST["Foto8"];
			$foto9=$_POST["Foto9"];			
			$bytesArchivo0="";
			$bytesArchivo1="";
			$bytesArchivo2="";
			$bytesArchivo3="";
			$bytesArchivo4="";
			$bytesArchivo5="";
			$bytesArchivo6="";
			$bytesArchivo7="";
			$bytesArchivo8="";
			$bytesArchivo9="";
			
				$select = mysqli_query($con,"Select 1 from regentsalvis where Vis_Nombre like '%$nombre%' and Vis_Apellido like '%$apellido%' and Veh_Placas like '%$placas%' and Fecha_Ent is not null and  Fecha_Sal is null") or die ("Error consulta");
				
				while ($resul = mysqli_fetch_array($select))
				{
					$existe=$resul['1'];
				}
				

				if($existe=="")
				{
					if($foto0<>"V")
					{
						$pathFot0 = "imagenes/visitas/F0_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot0, base64_decode($foto0));
						$bytesArchivo0= $server.$pathFot0;		
					}

					if($foto1<>"V")
					{
						$pathFot1 = "imagenes/visitas/F1_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot1, base64_decode($foto1));
						$bytesArchivo1= $server.$pathFot1;			
					}

					if($foto2<>"V")
					{
						$pathFot2 = "imagenes/visitas/F2_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot2, base64_decode($foto2));
						$bytesArchivo2= $server.$pathFot2;			
					}

					if($foto3<>"V")
					{
						$pathFot3 = "imagenes/visitas/F3_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot3, base64_decode($foto3));
						$bytesArchivo3= $server.$pathFot3;			
					}

					if($foto4<>"V")
					{
						$pathFot4 = "imagenes/visitas/F4_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot4, base64_decode($foto4));
						$bytesArchivo4= $server.$pathFot4;			
					}

					if($foto5<>"V")
					{
						$pathFot5 = "imagenes/visitas/F5_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot5, base64_decode($foto5));
						$bytesArchivo5= $server.$pathFot5;			
					}

					if($foto6<>"V")							
					{
						$pathFot6 = "imagenes/visitas/F6_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot6, base64_decode($foto6));
						$bytesArchivo6= $server.$pathFot6;			
					}

					if($foto7<>"V")
					{
						$pathFot7 = "imagenes/visitas/F7_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot7, base64_decode($foto7));
						$bytesArchivo7= $server.$pathFot7;			
					}

					if($foto8<>"V")
					{
						$pathFot8 = "imagenes/visitas/F8_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot8, base64_decode($foto8));
						$bytesArchivo8= $server.$pathFot8;			
					}

					if($foto9<>"V")
					{
						$pathFot9 = "imagenes/visitas/F9_Vis$nombre$apellido$empresa$fecha2.jpg";
						file_put_contents($pathFot9, base64_decode($foto9));
						$bytesArchivo9= $server.$pathFot9;			
					}

					$consulta = "insert into regentsalvis (FolMov,Fecha_Ent,VisPatio,VisDispEnt,Vis_Nombre,Vis_Apellido,Vis_Empresa,Visita,Per_Motivo,ObsEnt,Veh_Placas,Veh_Modelo,Veh_Color,Foto0,Foto1,Foto2,Foto3,Foto4,Foto5,Foto6,Foto7,Foto8,Foto9) VALUES ('$folmov','$fecha','$ubicacion','$dispositivo','$nombre','$apellido','$empresa','$visita','$motivo', '$descripcion','$placas','$modelo','$color','$bytesArchivo0','$bytesArchivo1','$bytesArchivo2','$bytesArchivo3','$bytesArchivo4','$bytesArchivo5','$bytesArchivo6','$bytesArchivo7','$bytesArchivo8','$bytesArchivo9')";

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
			$folmov = $_POST["Id"];
			$ubicacion = $_POST["Ubicacion"];
			$dispositivo = $_POST["Dispositivo"];
			$descripcion=$_POST["Observaciones"];
			$foto0=$_POST["Foto0"];
			$foto1=$_POST["Foto1"];
			$foto2=$_POST["Foto2"];
			$foto3=$_POST["Foto3"];
			$foto4=$_POST["Foto4"];
			$foto5=$_POST["Foto5"];
			$foto6=$_POST["Foto6"];
			$foto7=$_POST["Foto7"];
			$foto8=$_POST["Foto8"];
			$foto9=$_POST["Foto9"];			
			$bytesArchivo0="";
			$bytesArchivo1="";
			$bytesArchivo2="";
			$bytesArchivo3="";
			$bytesArchivo4="";
			$bytesArchivo5="";
			$bytesArchivo6="";
			$bytesArchivo7="";
			$bytesArchivo8="";
			$bytesArchivo9="";

					if($foto0<>"V")
					{
						$pathFot0 = "imagenes/visitas/F0_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot0, base64_decode($foto0));
						$bytesArchivo0= $server.$pathFot0;		
					}

					if($foto1<>"V")
					{
						$pathFot1 = "imagenes/visitas/F1_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot1, base64_decode($foto1));
						$bytesArchivo1= $server.$pathFot1;			
					}

					if($foto2<>"V")
					{
						$pathFot2 = "imagenes/visitas/F2_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot2, base64_decode($foto2));
						$bytesArchivo2= $server.$pathFot2;			
					}

					if($foto3<>"V")
					{
						$pathFot3 = "imagenes/visitas/F3_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot3, base64_decode($foto3));
						$bytesArchivo3= $server.$pathFot3;			
					}

					if($foto4<>"V")
					{
						$pathFot4 = "imagenes/visitas/F4_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot4, base64_decode($foto4));
						$bytesArchivo4= $server.$pathFot4;			
					}

					if($foto5<>"V")
					{
						$pathFot5 = "imagenes/visitas/F5_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot5, base64_decode($foto5));
						$bytesArchivo5= $server.$pathFot5;			
					}

					if($foto6<>"V")							
					{
						$pathFot6 = "imagenes/visitas/F6_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot6, base64_decode($foto6));
						$bytesArchivo6= $server.$pathFot6;			
					}

					if($foto7<>"V")
					{
						$pathFot7 = "imagenes/visitas/F7_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot7, base64_decode($foto7));
						$bytesArchivo7= $server.$pathFot7;			
					}

					if($foto8<>"V")
					{
						$pathFot8 = "imagenes/visitas/F8_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot8, base64_decode($foto8));
						$bytesArchivo8= $server.$pathFot8;			
					}

					if($foto9<>"V")
					{
						$pathFot9 = "imagenes/visitas/F9_SalVis$folmov$fecha2.jpg";
						file_put_contents($pathFot9, base64_decode($foto9));
						$bytesArchivo9= $server.$pathFot9;			
					}

			$consulta2 = "update regentsalvis set Fecha_Sal='$fecha',  VisDispSal='$dispositivo', ObsSalida='$descripcion',Foto0Sal='$bytesArchivo0',Foto1Sal='$bytesArchivo1',Foto2Sal='$bytesArchivo2',Foto3Sal='$bytesArchivo3',Foto4Sal='$bytesArchivo4',Foto5Sal='$bytesArchivo5',Foto6Sal='$bytesArchivo6',Foto7Sal='$bytesArchivo7',Foto8Sal='$bytesArchivo8',Foto9Sal='$bytesArchivo9' where FolMov='$folmov' and Fecha_Ent is not null and Fecha_Sal is null ";

					if(mysqli_query($con,$consulta2))
					{
						$Mensaje='Salida agregada correctamente.';
						
					}
					else
					{
						$Mensaje='Fallo al modificar registro.';
						
					}						
		}

		$Mensaje2=$Mensaje;	
		echo utf8_encode(json_encode($Mensaje2));
		mysqli_close($con);
?>
