<?php
	$mysqli = new mysqli('localhost', 'root', '', 'alpasaapp');
	
	const base_url = "http://localhost/AlpasaCS/Seguridad/";

	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}

	if (!$mysqli->set_charset("utf8")) {
    } else {

    }

// class conexion{
// 	private $host='localhost';//generalmente suele ser "127.0.0.1"
// 	private $user='root';//Usuario de tu base de datos
// 	private $pass='';//Contraseña del usuario de la base de datos
// 	private $db='alpasaapp';//Nombre de la base de datos
// 	public $counter;//Propiedad para almacenar el numero de registro devueltos por la consulta
	
// 	public  function conectar(){
// 		$conexion = new mysqli($this->host, $this->user, $this->pass, $this->db);
// 		$conexion->query("SET NAMES 'utf8'");
// 		return $conexion;
// 	}
// }


?>