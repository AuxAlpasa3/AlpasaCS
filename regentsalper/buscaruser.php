<?php
header('Content-Type: text/html;  charset=UTF-8');

		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "alpasaapp";
		//$qr = $_GET['QR'];
		$user = $_GET['usuario'];
		//$user='Admin';
		$pass = $_GET['pass'];
		//$pass='ac';

		$con = mysqli_connect($servername, $username, $password, $dbname);

mysqli_set_charset($con,"utf8");
		if(!$con){
			die("Conexion fallida: ".mysqli_connect_error());
		}
		$select = mysqli_query($con,"Select t2.Usuario,t2.Contrasenia from t_usuarios as t2 inner join t_personal as t1 on t1.IdPersonal=t2.EmpleadoId inner join t_ubicacion as t3 on t1.idUbicacion= t3.idUbicacion
             where t2.Usuario=$user and t2.Contrasenia=$pass and t2.status=1 and t1.Status=1;") or die ("Error consulta");

		$datos=array();
		if(mysqli_num_rows($select)!=0)
		{

		foreach ($select as $row) {
			array_push($datos, array(
				'usuario'=>$row['Usuario'],
				'pass'=>$row['Contrasenia']
				));
			}
		}else
		{

		}


		echo utf8_encode(json_encode($datos));
		mysqli_close($con);
?>
