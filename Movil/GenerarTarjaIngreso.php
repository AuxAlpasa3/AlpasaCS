<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 120);

// Función de logging mejorada
// function //debug_log($message) {
//     $timestamp = date('Y-m-d H:i:s');
//     $logMessage = "[{$timestamp}] {$message}\n";
//     file_put_contents('debug_impresion.log', $logMessage, FILE_APPEND);
//     error_log($message);
// }

if (headers_sent($filename, $linenum)) {
    die("Error: Headers already sent in $filename on line $linenum");
}

ob_start();

include '../api/db/conexion.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$ZonaHoraria = getenv('ZonaHoraria') ?: 'America/Mexico_City';
date_default_timezone_set($ZonaHoraria);
$RutaLocal = getenv('VERSION') ?: '';

//debug_log("🚀 Iniciando script de impresión de tarja");

// Función para verificar impresora por IP
function verificarImpresora($printerIP) {
    //debug_log("🔍 Verificando conectividad con impresora: {$printerIP}");
    
    if (empty($printerIP)) {
        //debug_log("❌ IP de impresora vacía");
        return false;
    }

    $printerIP = trim($printerIP);
    
    // Verificar ping
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = "ping -n 2 -w 1000 {$printerIP} 2>&1";
    } else {
        $command = "ping -c 2 -W 1 {$printerIP} 2>&1";
    }
    
    exec($command, $output, $returnCode);
    $pingConectado = $returnCode === 0;
    //debug_log("🏁 Resultado ping: " . ($pingConectado ? "CONECTADO" : "NO CONECTADO"));
    
    // Verificar puerto 9100 (puerto estándar de impresoras)
    $timeout = 3;
    $fp = @fsockopen($printerIP, 9100, $errno, $errstr, $timeout);
    if ($fp) {
        fclose($fp);
        //debug_log("🏁 Puerto 9100: ABIERTO");
        return true;
    } else {
        //debug_log("❌ Puerto 9100: CERRADO - {$errstr} ({$errno})");
        return $pingConectado; // Si responde al ping, asumimos que está conectada
    }
}

// Función para obtener solo la IP de la impresora
function obtenerIPImpresora($Conexion, $nombreImpresora, $idAlmacen) {
    //debug_log("📋 Buscando IP de impresora: {$nombreImpresora} para almacén {$idAlmacen}");
    
    $stmt = $Conexion->prepare("SELECT IPImpresora FROM t_impresoras WHERE IdImpresora = :impresora AND Almacen = :idAlmacen");
    $stmt->bindParam(':impresora', $nombreImpresora, PDO::PARAM_STR);
    $stmt->bindParam(':idAlmacen', $idAlmacen, PDO::PARAM_INT);
    $stmt->execute();
    $impresoraData = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$impresoraData) {
        throw new Exception("Impresora '$nombreImpresora' no encontrada para el almacén $idAlmacen");
    }
    
    //debug_log("✅ IP de impresora encontrada: {$impresoraData->IPImpresora}");
    
    return $impresoraData->IPImpresora;
}

