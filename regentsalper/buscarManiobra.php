<?php
header('Content-Type: text/html;  charset=UTF-8');

		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "alpasaapp";
		$qr = $_GET['QR'];

		//$qr = "900437";



		$con = mysqli_connect($servername, $username, $password, $dbname);

		if(!$con){
			die("Conexion fallida: ".mysqli_connect_error());
		}

		

		$select = mysqli_query($con,"Select * from t_maniobra where FolManiobra = '$qr' ") or die ("Error consulta");

		//$select = mysql_query($con,"Select * from catper where IdTrab = '$qr' ") or die ("Error consulta");

		$datos=array();

		foreach ($select as $row) {
			array_push($datos, array(
				'FolManiobra'=>$row['FolManiobra'],
				'Patio'=>$row['Patio'],
				'FechaCreacion'=>$row['FechaCreacion'],
				'Servicio'=>$row['Servicio'],
				'Producto'=>$row['Producto'],
				'Transportista'=>$row['Transportista'],
				'Placas'=>$row['Placas'],
				'Operador'=>$row['Operador'],
		));
		}

		echo utf8_encode(json_encode($datos));
		mysqli_close($con);
?>
