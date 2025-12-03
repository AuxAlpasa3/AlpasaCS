<?php
header('Content-Type: text/html;  charset=UTF-8');

		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "alpasaapp";
		$qr = $_GET['QR'];

		date_default_timezone_set("America/Chihuahua");
		$fecha = date('Y-m-d');
		//$qr = "1";



		//echo $fecha;
		$con = mysqli_connect($servername, $username, $password, $dbname);
		
		mysqli_set_charset($con,"utf8");

		if(!$con){
			die("Conexion fallida: ".mysqli_connect_error());
		}

		

		$select = mysqli_query($con,"select t1.Placas, t1.Marca, t1.Color,t1.Num_Serie, t1.Modelo,concat(t2.Nombre,' ', t2.ApPaterno) AS PersonaCargo, t1.RutaFoto					
									 from t_vehiculos t1 
									 	inner join t_personal t2 on t1.IdPersonal=t2.IdPersonal
									 where t1.ID_Veh = '$qr' and t1.Fecha_Fin >= '$fecha' ") or die ("Error: QR inactivo, favor de contactar al administrador del sistema");


		if(mysqli_num_rows($select)!=0)
		{
				$datos=array();

				foreach ($select as $row) {
				array_push($datos, array(
					'Placas'=>$row['Placas'],
					'Marca'=>$row['Marca'],
					'Modelo'=>$row['Modelo'],
					'Color'=>$row['Color'],
					'NumSerie'=>$row['Num_Serie'],
					'PersonaCargo'=>$row['PersonaCargo'],
					'Foto'=>$row['RutaFoto'],
			));
			}
		}
		else
		{
			$datos="QR vehicular inactivo, favor de contactar al administrador del sistemas";

		}

		

         echo json_encode($datos, JSON_UNESCAPED_UNICODE);
		mysqli_close($con);
?>
