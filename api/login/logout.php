<?php
Include '../db/conexion.php';
	
		session_start(); 
		session_destroy();
		session_unset();
		 header("Location:" .base_url.'Index.php');

?>
