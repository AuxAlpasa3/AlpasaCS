<?php
include_once "../templates/SesionP.php";
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$ZonaHoraria = getenv('ZonaHoraria');
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION');

try {

    $IdUbicacion = $_POST['IdUbicacion'];
    $IdRevision = $_POST['IdRevision'];

    // Obtener datos de la revisión
    $sentencia = $Conexion->query("SELECT t1.IdRevision,t2.TipoRevision, CONVERT(VARCHAR, (t1.fechainicio), 103) AS fechainicio,  
   CONVERT(VARCHAR, (t1.fechafinal), 103) as fechafinal, t1.descripcion from t_revision as t1 
    INNER JOIN t_tipoRevision as t2 on t1.TipoRevision=t2.IdTipoRevision where IdRevision=$IdRevision;");
    $Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if (empty($Query)) {
        die("No se encontró la revisión con ID: $IdRevision");
    }

    foreach ($Query as $row) {
        $IdRevision = $row->IdRevision;
        $TipoRevision = $row->TipoRevision;
        $fechainicio = $row->fechainicio;
        $fechafinal = $row->fechafinal;
        $descripcion = $row->descripcion;
    }

    // Obtener datos de la ubicación
    $sentencia2 = $Conexion->query("SELECT t2.Ubicacion, t1.TotalInventario,t1.TotalRevision,
    t1.TotalCoincide,t1.totalnoCoincide,t1.TotalNoExisten 
    from t_revisionubicaciones as t1 inner join t_ubicacion as t2 
    on t1.IdUbicacion=t2.IdUbicacion where IdRevision= $IdRevision  and t1.IdUbicacion=$IdUbicacion");
    $Query2 = $sentencia2->fetchAll(PDO::FETCH_OBJ);

    if (empty($Query2)) {
        die("No se encontraron datos para la ubicación ID: $IdUbicacion en la revisión ID: $IdRevision");
    }

    foreach ($Query2 as $row2) {
        $Ubicacion = $row2->Ubicacion;
        $TotalInventario = $row2->TotalInventario;
        $TotalRevision = $row2->TotalRevision;
        $TotalCoincide = $row2->TotalCoincide;
        $totalnoCoincide = $row2->totalnoCoincide;
        $TotalNoExisten = $row2->TotalNoExisten;
    }

    // Obtener total sin validar
    $sentencia3 = $Conexion->query("SELECT (TotalInventario-TotalRevision) as TotalSinValidar from t_revisionUbicaciones where IdRevision= $IdRevision  and IdUbicacion=$IdUbicacion");
    $Query3 = $sentencia3->fetchAll(PDO::FETCH_OBJ);

    if (empty($Query3)) {
        $TotalSinValidar = 0;
    } else {
        foreach ($Query3 as $row3) {
            $TotalSinValidar = $row3->TotalSinValidar;
        }
    }

    // Obtener datos para tabla de códigos coincidentes (Estado = 1)
    $sentencia5 = $Conexion->query("SELECT t2.FolioIngreso,FORMAT(t2.FechaIngreso, 'dd-MM-yyyy') as FechaIngreso, FORMAT(t2.FechaProduccion, 'dd-MM-yyyy') as FechaProduccion,TRIM(CONCAT(t3.Material, t3.Shape)) as MaterialShape, t2.MaterialNo,
    t2.Piezas, t2.NumPedido, t1.Codbarras as CodBarras1, t2.Origen, t6.NombreCliente as Cliente,
    t2.PaisOrigen, t2.NoTarima, t2.IdRemision, t1.Comentarios 
	From t_lecturaQr as t1 
	INNER JOIN t_inventario as t2 on t1.CodBarras=t2.CodBarras
	INNER JOIN t_articulo as t3 ON t2.IdArticulo = t3.IdArticulo 
    LEFT JOIN t_almacen as t5 ON t2.Almacen = t5.IdAlmacen
    LEFT JOIN t_cliente as t6 ON t2.Cliente = t6.IdCliente
    LEFT JOIN t_estadoMaterial as t7 ON t2.EstadoMaterial = t7.IdEstadoMaterial
    where t1.IdRevision=$IdRevision and t1.IdUbicacion=$IdUbicacion and t1.Estado=1  AND t2.EnProceso = 0");
    $Query5 = $sentencia5->fetchAll(PDO::FETCH_OBJ);
    $datosCoincidentes = [];
    if ($Query5) {
        foreach ($Query5 as $row) {
            $datosCoincidentes[] = array(
                'FolioIngreso1' => $row->FolioIngreso,
                'FechaIngreso1' => $row->FechaIngreso,
                'FechaProduccion1' => $row->FechaProduccion,
                'MaterialShape1' => $row->MaterialShape,
                'materilaNo1' => $row->MaterialNo,
                'piezas1' => $row->Piezas,
                'numPedido1' => $row->NumPedido,
                'CodBarras1' => 'ALP-' . sprintf("%06d", $row->CodBarras1),
                'origen1' => $row->Origen,
                'cliente1' => $row->Cliente,
                'paisOrigen1' => $row->PaisOrigen,
                'noTarima1' => $row->NoTarima,
                'idRemision1' => $row->IdRemision,
                'Comentarios1' => $row->Comentarios,
            );
        }
    }

    // Obtener datos para tabla de códigos no coincidentes (Estado <> 1)
    $sentencia6 = $Conexion->query("SELECT t2.FolioIngreso ,FORMAT(t2.FechaIngreso, 'dd-MM-yyyy') as FechaIngreso, FORMAT(t2.FechaProduccion, 'dd-MM-yyyy') as FechaProduccion,TRIM(CONCAT(t3.Material, t3.Shape)) as MaterialShape, t2.MaterialNo,
    t2.Piezas, t2.NumPedido, t1.Codbarras as CodBarras2, t2.Origen, t6.NombreCliente as Cliente,
    t2.PaisOrigen, t2.NoTarima, t2.IdRemision, t1.Comentarios 
	From t_lecturaQr as t1 
	INNER JOIN t_inventario as t2 on t1.CodBarras=t2.CodBarras
	INNER JOIN t_articulo as t3 ON t2.IdArticulo = t3.IdArticulo 
    LEFT JOIN t_almacen as t5 ON t2.Almacen = t5.IdAlmacen
    LEFT JOIN t_cliente as t6 ON t2.Cliente = t6.IdCliente
    LEFT JOIN t_estadoMaterial as t7 ON t2.EstadoMaterial = t7.IdEstadoMaterial
    where t1.IdRevision=$IdRevision and t1.IdUbicacion=$IdUbicacion and t1.Estado>1  AND t2.EnProceso = 0");
    $Query6 = $sentencia6->fetchAll(PDO::FETCH_OBJ);
    $datosNoCoincidentes = [];
    if ($Query6) {
        foreach ($Query6 as $rows) {
            $datosNoCoincidentes[] = array(
                'FolioIngreso2' => $rows->FolioIngreso,
                'FechaIngreso2' => $rows->FechaIngreso,
                'FechaProduccion2' => $rows->FechaProduccion,
                'MaterialShape2' => $rows->MaterialShape,
                'materilaNo2' => $rows->MaterialNo,
                'piezas2' => $rows->Piezas,
                'numPedido2' => $rows->NumPedido,
                'CodBarras2' => 'ALP-' . sprintf("%06d", $rows->CodBarras2),
                'origen2' => $rows->Origen,
                'cliente2' => $rows->Cliente,
                'paisOrigen2' => $rows->PaisOrigen,
                'noTarima2' => $rows->NoTarima,
                'idRemision2' => $rows->IdRemision,
                'Comentarios2' => $rows->Comentarios,
            );
        }
    }

    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>INVENTARIO FISICO VS DIGITAL</title>
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
            
                position: fixed;
                top: 30px;
            }

            .EncabezadosRev {
                position: fixed;
                top: 65px;
                margin: 0;
                padding: 0;
                right: 1px;
            }

            .EncabezadosTipos {
                position: fixed;
                top: 105px;
                margin: 0;
                padding: 0;
                right: 1px;
            }

            .revisontext {
                position: fixed;
                top: 75px;
                left: 630px;  
                margin: 0;
                padding: 0;
                color:darkorange;
                font-size: 10px;
                font-weight: bold;
            }
            .tipostext {
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
            .tipos {
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
            .Tipotipos {
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
            
            .signature-img {
                max-height: 100px;
                max-width: 200px;
                margin-bottom: -20px;
            }
            .section-title {
                background-color: #f5f5f5;
                padding: 8px;
                margin-top: 20px;
                font-weight: bold;
                border-left: 4px solid darkorange;
            }
            .subtable {
                width: 100%;
                margin-top: 10px;
            }
            .subtable th {
                background-color: darkorange;
                font-size: 8px;
            }
            .subtable td {
                font-size: 8px;
                padding: 3px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="company"><b>ALMACENAMIENTO Y LOGISTICA PORTUARIA DE ALTAMIRA, S.A DE C.V</b></div>
            <div class="company"><b> CALLE MAR ROJO No. 601 </b></div>
            <div class="company"><b> PUERTO INDUSTRIAL DE ALTAMIRA, ALTAMIRA TAMAULIPAS</b></div>
            <div class="company"><b>C.P.89603 TEL. (833) 260 64 51 Y (833) 260 94 54</b></div>
            <div class="company"><b><a href="https://www.alpasa.mx/">www.alpasa.com.mx</a></b></div>
            <div class="company"><b>R.F.C. ALP-070126-EV4</b></div>
            <div class="Tipotipos"><b>INVENTARIO FISICO VS DIGITAL</b></div>';

    $Header = "../dist/img/logoalpasa.png";
    if (file_exists($Header)) {
        $imageData = file_get_contents($Header);
        $base64 = 'data:' . mime_content_type($Header) . ';base64,' . base64_encode($imageData);
        $html .= '<img class="header2" src="' . $base64 . '"width="230" height="80" />';
    }

    $html .= '
            <div class="EncabezadosRev" style="border-top: 7px solid darkorange; width: 20%; margin: 0;"></div>
             <div class="text-overlay"><h6 class="revisontext">#Revision</h6></div>

            <div class="text-overlay"><h4 class="remision">' . htmlspecialchars($IdRevision) . '</h4></div>

            <div class="EncabezadosTipos" style="border-top: 7px solid darkorange; width: 20%; margin: 0;"></div>

             <div class="text-overlay"><h6 class="tipostext">#Tipo Revisión</h6></div>
             <div class="text-overlay"><h5 class="tipos">' . htmlspecialchars($TipoRevision) . '</h5></div>
        </div>


            <div style="border-top: 7px solid darkorange; width: 100%; margin: 0;"></div>
            

            <div class="info-section">
                <div class="row">
                    <div class="col" style="text-align:right;">
                    <span class="info-label">PERIODO DE REVISIÓN:</span>
                    <span class="info-texto" style="width: 100px;">' . htmlspecialchars($fechainicio) . '</span>
                    <span class="info-label"> HASTA: </span>
                    <span class="info-texto" style="width: 100px;">' . htmlspecialchars($fechafinal) . '</span>
                    </div>
                </div>

                <div class="row">
                    <div style="display: flex; justify-content: space-between; align-items: center; text-align:center;">
                        <span class="info-label">DESCRIPCIÓN:</span>
                        <span class="info-texto" style="width: 720px;">' . htmlspecialchars($descripcion) . '</span>
                    </div>
                </div>
                
                <div style="border-top: 7px solid darkorange; width: 100%; margin: 0;"></div>

                <div class="row">
                    <div style="display: flex; justify-content: space-between; align-items: center; text-align:center;">
                        <span class="info-label" style="width: 720px; font-size: 20px;">' . htmlspecialchars($Ubicacion) . '</span>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="auto">INVENTARIO EN SISTEMA</th>
                        <th width="auto">TOTAL NO ESCANEADOS</th>
                        <th width="auto">TOTAL DE CODIGOS REGISTRADOS</th>
                        <th width="auto">COINCIDENCIAS</th>
                        <th width="auto">NO COINCIDENTES</th>
                        <th width="auto">NO EXISTE EN INVENTARIO</th>
                    </tr>
                </thead>
                <tbody>';

    $html .= '
    <tr>
        <td width="auto">' . htmlspecialchars($TotalInventario) . '</td>
        <td width="auto">' . htmlspecialchars($TotalSinValidar) . '</td>
        <td width="auto">' . htmlspecialchars($TotalRevision) . '</td>
        <td width="auto">' . htmlspecialchars($TotalCoincide) . '</td>
        <td width="auto">' . htmlspecialchars($totalnoCoincide) . '</td>
        <td width="auto">' . htmlspecialchars($TotalNoExisten) . '</td>
    </tr>';

    $html .= '</tbody>
            </table>';

    // Tabla de códigos coincidentes (Estado = 1)
    if (!empty($datosCoincidentes)) {
        $html .= '
            <div class="section-title">CÓDIGOS COINCIDENTES (Total: ' . count($datosCoincidentes) . ')</div>
            <table class="subtable">
                <thead>
                    <tr>
                        <th width="Auto">FOLIO INGRESO</th>
                        <th width="Auto">FECHA INGRESO</th>
                        <th width="Auto">FECHA PRODUCCIÓN</th>
                        <th width="Auto">MATERIAL NO</th>
                        <th width="Auto">MATERIAL SHAPE</th>
                        <th width="Auto">PIEZAS</th>
                        <th width="Auto">NUM PEDIDO</th>
                        <th width="Auto">CÓDIGO DE BARRAS</th>
                        <th width="Auto">PAIS ORIGEN</th>
                        <th width="Auto">ORIGEN</th>
                        <th width="Auto">NO. TARIMA</th>
                        <th width="Auto">CLIENTE</th>
                        <th width="Auto">ID REMISION</th>
                        <th width="Auto">COMENTARIOS</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($datosCoincidentes as $dato) {
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($dato['FolioIngreso1']) . '</td>
                        <td>' . htmlspecialchars($dato['FechaIngreso1']) . '</td>
                        <td>' . htmlspecialchars($dato['FechaProduccion1']) . '</td>
                        <td>' . htmlspecialchars($dato['materilaNo1']) . '</td>
                        <td>' . htmlspecialchars($dato['MaterialShape1']) . '</td>
                        <td>' . htmlspecialchars($dato['piezas1']) . '</td>
                        <td>' . htmlspecialchars($dato['numPedido1']) . '</td>
                        <td>' . htmlspecialchars($dato['CodBarras1']) . '</td>
                        <td>' . htmlspecialchars($dato['paisOrigen1']) . '</td>
                        <td>' . htmlspecialchars($dato['origen1']) . '</td>
                        <td>' . htmlspecialchars($dato['noTarima1']) . '</td>
                        <td>' . htmlspecialchars($dato['cliente1']) . '</td>
                        <td>' . htmlspecialchars($dato['idRemision1']) . '</td>
                        <td>' . htmlspecialchars($dato['Comentarios1']) . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>';
    }

    // Tabla de códigos no coincidentes (Estado <> 1)
    if (!empty($datosNoCoincidentes)) {
        $html .= '
            <div class="section-title">CÓDIGOS NO COINCIDENTES (Total: ' . count($datosNoCoincidentes) . ')</div>
            <table class="subtable">
                <thead>
                    <tr>
                        <th width="Auto">FOLIO INGRESO</th>
                        <th width="Auto">FECHA INGRESO</th>
                        <th width="Auto">FECHA PRODUCCIÓN</th>
                        <th width="Auto">MATERIAL NO</th>
                        <th width="Auto">MATERIAL SHAPE</th>
                        <th width="Auto">PIEZAS</th>
                        <th width="Auto">NUM PEDIDO</th>
                        <th width="Auto">CÓDIGO DE BARRAS</th>
                        <th width="Auto">PAIS ORIGEN</th>
                        <th width="Auto">ORIGEN</th>
                        <th width="Auto">NO. TARIMA</th>
                        <th width="Auto">CLIENTE</th>
                        <th width="Auto">ID REMISION</th>
                        <th width="Auto">COMENTARIOS</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($datosNoCoincidentes as $dato) {
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($dato['FolioIngreso2']) . '</td>
                        <td>' . htmlspecialchars($dato['FechaIngreso2']) . '</td>
                        <td>' . htmlspecialchars($dato['FechaProduccion2']) . '</td>
                        <td>' . htmlspecialchars($dato['materilaNo2']) . '</td>
                        <td>' . htmlspecialchars($dato['MaterialShape2']) . '</td>
                        <td>' . htmlspecialchars($dato['piezas2']) . '</td>
                        <td>' . htmlspecialchars($dato['numPedido2']) . '</td>
                        <td>' . htmlspecialchars($dato['CodBarras2']) . '</td>
                        <td>' . htmlspecialchars($dato['paisOrigen2']) . '</td>
                        <td>' . htmlspecialchars($dato['origen2']) . '</td>
                        <td>' . htmlspecialchars($dato['noTarima2']) . '</td>
                        <td>' . htmlspecialchars($dato['cliente2']) . '</td>
                        <td>' . htmlspecialchars($dato['idRemision2']) . '</td>
                        <td>' . htmlspecialchars($dato['Comentarios2']) . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>';
    }


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
    header('Content-Disposition: inline; filename="inventario_fisico_digital_' . $IdRevision . '.pdf"');
    echo $dompdf->output();
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>