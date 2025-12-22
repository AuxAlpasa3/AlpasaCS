<?php
header('Content-Type: application/json; charset=UTF-8');
include_once "../api/db/conexion.php";

function respondWithError($message) {
    http_response_code(400);
    $response = ['success' => false, 'message' => $message];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

function respondWithSuccess($message) {
    $response = ['success' => true, 'message' => $message];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// VERIFICAR MÉTODO POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondWithError('Método no permitido. Se requiere POST.');
}

if (!isset($_POST['mov'])) {
    respondWithError('Falta el parámetro mov');
}

if ($_POST['mov'] === 'PROCESAR_COMPLETO') {
    PROCESAR_REMISION_COMPLETA();
} else {
    respondWithError('Operación no válida. Use PROCESAR_COMPLETO');
}

function PROCESAR_REMISION_COMPLETA() {
    global $Conexion;
    
    try {
        $camposRequeridos = ['IdRemisionEncabezado', 'Almacen', 'Transportista', 'Placas', 'Chofer', 'IdUsuario'];
        foreach ($camposRequeridos as $campo) {
            if (empty($_POST[$campo])) {
                respondWithError("Falta campo requerido: $campo");
            }
        }

        $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'];
        $Almacen = $_POST['Almacen'];
        $Transportista = $_POST['Transportista'];
        $Placas = $_POST['Placas'];
        $Chofer = $_POST['Chofer'];
        $IdUsuario = $_POST['IdUsuario'];
        $Contenedor = $_POST['Contenedor'] ?? '';
        $Sellos = $_POST['Sellos'] ?? '';
        $Tracto = $_POST['Tracto'] ?? '';
        $Caja = $_POST['Caja'] ?? '';

        $Piezas = [];
        if (isset($_POST['Piezas']) && !empty($_POST['Piezas'])) {
            $piezas_json = $_POST['Piezas'];
            $Piezas = json_decode($piezas_json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                respondWithError('Error al decodificar JSON de piezas: ' . json_last_error_msg());
            }
        }

        if (!$Conexion) {
            respondWithError('Error de conexión a la base de datos');
        }

        $Conexion->beginTransaction();

        $stmt = $Conexion->prepare(
            "UPDATE t_remision_encabezado 
             SET Transportista = ?, Placas = ?, Chofer = ?, Contenedor = ?, Sellos = ?, Tracto = ?, Caja = ? 
             WHERE IdRemisionEncabezado = ? AND Almacen = ?"
        );
        
        if (!$stmt) {
            throw new Exception('Error al preparar consulta de actualización');
        }
        
        $resultado = $stmt->execute([
            $Transportista, $Placas, $Chofer, $Contenedor, $Sellos, $Tracto, $Caja, 
            $IdRemisionEncabezado, $Almacen
        ]);

        if (!$resultado) {
            throw new Exception('Error al actualizar las remisiones agrupadas');
        }

        if (is_array($Piezas) && count($Piezas) > 0) {
            foreach ($Piezas as $item) {
                if (isset($item['IdLinea']) && isset($item['Piezas'])) {
                    $IdLinea = $item['IdLinea'];
                    $PiezasCantidad = $item['Piezas'];

                    $stmtUpdate = $Conexion->prepare(
                        "UPDATE t_remision_linea 
                         SET Piezas = ? 
                         WHERE IdLinea = ? AND Almacen = ?"
                    );
                    
                    if (!$stmtUpdate) {
                        throw new Exception('Error al preparar consulta de actualización de piezas');
                    }
                    
                    $resultadoUpdate = $stmtUpdate->execute([
                        $PiezasCantidad, $IdLinea, $Almacen
                    ]);

                    if (!$resultadoUpdate) {
                        throw new Exception('Error al actualizar las piezas de la línea ' . $IdLinea);
                    }
                }
            }
        }

        
        $ZonaHoraria = getenv('ZonaHoraria');
        date_default_timezone_set($ZonaHoraria);
        $fechahora = date('Ymd H:i:s');

        $stmt = $Conexion->query("SELECT ISNULL(MAX(IdTarja),0) + 1 AS IdTarja FROM t_ingreso where Almacen= $Almacen");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $IdTarja = $result->IdTarja;

        $stmt = $Conexion->prepare("SELECT t7.IdLinea, t1.IdRemision, t7.IdArticulo, t7.Piezas, 
                t8.NetWeightUnit, t1.Cliente, t1.Transportista, t1.Placas, 
                t1.Chofer, t1.FechaRemision, t1.Contenedor, t1.Caja, t1.Sellos, t1.Tracto, t1.Supervisor, t7.IdRemisionEncabezadoRef,t7.Booking
            FROM t_remision_encabezado AS t1 
            INNER JOIN t_remision_Linea AS t7 ON t1.IdRemision=t7.IdRemision
            INNER JOIN t_articulo AS t8 ON t7.IdArticulo=t8.IdArticulo
            WHERE t1.Estatus IN(0,1) AND t1.IdRemisionEncabezado = ? 
            ORDER BY t7.idremisionEncabezadoref");
        $stmt->execute([$IdRemisionEncabezado]);
        $Remisiones = $stmt->fetchAll(PDO::FETCH_OBJ);

        if (empty($Remisiones)) {
            throw new Exception('No se encontraron datos para la remisión especificada');
        }

        $stmt = $Conexion->query("
            SELECT MAX(a.CodBarras) + 1 AS NextCodBarras
            FROM (
                SELECT CodBarras FROM t_ingreso
                UNION ALL
                SELECT NvoCodBarras FROM t_armado
            ) a
            LEFT JOIN (
                SELECT CodBarras FROM t_ingreso
                UNION ALL
                SELECT NvoCodBarras FROM t_armado
            ) b ON a.CodBarras + 1 = b.CodBarras
            WHERE b.CodBarras IS NULL
        ");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $CodBarrasBase = $result->NextCodBarras;

        $stmtFotografia = $Conexion->prepare("INSERT INTO t_fotografias_Encabezado 
            (IdTarja, FechaIngreso, Tipo, Almacen, Estatus) 
            VALUES (?, ?, ?, ?, ?)");

        $resultFotografia = $stmtFotografia->execute([
            $IdTarja,
            $fechahora,
            1,
            $Almacen,
            0
        ]);

        if (!$resultFotografia) {
            throw new Exception("Error al insertar registro en t_fotografia_Encabezado");
        }

        $stmtInsert = $Conexion->prepare("INSERT INTO t_ingreso 
            (IdTarja, IdLinea, IdRemision, IdArticulo, CodBarras, Piezas, FechaIngreso, 
            NetWeight, GrossWeight, Cliente, HoraInicio, Transportista, Placas, Chofer, 
            Almacen, Estatus, EstadoMercancia, Supervisor, Visible, Booking) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($Remisiones as $index => $Remision) {
            $NetWeight = $Remision->NetWeightUnit * $Remision->Piezas;
            $intNetWeight = (int) ceil($NetWeight);
            $CodBarras = $CodBarrasBase + $index;

            $result = $stmtInsert->execute([
                $IdTarja,
                $Remision->IdLinea,
                $Remision->IdRemisionEncabezadoRef,
                $Remision->IdArticulo,
                $CodBarras,
                $Remision->Piezas,
                $Remision->FechaRemision,
                $intNetWeight,
                $intNetWeight + 21,
                $Remision->Cliente,
                $fechahora,
                $Remision->Transportista,
                $Remision->Placas,
                $Remision->Chofer,
                $Almacen,
                0,
                1,
                $Remision->Supervisor,
                1,
                $Remision->Booking
            ]);

            if (!$result) {
                throw new Exception("Error al insertar registro de ingreso");
            }

            // Actualizar remisión línea con código de barras
            $stmtUpdateRemision = $Conexion->prepare("UPDATE t_remision_linea SET CodBarras = ? 
            WHERE IdRemision = ? AND IdArticulo = ? and IdRemisionEncabezadoRef= ? ");

            $result = $stmtUpdateRemision->execute([
                $CodBarras,
                $Remision->IdRemision,
                $Remision->IdArticulo,
                $Remision->IdRemisionEncabezadoRef
            ]);

            if (!$result) {
                throw new Exception("Error al actualizar línea de remisión");
            }
        }

        // Actualizar estatus de la remisión
        $stmt = $Conexion->prepare("UPDATE t_remision_encabezado SET Estatus = ? WHERE IdRemisionEncabezado = ?");
        $result = $stmt->execute([2, $IdRemisionEncabezado]);

        if (!$result) {
            throw new Exception("Error al actualizar estatus de la remisión");
        }

        // Registrar en bitácora
        $consulta = "INSERT INTO t_bitacora (Tabla, Movimiento, Fecha, Consulta, Usuario) 
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $Conexion->prepare($consulta);
        $stmt->execute([
            't_ingreso',
            'AgregarIngreso' . $IdTarja,
            $fechahora,
            "Ingreso de remisión agrupada $IdRemisionEncabezado",
            $IdUsuario
        ]);

        $Conexion->commit();
        
        respondWithSuccess('Se ha registrado el ingreso correctamente');

    } catch (PDOException $e) {
        if (isset($Conexion)) {
            $Conexion->rollBack();
        }
        respondWithError('Error de base de datos: ' . $e->getMessage());
    } catch (Exception $e) {
        if (isset($Conexion)) {
            $Conexion->rollBack();
        }
        respondWithError($e->getMessage());
    } finally {
        if (isset($Conexion)) {
            $Conexion = null;
        }
    }
}
?>