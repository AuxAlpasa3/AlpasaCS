<?php

  include ('../api/db/conexion.php');

	require "phpqrcode/qrlib.php";   

	$CodBarras = $_GET['CodBarras'];

	$dir = 'QR/';


					$filename = $dir.'CodBarras_'.$CodBarras.'.png';
					
					$tamaño = 11;  
					$level = 'L'; 
					$framSize = 0;  
					$contenido = $CodBarras;
					
					QRcode::png($contenido, $filename, $level, $tamaño, $framSize); 
					 header('Content-Description: File Transfer');
				    header('Content-Type: application/octet-stream');
				    header('Content-Disposition: attachment; filename="' .$dir.basename($filename) . '"');
				    header('Expires: 0');
				    header('Cache-Control: must-revalidate');
				    header('Pragma: public');
				    header('Content-Length: ' . filesize($filename));
				    // Leer el archivo y enviarlo al usuario
				    readfile($filename);
    exit;

					?>