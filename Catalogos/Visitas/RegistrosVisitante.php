<?php 
  include ('../Config/conexion.php');
//INSERTAR VISITANTE//
		$server='http://192.168.10.243:8080/regentsalper/imagenes/';
		$VisNombre = (!empty($_POST['nombre']))   ?  $_POST['nombre']: NULL;
		$VisApPaterno = (!empty($_POST['ApPaterno']))   ?  $_POST['ApPaterno']: NULL;
		$VisApMaterno = (!empty($_POST['ApMaterno']))   ?  $_POST['ApMaterno']: NULL;

		$VisEmpresa = (!empty($_POST['VEmpresa']))   ?  $_POST['VEmpresa']: NULL;
		$VisVisita = (!empty($_POST['VDirige']))   ?  $_POST['VDirige']: NULL;
		$VisMotivo = (!empty($_POST['VMotivo']))   ?  $_POST['VMotivo']: NULL;
		$VisTipo = (!empty($_POST['v_tvisita']))   ?  $_POST['v_tvisita']: NULL;
		$VisFoto = (!empty($_POST['fotovis']))   ?  $_POST['fotovis']: NULL;		
		$bytesArchivoVis=" ";
		
	if((!empty($VisFoto)))
		{
			$pathFotoVis = "Visitantes/Foto_Vis_$VisApPaterno$VisNombre.jpg";
			file_put_contents($pathFotoVis, base64_decode($VisFoto));
			$bytesArchivoVis= $server.$pathFotoVis;		
		}
		else
		{
			$bytesArchivoVis=" ";
		}
					

$snvehiculo = ($_POST['snvehiculo']);

