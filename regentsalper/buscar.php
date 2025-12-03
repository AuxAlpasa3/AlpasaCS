<?php


		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "alpasaapp";


		$con = mysqli_connect($servername, $username, $password, $dbname);

		if(!$con){
			die("Conexion fallida: ".mysqli_connect_error());
		}


		$select = mysqli_query($con,"Select * from regentsalvis where Fecha_Ent is not null and Fecha_Sal is null ") or die ("Error consulta");

		$datos=array();

		foreach ($select as $row) {
			array_push($datos, array(
				'ID'=>$row['FolMov'],
				'Nombre'=>$row['Per_Nombre'],
				'Apellido'=>$row['Per_Apellido'],
				'Placas'=>$row['Veh_Placas'],
				'Patio'=>$row['Gen_Patio'],

		));
		}



/*
		while ($row = mysqli_fetch_array($select)){
			
			echo utf8_encode(json_encode($row['Nombre'].$row['Per_Nombre']));
			echo utf8_encode(json_encode($row['Apellido'].$row['Per_Apellido']));
			echo utf8_encode(json_encode($row['Placas'].$row['Veh_Placas']));
			
		}
*/


		echo utf8_encode(json_encode($datos));
		mysqli_close($con);
?>