// Función para imprimir PDF usando solo IP
function imprimirPDFPorIP($pdfOutput, $printerIP) {
    //debug_log("🖨️ Iniciando impresión por IP: {$printerIP}");
    
    // Crear archivo temporal
    $tempDir = sys_get_temp_dir();
    $tempFile = tempnam($tempDir, 'tarja_') . '.pdf';
    
    //debug_log("📄 Guardando PDF temporal en: {$tempFile}");
    
    if (file_put_contents($tempFile, $pdfOutput) === false) {
        throw new Exception("No se pudo crear archivo temporal para impresión");
    }
    
    if (!file_exists($tempFile)) {
        throw new Exception("Archivo temporal no se creó correctamente");
    }
    
    $fileSize = filesize($tempFile);
    //debug_log("✅ PDF temporal creado: {$fileSize} bytes");

    $resultados = [];
    
    try {
        // MÉTODO 1: Envío directo RAW por puerto 9100 (MÁS CONFIABLE)
        //debug_log("📡 Intentando envío RAW por puerto 9100...");
        $resultadoRaw = enviarPDFRaw($pdfOutput, $printerIP);
        $resultados['raw_socket'] = $resultadoRaw;
        
        if ($resultadoRaw['exitoso']) {
            //debug_log("✅ Envío RAW exitoso");
            return $resultadoRaw;
        }
        
        // MÉTODO 2: Usar comando de impresión con IP directa (Windows)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            //debug_log("🖥️ Intentando impresión Windows por IP...");
            
            // Método 2.1: Usar nombre genérico con IP
            $printerPath = "\\\\{$printerIP}\\PDF"; // Nombre genérico
            $command = "print /D:\"{$printerPath}\" \"{$tempFile}\" 2>&1";
            exec($command, $output, $returnCode);
            
            $resultados['print_ip'] = [
                'exitoso' => $returnCode === 0,
                'codigo' => $returnCode,
                'mensaje' => implode(" | ", $output),
                'comando' => $command
            ];
            
            if ($returnCode === 0) {
                //debug_log("✅ Impresión por IP exitosa");
                return $resultados['print_ip'];
            }
            
            // Método 2.2: PowerShell con IP directa
            //debug_log("🔧 Intentando PowerShell con IP...");
            $psCommand = "powershell -Command \"
                `$file = '{$tempFile}'
                `$printerIP = '{$printerIP}'
                
                # Crear objeto de impresión directa
                try {
                    `$process = New-Object System.Diagnostics.Process
                    `$process.StartInfo.FileName = `$file
                    `$process.StartInfo.Verb = 'Print'
                    `$process.StartInfo.CreateNoWindow = `$true
                    `$process.StartInfo.UseShellExecute = `$true
                    `$process.StartInfo.WindowStyle = [System.Diagnostics.ProcessWindowStyle]::Hidden
                    
                    `$process.Start() | Out-Null
                    `$process.WaitForExit(30000)
                    
                    if (!`$process.HasExited) {
                        `$process.Kill()
                        Write-Output 'ERROR: Timeout'
                        exit 2
                    }
                    Write-Output 'EXITO: Impresión enviada'
                    exit 0
                } catch {
                    Write-Output ('ERROR: ' + `$_.Exception.Message)
                    exit 3
                }
            \" 2>&1";
            
            exec($psCommand, $output, $returnCode);
            $resultados['powershell_ip'] = [
                'exitoso' => $returnCode === 0,
                'codigo' => $returnCode,
                'mensaje' => implode(" | ", $output),
                'comando' => 'PowerShell IP directa'
            ];
        }
        
        // MÉTODO 3: Linux - usar lpr con IP
        else {
            //debug_log("🐧 Intentando impresión Linux por IP...");
            $command = "lpr -H {$printerIP} -P raw \"{$tempFile}\" 2>&1";
            exec($command, $output, $returnCode);
            
            $resultados['linux_lpr'] = [
                'exitoso' => $returnCode === 0,
                'codigo' => $returnCode,
                'mensaje' => implode(" | ", $output),
                'comando' => $command
            ];
        }
        
        // Buscar algún método exitoso
        foreach ($resultados as $metodo => $resultado) {
            if ($resultado['exitoso']) {
                //debug_log("✅ Método exitoso: {$metodo}");
                return $resultado;
            }
        }
        
        // Si ningún método funcionó, devolver el primer error
        $primerResultado = reset($resultados);
        //debug_log("❌ Todos los métodos fallaron");
        return $primerResultado;
        
    } finally {
        // Limpiar archivo temporal
        sleep(2);
        if (file_exists($tempFile)) {
            if (unlink($tempFile)) {
                //debug_log("🧹 Archivo temporal eliminado");
            } else {
                //debug_log("⚠️ No se pudo eliminar archivo temporal");
            }
        }
    }
}