IF($snvehiculo =='Si')
{
	$VisMarca = (!empty($_POST['marca']))   ?  $_POST['marca'] : NULL;
	$VisModelo = (!empty($_POST['Modelo']))   ?  $_POST['Modelo']: NULL;
	$VisNumeroSerie = (!empty($_POST['NumSerie']))   ?  $_POST['NumSerie']: NULL;
	$VisPlacas = (!empty($_POST['placas']))   ?  $_POST['placas']: NULL;
	$VisAnio = (!empty($_POST['Anio']))   ?  $_POST['Anio'] : NULL;
	$VisColor = (!empty($_POST['Color']))   ?  $_POST['Color']: NULL;
	$VisFotoVehiculo = (!empty($_POST['Fveh']))   ?  $_POST['Fveh']: NULL;
	$bytesArchivoVisVeh=" ";




	if((!empty($VisFotoVehiculo)))
			{
				$pathFotoVis = "Visitantes/Foto_Vis_$VisApPaterno$VisNombre_$VisMarca.jpg";
				file_put_contents($pathFotoVis, base64_decode($VisFotoVehiculo));
				$bytesArchivoVisVeh= $server.$pathFotoVis;		
			}
			else {
				$bytesArchivoVisVeh=" ";
			}
						
}
elseIF($snvehiculo =='No')
{

	$VisMarca = " ";
	$VisModelo = " ";
	$VisNumeroSerie = " ";
	$VisPlacas = " ";
	$VisAnio = " ";
	$VisColor = " ";
	$bytesArchivoVisVeh = " ";

}

	$VisStatus = 1;

	IF($VisTipo==1) 
	{
		$date_now = date('Y-m-d');
	$date_future = strtotime('+30 day', strtotime($date_now));
	$date_future = date('Y-m-d', $date_future);
		$VisFechaCaducidad = $date_future;
	}
	elseif($VisTipo==2)
	{
		$date_now = date('Y-m-d');
	$date_future = strtotime('+100 day', strtotime($date_now));
	$date_future = date('Y-m-d', $date_future);
		$VisFechaCaducidad = $date_future;
	}


	$ConsultaValidar ="Select max(IdVisitante) AS IdVisitante from t_visitante where VisNombre='$VisNombre' and VisApPaterno='$VisApPaterno' and  VisApMaterno='$VisApMaterno' and VisEmpresa='$VisEmpresa';";
	
				$select = mysqli_query($mysqli,$ConsultaValidar) or die ("Error consulta");

                  while ($row = mysqli_fetch_assoc($select)) 
                    {
                      $IdVisitante = $row["IdVisitante"];
                    } 
	              if (!empty($IdVisitante))
	                {
						$ConsultaVigencia ="Select VisFechaCaducidad from t_visitante where IdVisitante=$IdVisitante;";
						
							$select2 = mysqli_query($mysqli,$ConsultaVigencia) or die ("Error consulta");
				               $total2= mysqli_num_rows($select2);
				              if ($total2>0) 
				                {
				                  while ($row = mysqli_fetch_assoc($select2)) 
				                    {
				                      $VisFechaCaducidad = $row["VisFechaCaducidad"];
				                    } 
					                   if($VisFechaCaducidad < date('d-m-Y'))
					                   {
					                   	IF($VisTipo==1) 
											{
												$date_now = date('Y-m-d');
											$date_future = strtotime('+30 day', strtotime($date_now));
											$date_future = date('Y-m-d', $date_future);
												$VisFechaCaducidad = $date_future;
											}
											elseif($VisTipo==2)
											{
												$date_now = date('Y-m-d');
											$date_future = strtotime('+100 day', strtotime($date_now));
											$date_future = date('Y-m-d', $date_future);
												$VisFechaCaducidad = $date_future;
											}

										  $sql2 = "Insert into t_visitante (VisNombre, VisApPaterno, VisApMaterno, VisEmpresa, VisVisita, VisMotivo, VisTipo, VisFoto, VisMarca, VisModelo,VisNumeroSerie, VisPlacas, VisAnio, VisColor, VisFotoVehiculo, VisStatus, VisFechaCaducidad) VALUES ('$VisNombre','$VisApPaterno','$VisApMaterno','$VisEmpresa','$VisVisita','$VisMotivo',$VisTipo,'$bytesArchivoVis','$VisMarca','$VisModelo','$VisNumeroSerie','$VisPlacas','$VisAnio','$VisColor','$bytesArchivoVisVeh',$VisStatus,'$VisFechaCaducidad')";

											if(mysqli_query($mysqli,$sql2))
													{
												   		 echo "<script>
											                alert('Invitado Agregado Correctamente');
											                window.location= '../Personal/Personal.php'
											   	  			 </script>";
													}
												else
													{
												   		echo "<script>
											                alert('Favor de Validar la información, no se logro agregar correctamente.');
											                window.location= '../Personal/AgregarVisitante.php'
											   	  			 </script>";			
													}	
									    }else
											{
									   		 echo "<script>
											                alert('Invitado Vigente en el Portal hasta la fecha $VisFechaCaducidad.');
											                window.location= '../Personal/AgregarVisitante.php'
											   	   </script>";
											}
										}
									}else
									{
										 $sql2 = "Insert into t_visitante (VisNombre, VisApPaterno, VisApMaterno, VisEmpresa, VisVisita, VisMotivo, VisTipo, VisFoto, VisMarca, VisModelo,VisNumeroSerie, VisPlacas, VisAnio, VisColor, VisFotoVehiculo, VisStatus, VisFechaCaducidad) VALUES ('$VisNombre','$VisApPaterno','$VisApMaterno','$VisEmpresa','$VisVisita','$VisMotivo',$VisTipo,'$bytesArchivoVis','$VisMarca','$VisModelo','$VisNumeroSerie','$VisPlacas','$VisAnio','$VisColor','$bytesArchivoVisVeh',$VisStatus,'$VisFechaCaducidad')";

											if(mysqli_query($mysqli,$sql2))
													{
												   		 echo "<script>
											                alert('Invitado Agregado Correctamente');
											                window.location= '../Personal/Personal.php'
											   	  			 </script>";
													}
												else
													{
														 echo "<script>
											                alert('Favor de Validar la información, no se logro agregar correctamente.');
											                window.location= '../Personal/AgregarVisitante.php'
											   	  			 </script>";		
													}	
									}



?>


