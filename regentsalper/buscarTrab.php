<?php
header('Content-Type: text/html;  charset=UTF-8');

		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "Control_accesos";
		$qr = $_GET['QR'];

		$con = mysqli_connect($servername, $username, $password, $dbname);

mysqli_set_charset($con,"utf8");
		if(!$con){
			die("Conexion fallida: ".mysqli_connect_error());
		}
		$select = mysqli_query($con,"Select t1.ID_Per,t1.Nombre,concat(t1.ApPaterno,' ',t1.ApMaterno) as 'Apellidos',t1.Cargo,t1.Departamento,t2.NomCorto,t1.Empresa, t1.RutaFoto 
		 FROM catper as t1 inner join catubicacion as t2 on t1.ubicacion=t2.IdUbicacion WHERE t1.ID_Per='$qr' and t1.Status=1;") or die ("Error consulta");
		if(mysqli_num_rows($select)!=0)
		{
				$datos=array();

				foreach ($select as $row) {
				array_push($datos, array(
					'ID'=>$row['ID_Per'],
					'Nombre'=>$row['Nombre'],
					'Apellidos'=>$row['Apellidos'],
					'Cargo'=>$row['Cargo'],
					'Depto'=>$row['Departamento'],
					'NomCorto'=>$row['NomCorto'],
					'Empresa'=>$row['Empresa'],
					'Foto'=>$row['RutaFoto'],
			));
			}
		}
		else 
		{ 

			$datos="QR personal inactivo, favor de contactar al administrador del sistema";
		}

         echo json_encode($datos, JSON_UNESCAPED_UNICODE);
		mysqli_close($con);
?>
