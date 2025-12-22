<?php

  include ('../api/db/conexion.php');
	
    require_once '../vendor/autoload.php';
    require "../lib/phpqrcode/qrlib.php"; 
	\PhpOffice\PhpWord\Autoloader::register();
	use PhpOffice\PhpWord\TemplateProcessor;
	use PhpOffice\PhpWord\IOFactory;
	use PhpOffice\PhpWord\Settings;
	use PhpOffice\PhpWord\Shared\Converter;

use PhpOffice\PhpWord\Element\Table;


    $ZonaHoraria= getenv('ZonaHoraria');
    date_default_timezone_set($ZonaHoraria);
    $RutaLocal = getenv('VERSION');
	$templateWord = new TemplateProcessor('../Config/Documento/TarjaSalidaPlantilla.docx');

	$IdTarja = $_POST['IdTarja'];
	//$IdTarja=1;


		$sentencia = $Conexion->query("SELECT distinct(t1.IdTarja) as Tarja ,t1.IdRemision,CONVERT(DATE,t1.FechaSalida) as FechaSalida, FORMAT(t1.FechaSalida, 'hh:mm tt')  as HoraSalida,t3.NombreCliente,t1.Transportista,t1.Chofer,t1.Placas,  FORMAT(t1.HoraInicio, 'hh:mm tt') as HoraInicio,FORMAT(t1.HoraFinal, 'hh:mm tt')  as HoraFinal,t1.Supervisor,t1.Checador From t_salida as t1
			INNER JOIN t_remision_encabezado as t2 on t2.IdRemision=t1.IdRemision
			INNER JOIN t_cliente as t3 on t2.Cliente=t3.IdCliente WHERE idTarja=$IdTarja ");
			    $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);
      foreach($Query as $row) {
		        $Tarja = $row->Tarja;
		        $IdRemision = $row->IdRemision;
		    		$FechaSalida = $row->FechaSalida;
		    		$HoraSalida = $row->HoraSalida;
		    		$NombreCliente = $row->NombreCliente;
		    		$Transportista = $row->Transportista;
		    		$Chofer = $row->Chofer;
		    		$Placas = $row->Placas;
		    		$HoraInicio = $row->HoraInicio;
		    		$HoraFinal = $row->HoraFinal;
		    	}

		    		$sentencia2 = $Conexion->query("SELECT distinct(t3.NombreColaborador) as Supervisor From t_salida as t1
		    			INNER JOIN t_usuario as t3 on t1.Supervisor=t3.IdUsuario 	WHERE idTarja=$IdTarja ");
						    $Query2 = $sentencia2->fetchAll(PDO::FETCH_OBJ);
			      foreach($Query2 as $row2) {
					        $Supervisor = $row2->Supervisor;
					    	}

					  $sentencia3 = $Conexion->query("SELECT distinct(t3.NombreColaborador) as Checador From t_salida as t1
		    			INNER JOIN t_usuario as t3 on t1.Checador=t3.IdUsuario 	WHERE idTarja=$IdTarja ");
						    $Query3 = $sentencia3->fetchAll(PDO::FETCH_OBJ);
			      foreach($Query3 as $row3) {
					        $Checador = $row3->Checador;
					    	}
					    	

		$templateWord->setValue('IdTarja','ALPSV-SAL-'.sprintf("%04d", $Tarja));
		$templateWord->setValue('IdRemision',$IdRemision);
		$templateWord->setValue('Fecha',$FechaSalida);
		$templateWord->setValue('Hora',$HoraInicio);
		$templateWord->setValue('Cliente',$NombreCliente);
		$templateWord->setValue('Transportista',$Transportista);
		$templateWord->setValue('Transportistas',$Transportista);
		$templateWord->setValue('Chofer',$Chofer);
		$templateWord->setValue('Placas',$Placas);
		$templateWord->setValue('HoraInicio',$HoraInicio);
		$templateWord->setValue('HoraFinal',$HoraFinal);
		$templateWord->setValue('Checador',$Checador);
		$templateWord->setValue('Supervisor',$Supervisor);

	$sentencia4 = $Conexion->query("SELECT count(Codbarras) as cuenta From t_salida as t1
		    			INNER JOIN t_usuario as t3 on t1.Supervisor=t3.IdUsuario 	WHERE idTarja=$IdTarja ");
						    $Query4 = $sentencia4->fetchAll(PDO::FETCH_OBJ);
			      foreach($Query4 as $row4) {
					        $cuenta = $row4->cuenta;
					    	}

		$sentencia5 = $Conexion->query("SELECT t1.CodBarras as CodBarras , NoTarima, CONVERT(DATE,t1.FechaProduccion) as FechaProduccion,t2.MaterialNo, trim(Concat(t2.Material,t2.Shape)) as MaterialShape, t1.Piezas,t1.NumPedido,t1.NetWeight,t1.GrossWeight,t1.Checador,t1.Supervisor
					From t_salida as t1 
					INNER JOIN t_articulo as t2 on t1.IdArticulo=t2.IdArticulo
					INNER JOIN t_remision_encabezado as t3 on t1.IdRemision=t3.idRemision
					INNER JOIN t_cliente as t4 on t3.Cliente=t4.IdCliente
					where IdTarja= $IdTarja ORDER BY t2.MaterialNo, t1.CodBarras asc");
						    $Query = $sentencia5->fetchAll(PDO::FETCH_OBJ);
		if($Query)
		{
			$datos=array();
			foreach ($Query AS $row) {
				array_push($datos, array(
				 'CodBarras' =>'ALP-'.sprintf("%06d",$row->CodBarras),
			     'NoTarima' =>$row->NoTarima, 
			     'FechaProduccion' =>$row->FechaProduccion , 
			     'MaterialNo' =>$row->MaterialNo, 
			     'MaterialShape' =>$row->MaterialShape, 
			     'Piezas' =>$row->Piezas, 
			     'Pedido' =>$row->NumPedido, 
			     'NetWeight' =>$row->NetWeight, 
			     'GrossWeight' =>$row->GrossWeight,
			     $ChecadorID=$row->Checador,
			     $SupervisorID=$row->Supervisor
				));
			}
		}
		$templateWord->cloneRowAndSetValues('CodBarras', $datos);

		
				$imagenVacia = 'FirmasTransportistas/firmaVacia.png';

				$ChecadorF = $imagenVacia;
				$SupervisorF = $imagenVacia;
				$TransportistaF = $imagenVacia;



				$checador = $Conexion->query("SELECT Firma From t_usuario where IdUsuario=$ChecadorID;");
			    $firmaChecador = $checador->fetchAll(PDO::FETCH_OBJ);
      foreach ($firmaChecador as $f) {
				    if (!empty($f->Firma)) {
				        $ChecadorF = $f->Firma;
				    }
				}

		    	$supervisor = $Conexion->query("SELECT Firma From t_usuario where IdUsuario=$SupervisorID;");
			    $firmasupervisor = $supervisor->fetchAll(PDO::FETCH_OBJ);
      foreach($firmasupervisor as $f) {
      	  if (!empty($f->Firma)) {
				        $SupervisorF = $f->Firma;
				    }
				}

		    	$Transpor = $Conexion->query("SELECT distinct(Firma) as Firma From t_salida where IdTarja=$IdTarja");
			    $firmaTransportista = $Transpor->fetchAll(PDO::FETCH_OBJ);
      foreach($firmaTransportista as $f) {
      	if (!empty($f->Firma)) {
				        $TransportistaF = $f->Firma;
				    }
				}
				$templateWord->setImageValue('checador',$ChecadorF);
				$templateWord->setImageValue('supervisor',	$SupervisorF);
				$templateWord->setImageValue('transportista',$TransportistaF);


$templateWord->saveAs("C:\\xampp\\htdocs\\".$RutaLocal."\\TARJAS\\SALIDAS\\TarjaSalida_".$Tarja.".docx");


chdir("C:\\Program Files\\LibreOffice\\program");
putenv("HOME=C:\\inetpub\\wwwroot\\temp");


exec("\"C:\\Program Files\\LibreOffice\\program\\soffice.com\" --headless \"-env:UserInstallation=file:///C:/inetpub/wwwroot/LibreOfficeProfilePath1\" --convert-to pdf:writer_pdf_Export --outdir \"C:\\xampp\\htdocs\\".$RutaLocal."\\TARJAS\\SALIDAS\" \"C:\\xampp\\htdocs\\".$RutaLocal."\\TARJAS\\SALIDAS\\TarjaSalida_$Tarja.docx\"", $outputFile, $ret);

// Do this before executing soffice command and pass it to -env
$profilepath = "C:/inetpub/wwwroot/LibreOfficeProfilePath" . date("YmdHis") . rand(0, 999999);
// Make sure to replace '/' with '\' when deleting
exec("rmdir /S /Q \"" . str_replace("/", "\\", $profilepath) . "\"");

///TODO BIEN//

$Documentopdf="C:\\xampp\\htdocs\\".$RutaLocal."\\TARJAS\\SALIDAS\\TarjaSalida_".$Tarja.".pdf";
$Documento="TarjaIngreso_".$Tarja.".pdf";

			header('Content-Type: application/pdf');
      		header("Content-Disposition: inline; filename=$Documento");
      		header('Content-Transfer-Encoding: binary');
      		header('Accept-Ranges: bytes');
    		readfile($Documentopdf);
?>
<a href="GenerarTarja.php?archivo="<?php echo $Documentopdf;?> target="_blank">Abrir PDF</a>

