<?php
include_once "../templates/SesionP.php";
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$ZonaHoraria = getenv('ZonaHoraria') ?: 'America/Mexico_City';
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION') ?: '';

if (!isset($_POST['IdTarja']) || !is_numeric($_POST['IdTarja'])) {
    die('Invalid Tarja ID');
}

$IdTarja = (int) $_POST['IdTarja'];
//$IdAlmacen = (int) $_POST['IdAlmacen'];

$IdAlmacen = 40;

try {
    $stmt = $Conexion->prepare("SELECT t1.IdTarja as Tarja, t1.IdRemision, t2.IdRemision AS IdRemisionOriginal, 
        CONVERT(DATE, t1.FechaSalida) as FechaSalida, FORMAT(t1.FechaSalida, 'hh:mm tt') as HoraSalida, 
        t3.NombreCliente, t1.Transportista, t1.Chofer, t1.Placas, 
        FORMAT(t1.HoraInicio, 'hh:mm tt') as HoraInicio, FORMAT(t1.HoraFinal, 'hh:mm tt') as HoraFinal, 
        t1.Supervisor as SupervisorID, t1.Checador as ChecadorID, t4.Direccion,
        CONCAT(t4.Municipio,', ',t4.Estado,', ',t4.Pais) as Direccion2, t4.CodPostal,t4.NumRecinto
        FROM t_salida as t1 
        INNER JOIN t_remision_encabezado as t2 ON t2.IdRemisionEncabezado = t1.IdRemision 
        INNER JOIN t_cliente as t3 ON t2.Cliente = t3.IdCliente 
        INNER JOIN t_almacen as t4 on t1.Almacen=t4.IdAlmacen
        WHERE t1.idTarja = :idTarja and t1.Almacen= :IdAlmacen
        GROUP BY t1.IdTarja, t1.IdRemision, CONVERT(DATE, t1.FechaSalida), FORMAT(t1.FechaSalida, 'hh:mm tt'), 
        t3.NombreCliente, t1.Transportista, t1.Chofer, t1.Placas, FORMAT(t1.HoraInicio, 'hh:mm tt'), 
        FORMAT(t1.HoraFinal, 'hh:mm tt'), t1.Supervisor, t1.Checador,t4.Direccion,t4.Municipio,t4.Estado,
        t4.Pais, t4.CodPostal,t4.NumRecinto, t2.IdRemision");

    $stmt->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt->bindParam(':IdAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$row) {
        die('No se encontró la tarja especificada');
    }

    $Tarja = $row->Tarja;
    $IdRemision = $row->IdRemision;
    $IdRemisionOriginal = $row->IdRemisionOriginal;
    $FechaSalida = $row->FechaSalida;
    $HoraSalida = $row->HoraSalida;
    $NombreCliente = $row->NombreCliente;
    $Transportista = $row->Transportista;
    $Chofer = $row->Chofer;
    $Placas = $row->Placas;
    $HoraInicio = $row->HoraInicio;
    $HoraFinal = $row->HoraFinal;
    $SupervisorID = $row->SupervisorID;
    $ChecadorID = $row->ChecadorID;
    $Direccion = $row->Direccion;
    $Direccion2 = $row->Direccion2;
    $CodPostal = $row->CodPostal;
    $NumRecinto = $row->NumRecinto;

    $stmt2 = $Conexion->prepare("SELECT DISTINCT(t3.NombreColaborador) as Supervisor 
        FROM t_salida as t1
        INNER JOIN t_usuario as t3 ON t1.Supervisor = t3.IdUsuario 
        WHERE idTarja = :idTarja");
    $stmt2->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt2->execute();
    $row2 = $stmt2->fetch(PDO::FETCH_OBJ);
    $Supervisor = $row2 ? $row2->Supervisor : '';

    $stmt3 = $Conexion->prepare("SELECT DISTINCT(t3.NombreColaborador) as Checador 
        FROM t_salida as t1
        INNER JOIN t_usuario as t3 ON t1.Checador = t3.IdUsuario 
        WHERE idTarja = :idTarja");
    $stmt3->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt3->execute();
    $row3 = $stmt3->fetch(PDO::FETCH_OBJ);
    $Checador = $row3 ? $row3->Checador : '';

    // Count items
    $stmt4 = $Conexion->prepare("SELECT COUNT(CodBarras) as cuenta FROM t_salida WHERE idTarja = :idTarja");
    $stmt4->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt4->execute();
    $row4 = $stmt4->fetch(PDO::FETCH_OBJ);
    $cuenta = $row4 ? $row4->cuenta : 0;

    $stmt5 = $Conexion->prepare("SELECT t1.CodBarras, t1.IdRemision, t3.IdRemision as IdRemisionOriginal, 
        CONVERT(DATE, t1.FechaProduccion) as FechaProduccion, t2.MaterialNo, 
        TRIM(CONCAT(t2.Material, t2.Shape)) as MaterialShape, t1.Piezas, t1.NumPedido, 
        t1.NetWeight, t1.GrossWeight, t1.NoTarima, t1.Checador, t1.Supervisor,
        t1.PaisOrigen as Destino
        FROM t_salida as t1 
        INNER JOIN t_articulo as t2 ON t1.IdArticulo = t2.IdArticulo
        INNER JOIN t_remision_encabezado as t3 ON t1.IdRemision = t3.IdRemisionEncabezado
        INNER JOIN t_cliente as t4 ON t3.Cliente = t4.IdCliente
        WHERE t1.IdTarja = :idTarja and t1.Almacen= :IdAlmacen ORDER BY CodBarras ASC");
    $stmt5->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt5->bindParam(':IdAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $stmt5->execute();
    $items = $stmt5->fetchAll(PDO::FETCH_OBJ);

    // OBTENER DATOS DEL PERSONAL ADICIONAL
    $stmtPersonal = $Conexion->prepare("SELECT * FROM t_salida_personal WHERE IdTarjaSalida = :idTarja AND IdAlmacen = :IdAlmacen");
    $stmtPersonal->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmtPersonal->bindParam(':IdAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $stmtPersonal->execute();
    $personalAdicional = $stmtPersonal->fetchAll(PDO::FETCH_OBJ);

    $imagenVacia = 'https://intranet.alpasamx.com/' . $RutaLocal . '/Ingresos/FirmasTransportistas/firmaVacia.png';
    $ChecadorF = $imagenVacia;
    $SupervisorF = $imagenVacia;
    $TransportistaF = $imagenVacia;

    if ($ChecadorID) {
        $stmtChecador = $Conexion->prepare("SELECT Firma FROM t_usuario WHERE IdUsuario = :checadorId");
        $stmtChecador->bindParam(':checadorId', $ChecadorID, PDO::PARAM_INT);
        $stmtChecador->execute();
        $firmaChecador = $stmtChecador->fetch(PDO::FETCH_OBJ);
        if ($firmaChecador && !empty($firmaChecador->Firma)) {
            $ChecadorF = $firmaChecador->Firma;
        }
    }

    if ($SupervisorID) {
        $stmtSupervisor = $Conexion->prepare("SELECT Firma FROM t_usuario WHERE IdUsuario = :supervisorId");
        $stmtSupervisor->bindParam(':supervisorId', $SupervisorID, PDO::PARAM_INT);
        $stmtSupervisor->execute();
        $firmaSupervisor = $stmtSupervisor->fetch(PDO::FETCH_OBJ);
        if ($firmaSupervisor && !empty($firmaSupervisor->Firma)) {
            $SupervisorF = $firmaSupervisor->Firma;
        }
    }

    $stmtTransportista = $Conexion->prepare("SELECT DISTINCT(Firma) as Firma FROM t_salida WHERE IdTarja = :idTarja and Almacen=:idAlmacen");
    $stmtTransportista->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmtTransportista->bindParam(':idAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $stmtTransportista->execute();
    $firmaTransportista = $stmtTransportista->fetch(PDO::FETCH_OBJ);
    if ($firmaTransportista && !empty($firmaTransportista->Firma)) {
        $TransportistaF = $firmaTransportista->Firma;
    }

    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Tarja de Salida ALP' . $NumRecinto . '-SAL-' . sprintf("%04d", $Tarja) . '</title>
        <style>
          @import url("https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap");
            @page {
                margin: 20px;
            }
            .header {
                text-align: center;
                margin-top: 0px;
            }

            .header2 {
                position: absolute;
                top: 50px;
            }

            .EncabezadosRem {
                position: absolute;
                top: 65px;
                margin: 0;
                padding: 0;
                right: 1px;
            }

            .EncabezadosTar {
                position: absolute;
                top: 105px;
                margin: 0;
                padding: 0;
                right: 1px;
            }

            .remisiontext {
                position: fixed;
                top: 75px;
                left: 630px;  
                margin: 0;
                padding: 0;
                color:darkorange;
                font-size: 10px;
                font-weight: bold;
            }
            .tarjatext {
                position: fixed;
                top: 115px;
                left: 630px; 
                color:darkorange;  
                margin: 0;
                padding: 0;
                font-size: 10px;
                font-weight: bold;
            }
             
            .remision {
                position: fixed;
                top: 85px;
                left: 630px;  
                margin: 0;
                padding: 0;
                font-size: 16px;
                font-weight: bold;
            }
            .tarja {
                position: fixed;
                top: 125px;
                left: 630px;   
                margin: 0;
                padding: 0;
                font-size: 16px;
                font-weight: bold;
            }
             
            .company {
                 font-family: "Arimo", sans-serif;
                font-weight: bold;
            }
            .TipoTarja {
                font-family: "Arimo", sans-serif;
                font-weight: bold;
                font-size: 30px;
                top: 50px;
            }
            
            body { 
                font-family: Arial, sans-serif;
                font-size: 11px;
                margin: 0;
                padding: 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
                font-size: 8px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 4px;
                text-align: left;
                font-size: 9px;
                text-align: center;
            }
            th {
                background-color: darkorange;
                color: white;
                text-align: center;
            }
            .footer {
                margin-top: 30px;
                page-break-inside: avoid;
            }
            .info-section {
                margin-bottom: 20px;
                margin-top: 5px;
            }
            .info-label {
                font-weight: bold;
                display: inline-block;
                margin-top: 10px;
            }
            .info-texto {
                display: inline-block;
                text-align: center;
                border-bottom: 1px solid black;
                padding-bottom: 1px;
            }
            .signature-line {
                border-top: 1px solid black;
                width: 90%;
                margin: 0 auto;
                padding-top: 5px;
                text-align: center;
            }
            .cuadro-comentarios {
                width: 740px;
                height: 50px;
                border: 3px solid darkorange; 
                background-color: transparent; 
                padding: 15px;
                margin-top: 10px;
                font-family: Arial, sans-serif;
            }
            
            .cuadro-comentarios h3 {
                margin-top: 0;
                color: darkorange;
            }
            .signature-img {
                max-height: 100px;
                max-width: 200px;
                margin-bottom: -20px;
            }
            .personal-table {
                margin-top: 20px;
                page-break-inside: avoid;
            }
            .personal-title {
                background-color: darkorange;
                color: white;
                padding: 8px;
                text-align: center;
                font-weight: bold;
                font-size: 12px;
                margin-top: 20px;
                border-radius: 5px 5px 0 0;
            }
            .section-divider {
                border-top: 2px solid darkorange;
                margin: 20px 0;
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="header">
        <div class="company"><b>ALMACENAMIENTO Y LOGISTICA PORTUARIA DE ALTAMIRA, S.A DE C.V</b></div>
        <div class="company"><b>' . $Direccion . '</b></div>
        <div class="company"><b>' . $Direccion2 . '</b></div>
        <div class="company"><b>C.P. ' . $CodPostal . ' TEL. (833) 260 64 51 Y (833) 260 94 54</b></div>
        <div class="company"><b><a href="https://www.alpasa.mx/">www.alpasa.com.mx</a></b></div>
        <div class="company"><b>R.F.C. ALP-070126-EV4</b></div>
        <div class="TipoTarja"><b>TARJA SALIDA</b></div>
    </div>';

    $Header = "../dist/img/logoalpasa.png";
    if (file_exists($Header)) {
        $imageData = file_get_contents($Header);
        $base64 = 'data:' . mime_content_type($Header) . ';base64,' . base64_encode($imageData);
        $html .= '<img class="header2" src="' . $base64 . '"width="230" height="80" />';
    }

    $html .= '
            <div class="EncabezadosRem" style="border-top: 7px solid darkorange; width: 20%; margin: 0;"></div>
             <div class="text-overlay"><h6 class="remisiontext">#Remision</h6></div>

            <div class="text-overlay"><h4 class="remision"> REM-' . htmlspecialchars($IdRemisionOriginal) . '</h4></div>

            <div class="EncabezadosTar" style="border-top: 7px solid darkorange; width: 20%; margin: 0;"></div>

             <div class="text-overlay"><h6 class="tarjatext">#Tarja</h6></div>
             <div class="text-overlay"><h5 class="tarja">ALP' . $NumRecinto . '-SAL-' . sprintf("%04d", $Tarja) . '</h5></div>
            </div>

            <div style="border-top: 7px solid darkorange; width: 100%; margin: 0;"></div>
            
            <div class="info-section">
            <div class="row">
                <div class="col" style="text-align:right;">
                <span class="info-label">Fecha:</span>
                <span class="info-texto" style="width: 100px;">' . htmlspecialchars($FechaSalida) . '</span>
                <span class="info-label">Hora: </span>
                <span class="info-texto" style="width: 100px;">' . htmlspecialchars($HoraSalida) . '</span>
                </div>
            </div>
            <div class="row">
                 <div style="display: flex; justify-content: space-between; align-items: center; text-align:center;">
                    <span class="info-label">Cliente:</span>
                     <span class="info-texto" style="width: 720px;">' . $NombreCliente . '</span>
                </div>
            </div>
            <div class="row">
                 <div style="display: flex; justify-content: space-between; align-items: center; text-align:center;">
                    <span class="info-label">Transportista:</span>
                    <span class="info-texto" style="width: 650px;">' . htmlspecialchars($Transportista) . '</span>
                </div>
            </div>
            <div class="row">
                 <div style="display: flex; justify-content: space-between; align-items: center; text-align:center;">
                <span class="info-label">Chofer:</span>
                <span class="info-texto" style="width: 335px;">' . htmlspecialchars($Chofer) . '</span>
                <span class="info-label">Placas:</span>
                <span class="info-texto" style="width: 335px;">' . htmlspecialchars($Placas) . '</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div style="display: flex; justify-content: space-between; align-items: center; text-align:center;">
                            <span class="info-label">HoraInicio:</span>
                            <span class="info-texto" style="width: 100px;">' . htmlspecialchars($HoraInicio) . '</span>
                            <span class="info-label">HoraFinal:</span>
                            <span class="info-texto" style="width: 100px;">' . htmlspecialchars($HoraFinal) . '</span>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 7px solid darkorange; width: 100%; margin: 0;"></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="auto">CODIGO BARRAS</th>
                        <th width="auto">REMISIÓN</th>
                        <th width="auto">NO. MATERIAL</th>
                        <th width="auto">MATERIAL SHAPE</th>
                        <th width="auto">PIEZAS</th>
                        <th width="auto">DESTINO</th>
                        <th width="auto">N° TARIMA</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($items as $item) {
        $html .= '
    <tr>
        <td width="auto">' . $NumRecinto . "-" . sprintf("%06d", $item->CodBarras) . '</td>
        <td width="auto">' . htmlspecialchars($item->IdRemisionOriginal) . '</td>
        <td width="auto">' . htmlspecialchars($item->MaterialNo) . '</td>
        <td width="auto">' . htmlspecialchars($item->MaterialShape) . '</td>
        <td width="auto">' . htmlspecialchars($item->Piezas) . '</td>
        <td width="auto">' . htmlspecialchars($item->Destino) . '</td>
        <td width="auto">' . htmlspecialchars($item->NoTarima) . '</td>
    </tr>';
    }

    $html .= '</tbody>
            </table>';

     // AGREGAR TABLA DE PERSONAL ADICIONAL SI EXISTEN DATOS
    if (!empty($personalAdicional)) {
        $html .= '
            <div class="section-divider"></div>
            <div class="personal-table">
                <div class="personal-title">PERSONAL ADICIONAL QUE COLABORÓ EN LA SALIDA</div>
                <table>
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="30%">NOMBRE</th>
                            <th width="30%">ROL</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($personalAdicional as $personal) {
            $html .= '
                        <tr>
                            <td width="10%">' . htmlspecialchars($personal->IdEmpleado) . '</td>
                            <td width="30%">' . htmlspecialchars($personal->Nombre) . '</td>
                            <td width="30%">' . htmlspecialchars($personal->Rol) . '</td>
                        </tr>';
        }

        $html .= '
                    </tbody>
                </table>
            </div>';
    }

    $html .= '
            <div class="footer">
                <div style="width: 100%; margin-top: 50px;">
                    <div style="float: left; width: 33%; text-align: center;">
                        <img src="' . $ChecadorF . '" class="signature-img"><br>
                        <div class="signature-line">
                            <strong>' . htmlspecialchars($Checador) . '</strong><br>
                            NOMBRE Y FIRMA DE CHECADOR
                        </div>
                    </div>
                    <div style="float: left; width: 33%; text-align: center;">
                        <img src="' . $SupervisorF . '" class="signature-img"><br>
                        <div class="signature-line">
                            <strong>' . htmlspecialchars($Supervisor) . '</strong><br>
                            NOMBRE Y FIRMA DE SUPERVISOR
                        </div>
                    </div>
                    <div style="float: left; width: 33%; text-align: center;">
                        <img src="' . $TransportistaF . '" class="signature-img"><br>
                        <div class="signature-line">
                            <strong>' . htmlspecialchars($Transportista) . '</strong><br>
                            NOMBRE Y FIRMA DE TRANSPORTISTA
                        </div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>
        </body>
    </html>';

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();

    // Enviar el PDF directamente al navegador
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="TarjaSalida_' . $Tarja . '.pdf"');
    echo $dompdf->output();
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>