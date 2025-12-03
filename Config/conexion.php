<?php
	$mysqli = new mysqli('localhost', 'root', '', 'alpasaapp');
	
	const base_url = "http://localhost/AlpasaCS/";

	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}

	if (!$mysqli->set_charset("utf8")) {
    } else {

    }


?>