// Función para enviar PDF como datos RAW a impresora por IP
function enviarPDFRaw($pdfData, $printerIP, $port = 9100) {
    //debug_log("📡 Enviando PDF RAW a {$printerIP}:{$port}");
    
    try {
        $timeout = 10;
        $socket = @fsockopen($printerIP, $port, $errno, $errstr, $timeout);
        
        if (!$socket) {
            return [
                'exitoso' => false,
                'codigo' => $errno,
                'mensaje' => "Error conectando al puerto {$port}: {$errstr}",
                'comando' => "RAW TCP {$printerIP}:{$port}"
            ];
        }
        
        // Configurar timeout
        stream_set_timeout($socket, $timeout);
        
        // Enviar datos PDF directamente
        $bytesWritten = fwrite($socket, $pdfData);
        fclose($socket);
        
        if ($bytesWritten === false) {
            return [
                'exitoso' => false,
                'codigo' => -1,
                'mensaje' => "Error al escribir en el socket",
                'comando' => "RAW TCP {$printerIP}:{$port}"
            ];
        }
        
        if ($bytesWritten < strlen($pdfData)) {
            return [
                'exitoso' => false,
                'codigo' => -1,
                'mensaje' => "Solo se enviaron {$bytesWritten}/" . strlen($pdfData) . " bytes",
                'comando' => "RAW TCP {$printerIP}:{$port}"
            ];
        }
        
        //debug_log("✅ Envío RAW exitoso: {$bytesWritten} bytes enviados a {$printerIP}:{$port}");
        return [
            'exitoso' => true,
            'codigo' => 0,
            'mensaje' => "PDF enviado exitosamente ({$bytesWritten} bytes)",
            'comando' => "RAW TCP {$printerIP}:{$port}"
        ];
        
    } catch (Exception $e) {
        return [
            'exitoso' => false,
            'codigo' => -1,
            'mensaje' => "Excepción: " . $e->getMessage(),
            'comando' => "RAW TCP {$printerIP}:{$port}"
        ];
    }
}

