<?php

  include ('../Config/conexion.php');
	require_once dirname(__FILE__).'/PHPWord-master/src/PhpWord/Autoloader.php';
	\PhpOffice\PhpWord\Autoloader::register();
	use PhpOffice\PhpWord\TemplateProcessor;

	$templateWord = new TemplateProcessor('../Credenciales/CredencialVehiculo.docx');

	require "phpqrcode/qrlib.php";    
	
	$dir = 'QRVehiculo/';
	
	if (!file_exists($dir))
        mkdir($dir);
	$ID = $_GET['ID'];

			$select = mysqli_query($mysqli,"SELECT LPAD(IdVehiculo, 5,'0') as IdVehiculo, Marca, Modelo, Placas FROM t_vehiculos where IdVehiculo=".$ID) or die ("Error consulta");
			$total= mysqli_num_rows($select);
			if ($total>0) 
		    {
		    	while ($fila = mysqli_fetch_assoc($select)) 
		        {
		        	$IdVehiculo = $fila['IdVehiculo'];
		        	$Marca = $fila['Marca'];
		    		$Modelo = $fila['Modelo'];
		    		$Placas = $fila['Placas'];
		    	}
		    }

	$filename = $dir.'QRCredencialVehiculo'.$IdVehiculo.'.png';
	
	$tamaño = 11; //Tamaño de Pixel
	$level = 'L'; //Precisión Baja
	$framSize = 1; //Tamaño en blanco
	$contenido = $IdVehiculo; //Texto
	
	QRcode::png($contenido, $filename, $level, $tamaño, $framSize); 

$templateWord->setImageValue('qr', 'QRVehiculo/QRCredencialVehiculo'.$IdVehiculo.'.png');

	echo '<img src="'.$dir.basename($filename).'" /><hr/>';  

//$templateWord->setImageValue('../Credenciales/CuadroBlanco.jpg','../QR/QRCredencialVehiculo'.$IdVehiculo.'.png');

// --- Asignamos valores a la plantilla
$templateWord->setValue('Marca',$Marca);
$templateWord->setValue('Placa',$Placas);
$templateWord->setValue('Modelo',$Modelo);
$templateWord->setValue('id',$IdVehiculo);

// --- Guardamos el documento
$templateWord->saveAs('C:\Users\kbarrios\Desktop\Credenciales\Documentos\CredencialVehiculo_Personal'.$ID.'.docx');

header("Content-Disposition: attachment; filename=CredencialVehiculo$IdVehiculo.docx; charset=iso-8859-1");
echo file_get_contents('C:\Users\kbarrios\Desktop\Credenciales\Documentos\CredencialVehiculo_Personal'.$ID.'.docx');
        
?>