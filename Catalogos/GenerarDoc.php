<?php

  include ('../Config/conexion.php');
	require_once dirname(__FILE__).'/PHPWord-master/src/PhpWord/Autoloader.php';
	\PhpOffice\PhpWord\Autoloader::register();
	use PhpOffice\PhpWord\TemplateProcessor;

	$templateWord = new TemplateProcessor('../Credenciales/CredPersonal.docx');

	require "phpqrcode/qrlib.php";    
	
	$dir = 'QR/';
	
	if (!file_exists($dir))
        mkdir($dir);
	$ID = $_GET['ID'];
			$select = mysqli_query($mysqli,"Select  LPAD(IdPersonal, 5,'0') as 'IdPersonal' ,Nombre,concat(ApPaterno,' ',ApMaterno) as 'Apellidos', (CASE when t1.Departamento=0 THEN 'SinDepto' else t4.NomDepto END) AS NomDepto
				FROM t_personal as t1 LEFT join t_departamento as t4 on t4.IdDepartamento=t1.Departamento WHERE IdPersonal=".$ID) or die ("Error consulta");
			$total= mysqli_num_rows($select);
			if ($total>0) 
		    {
		    	while ($fila = mysqli_fetch_assoc($select)) 
		        {
		        	$IdPersonal = $fila['IdPersonal'];
		        	$Nombre = $fila['Nombre'];
		    		$Apellidos = $fila['Apellidos'];
		    		$Puesto = $fila['NomDepto'];
		    	}
		    }

 
	$filename = $dir.'QRCredencialPersonal'.$IdPersonal.'.png';
	
	$tamaño = 11; //Tamaño de Pixel
	$level = 'L'; //Precisión Baja
	$framSize = 1; //Tamaño en blanco
	$contenido = $IdPersonal; //Texto
	
	QRcode::png($contenido, $filename, $level, $tamaño, $framSize); 

$templateWord->setImageValue('qr', 'QR/QRCredencialPersonal'.$IdPersonal.'.png');

	echo '<img src="'.$dir.basename($filename).'" /><hr/>';  

//$templateWord->setImageValue('../Credenciales/CuadroBlanco.jpg','../QR/QRCredencialPersonal'.$IdPersonal.'.png');

// --- Asignamos valores a la plantilla
$templateWord->setValue('Nombre',$Nombre);
$templateWord->setValue('Apellidos',$Apellidos);
$templateWord->setValue('Puesto',$Puesto);
$templateWord->setValue('ID',$IdPersonal);

// --- Guardamos el documento
$templateWord->saveAs('C:\Users\kbarrios\Desktop\Credenciales\Documentos\CredencialPersonal'.$IdPersonal.'.docx');


header("Content-Disposition: attachment; filename=CredencialPersonal$IdPersonal.docx; charset=iso-8859-1");
echo file_get_contents('C:\Users\kbarrios\Desktop\Credenciales\Documentos\CredencialPersonal'.$IdPersonal.'.docx');
        
?>