try {
     
      $IdTarja = (int) $_POST['IdTarja'];
      $IdAlmacen = (int) $_POST['IdAlmacen'];
      $Impresora = $_POST['Impresora'];

    $printerIP = obtenerIPImpresora($Conexion, $Impresora, $IdAlmacen);
    //debug_log("🖨️ Usando IP de impresora: {$printerIP}");

    if (!verificarImpresora($printerIP)) {
        //debug_log("❌ ERROR: Impresora {$printerIP} no está accesible");
        throw new Exception("La impresora {$printerIP} no está accesible en la red. Verifique la conexión.");
    } else {
        //debug_log("✅ Impresora verificada y accesible");
    }

    $stmt = $Conexion->prepare("SELECT t1.IdTarja as Tarja, t1.IdRemision, t2.IdRemision AS IdRemisionOriginal, CONVERT(DATE, t1.FechaIngreso) as FechaIngreso, 
        FORMAT(t1.HoraInicio, 'hh:mm tt') as HoraIngreso, t3.NombreCliente, t1.Transportista, t1.Chofer, t1.Placas, 
        FORMAT(t1.HoraInicio, 'hh:mm tt') as HoraInicio, FORMAT(t1.HoraFinal, 'hh:mm tt') as HoraFinal, 
        t1.Supervisor as SupervisorID, t1.Checador as ChecadorID, t4.Direccion,
        CONCAT(t4.Municipio,', ',t4.Estado,', ',t4.Pais) as Direccion2, t4.CodPostal,t4.NumRecinto
        FROM t_ingreso as t1 
        INNER JOIN t_remision_encabezado as t2 ON t2.IdRemisionEncabezado  = t1.IdRemision 
        INNER JOIN t_cliente as t3 ON t2.Cliente = t3.IdCliente 
        INNER JOIN t_almacen as t4 on t1.Almacen=t4.IdAlmacen
        WHERE t1.idTarja = :idTarja and t1.Almacen= :IdAlmacen
        GROUP BY t1.IdTarja, t1.IdRemision, CONVERT(DATE, t1.FechaIngreso), FORMAT(t1.HoraInicio, 'hh:mm tt'), 
        t3.NombreCliente, t1.Transportista, t1.Chofer, t1.Placas, FORMAT(t1.HoraInicio, 'hh:mm tt'), 
        FORMAT(t1.HoraFinal, 'hh:mm tt'), t1.Supervisor, t1.Checador,t4.Direccion,t4.Municipio,t4.Estado,
        t4.Pais, t4.CodPostal,t4.NumRecinto, t2.IdRemision,t1.Origen");

    $stmt->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt->bindParam(':IdAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$row) {
        throw new Exception('No se encontró la tarja especificada');
    }

    //debug_log("✅ Datos principales de tarja obtenidos");

    $Tarja = $row->Tarja;
    $IdRemision = $row->IdRemision;
    $IdRemisionOriginal = $row->IdRemisionOriginal;
    $FechaIngreso = $row->FechaIngreso;
    $HoraIngreso = $row->HoraIngreso;
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

    // Obtener nombre del supervisor
    $stmt2 = $Conexion->prepare("SELECT DISTINCT(t3.NombreColaborador) as Supervisor 
        FROM t_ingreso as t1
        INNER JOIN t_usuario as t3 ON t1.Supervisor = t3.IdUsuario 
        WHERE idTarja = :idTarja");
    $stmt2->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt2->execute();
    $row2 = $stmt2->fetch(PDO::FETCH_OBJ);
    $Supervisor = $row2 ? $row2->Supervisor : '';

    // Obtener nombre del checador
    $stmt3 = $Conexion->prepare("SELECT DISTINCT(t3.NombreColaborador) as Checador 
        FROM t_ingreso as t1
        INNER JOIN t_usuario as t3 ON t1.Checador = t3.IdUsuario 
        WHERE idTarja = :idTarja");
    $stmt3->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt3->execute();
    $row3 = $stmt3->fetch(PDO::FETCH_OBJ);
    $Checador = $row3 ? $row3->Checador : '';

    // Obtener comentarios
    $stmt10 = $Conexion->prepare("WITH ComentariosFiltrados AS (
        SELECT STRING_AGG(CONCAT('$NumRecinto','-', RIGHT('000000' + CAST(CodBarras AS VARCHAR(6)), 6)), ', ') AS codigos_barras, 
        comentarios FROM t_ingreso WHERE idTarja = :idTarja AND comentarios IS NOT NULL AND 
        comentarios <> ' ' AND comentarios <> 'SIN COMENTARIOS' GROUP BY comentarios)
        SELECT CONCAT('CodBarras: ', codigos_barras, ': ', comentarios) AS resultado FROM ComentariosFiltrados 
        UNION ALL 
        SELECT 'SIN COMENTARIOS' AS resultado WHERE NOT EXISTS (SELECT 1 FROM ComentariosFiltrados)");
    $stmt10->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt10->execute();
    $row10 = $stmt10->fetch(PDO::FETCH_OBJ);
    $Comentarios = $row10 ? $row10->resultado : 'SIN COMENTARIOS';

    // Contar items
    $stmt4 = $Conexion->prepare("SELECT COUNT(CodBarras) as cuenta FROM t_ingreso WHERE idTarja = :idTarja");
    $stmt4->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt4->execute();
    $row4 = $stmt4->fetch(PDO::FETCH_OBJ);
    $cuenta = $row4 ? $row4->cuenta : 0;

    //debug_log("📊 Tarja contiene {$cuenta} items");

    // Obtener detalles de items
    $stmt5 = $Conexion->prepare("SELECT  t1.CodBarras, t1.IdRemision,t3.IdRemision as IdRemisionOriginal, CONVERT(DATE, t1.FechaIngreso) as FechaIngreso, CONVERT(DATE, t1.FechaProduccion) as FechaProduccion, t2.MaterialNo, TRIM(CONCAT(t2.Material, t2.Shape)) as MaterialShape, t1.Piezas, t1.NumPedido, t1.NetWeight, t1.GrossWeight, t5.Ubicacion, t1.NoTarima, t1.Checador, t1.Supervisor, STUFF((
        SELECT ', ' + tem.EstadoMaterial 
        FROM STRING_SPLIT(t1.EstadoMercancia, ',') estado 
        INNER JOIN t_estadoMaterial tem 
            ON TRY_CAST(estado.value AS INT) = tem.IdEstadoMaterial 
        FOR XML PATH('')), 1, 2, '') as EstadoMercancia,
    t1.EstadoMercancia as EstadosIds, t1.Origen as Destino
    FROM t_ingreso as t1 
    INNER JOIN t_articulo as t2 ON t1.IdArticulo = t2.IdArticulo
    INNER JOIN t_remision_encabezado as t3 ON t1.IdRemision = t3.IdRemisionEncabezado
    INNER JOIN t_cliente as t4 ON t3.Cliente = t4.IdCliente
    INNER JOIN t_ubicacion as t5 ON t1.IdUbicacion = t5.IdUbicacion
    WHERE t1.IdTarja = :idTarja and t1.Almacen= :IdAlmacen ORDER BY CodBarras ASC");
    $stmt5->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmt5->bindParam(':IdAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $stmt5->execute();
    $items = $stmt5->fetchAll(PDO::FETCH_OBJ);

    //debug_log("✅ {$cuenta} items obtenidos de la base de datos");

    // OBTENER DATOS DEL PERSONAL ADICIONAL
    $stmtPersonal = $Conexion->prepare("SELECT * FROM t_ingreso_personal WHERE IdTarjaIngreso = :idTarja AND IdAlmacen = :IdAlmacen");
    $stmtPersonal->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmtPersonal->bindParam(':IdAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $stmtPersonal->execute();
    $personalAdicional = $stmtPersonal->fetchAll(PDO::FETCH_OBJ);

    if (!empty($personalAdicional)) {
        //debug_log("👥 Personal adicional encontrado: " . count($personalAdicional) . " personas");
    }

    // Obtener firmas
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
            //debug_log("✅ Firma de checador obtenida");
        }
    }

    if ($SupervisorID) {
        $stmtSupervisor = $Conexion->prepare("SELECT Firma FROM t_usuario WHERE IdUsuario = :supervisorId");
        $stmtSupervisor->bindParam(':supervisorId', $SupervisorID, PDO::PARAM_INT);
        $stmtSupervisor->execute();
        $firmaSupervisor = $stmtSupervisor->fetch(PDO::FETCH_OBJ);
        if ($firmaSupervisor && !empty($firmaSupervisor->Firma)) {
            $SupervisorF = $firmaSupervisor->Firma;
            //debug_log("✅ Firma de supervisor obtenida");
        }
    }

    $stmtTransportista = $Conexion->prepare("SELECT DISTINCT(Firma) as Firma FROM t_ingreso WHERE IdTarja = :idTarja and Almacen=:idAlmacen");
    $stmtTransportista->bindParam(':idTarja', $IdTarja, PDO::PARAM_INT);
    $stmtTransportista->bindParam(':idAlmacen', $IdAlmacen, PDO::PARAM_INT);
    $stmtTransportista->execute();
    $firmaTransportista = $stmtTransportista->fetch(PDO::FETCH_OBJ);
    if ($firmaTransportista && !empty($firmaTransportista->Firma)) {
        $TransportistaF = $firmaTransportista->Firma;
        //debug_log("✅ Firma de transportista obtenida");
    }

    // Generar HTML del PDF
    //debug_log("📝 Generando HTML para PDF...");
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Tarja de Ingreso ALP' . $NumRecinto . '-ING-' . sprintf("%04d", $Tarja) . '</title>
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
        <div class="TipoTarja"><b>TARJA INGRESO</b></div>
    </div>';

    // Agregar logo
    $Header = "../dist/img/logoalpasa.png";
    if (file_exists($Header)) {
        $imageData = file_get_contents($Header);
        $base64 = 'data:' . mime_content_type($Header) . ';base64,' . base64_encode($imageData);
        $html .= '<img class="header2" src="' . $base64 . '"width="230" height="80" />';
        //debug_log("✅ Logo agregado al PDF");
    }

    $html .= '
            <div class="EncabezadosRem" style="border-top: 7px solid darkorange; width: 20%; margin: 0;"></div>
             <div class="text-overlay"><h6 class="remisiontext">#Remision</h6></div>

            <div class="text-overlay"><h4 class="remision"> REM-' . htmlspecialchars($IdRemisionOriginal) . '</h4></div>

            <div class="EncabezadosTar" style="border-top: 7px solid darkorange; width: 20%; margin: 0;"></div>

             <div class="text-overlay"><h6 class="tarjatext">#Tarja</h6></div>
             <div class="text-overlay"><h5 class="tarja">ALP' . $NumRecinto . '-ING-' . sprintf("%04d", $Tarja) . '</h5></div>
            </div>

            <div style="border-top: 7px solid darkorange; width: 100%; margin: 0;"></div>
            
            <div class="info-section">
            <div class="row">
                <div class="col" style="text-align:right;">
                <span class="info-label">Fecha:</span>
                <span class="info-texto" style="width: 100px;">' . htmlspecialchars($FechaIngreso) . '</span>
                <span class="info-label">Hora: </span>
                <span class="info-texto" style="width: 100px;">' . htmlspecialchars($HoraIngreso) . '</span>
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
                        <th width="auto">UBICACIÓN</th>
                        <th width="auto">DESTINO</th>
                        <th width="auto">N° TARIMA</th>
                        <th width="auto">ESTADO MATERIAL</th>
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
        <td width="auto">' . htmlspecialchars($item->Ubicacion) . '</td>
        <td width="auto">' . htmlspecialchars($item->Destino) . '</td>
        <td width="auto">' . htmlspecialchars($item->NoTarima) . '</td>
        <td width="auto">' . htmlspecialchars($item->EstadoMercancia) . '</td>
    </tr>';
    }

    $html .= '</tbody>
            </table>

            <div class="row">
                <div class="col" style="text-align: Center;">
                    <div class="cuadro-comentarios">
                        <span class="info-label">Comentarios:</span>
                        <span class="info-label">' . htmlspecialchars($Comentarios) . '</span>
                    </div>
                </div>
            </div>';

    // AGREGAR TABLA DE PERSONAL ADICIONAL SI EXISTEN DATOS
    if (!empty($personalAdicional)) {
        $html .= '
            <div class="section-divider"></div>
            <div class="personal-table">
                <div class="personal-title">PERSONAL ADICIONAL QUE COLABORÓ EN EL INGRESO</div>
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

    //debug_log("✅ HTML generado exitosamente");

    // Configurar Dompdf
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'Arial');
    $options->set('chroot', realpath('..'));

    $dompdf = new Dompdf($options);
    
    //debug_log("📝 Cargando HTML en Dompdf");
    $dompdf->loadHtml($html);
    $dompdf->setPaper('Letter', 'portrait');
    
    //debug_log("🎨 Renderizando PDF...");
    $dompdf->render();
    
    // Obtener el output del PDF
    $pdfOutput = $dompdf->output();
    
    if (empty($pdfOutput)) {
        throw new Exception("Error: No se pudo generar el PDF - contenido vacío");
    }
    
    //debug_log("✅ PDF generado exitosamente: " . strlen($pdfOutput) . " bytes");

    // Intentar imprimir el PDF usando solo la IP
    //debug_log("🖨️ Enviando PDF por IP: {$printerIP}");
    $resultadoImpresion = imprimirPDFPorIP($pdfOutput, $printerIP);
    
    // Preparar respuesta
    $respuesta = [
        'estado' => $resultadoImpresion['exitoso'] ? 'exito' : 'advertencia',
        'mensaje' => $resultadoImpresion['exitoso'] ? 'Tarja impresa exitosamente' : 'PDF generado pero hubo problemas con la impresión',
        'tarja' => 'ALP' . $NumRecinto . '-ING-' . sprintf("%04d", $Tarja),
        'remision' => 'REM-' . $IdRemisionOriginal,
        'impresora_ip' => $printerIP,
        'detalles_impresion' => $resultadoImpresion
    ];

    // Limpiar buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Enviar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($respuesta);
    //debug_log("📤 Respuesta enviada: " . ($resultadoImpresion['exitoso'] ? 'ÉXITO' : 'ADVERTENCIA'));
    exit;

} catch (Exception $e) {
    //debug_log("❌ Error: " . $e->getMessage());
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    echo json_encode([
        'estado' => 'error',
        'mensaje' => $e->getMessage()
    ]);
    exit;
}
?>