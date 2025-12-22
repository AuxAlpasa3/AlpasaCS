<?php
header('Content-Type: application/json; charset=UTF-8');
include '../../api/db/conexion.php';

try {
    $fechahora = date('Ymd H:i:s');
    $User = $_POST['user'] ?? null;  
    $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'] ?? null;
    $IdRemision = $_POST['IdRemision'] ?? null; 
    $Almacen = $_POST['Almacen'] ?? null;
    $tipoTransporte = $_POST['IdTransporte'] ?? null; 
    $Booking = $_POST['Booking'] ?? null;
    
    
    if (!$Almacen) {
        throw new Exception('Almacén no proporcionado');
    }
    
    if (!$User) {
        throw new Exception('Usuario no proporcionado');
    }

    $Conexion->beginTransaction();

    $stmt = $Conexion->prepare("SELECT COALESCE(MAX(IdTarja), 0) + 1 AS IdTarja FROM t_salida WHERE Almacen = ?");
    $stmt->execute([$Almacen]);
    $CONT = $stmt->fetchColumn();

    if (!$CONT) {
        throw new Exception('Error al generar IdTarja');
    }

    $stmt = $Conexion->prepare("SELECT IdUsuario FROM t_usuario WHERE IdUsuario = ?");
    $stmt->execute([$User]);
    $usuario = $stmt->fetchColumn();

    if (!$usuario) {
        throw new Exception('Usuario no válido');
    }

    $stmt = $Conexion->prepare("SELECT ps.IdRemision, ps.IdLinea, ps.EsArmado 
                               FROM t_pasoSalida ps
                               INNER JOIN t_remision_encabezado re ON ps.IdRemision = re.IdRemisionEncabezado
                               WHERE re.IdRemisionEncabezado = ? 
                               GROUP BY ps.IdRemision, ps.IdLinea, ps.EsArmado");
    $stmt->execute([$IdRemisionEncabezado]);
    $lineas = $stmt->fetchAll(PDO::FETCH_OBJ);

    if (empty($lineas)) {
        throw new Exception('No se encontraron líneas para procesar');
    }

    $stmtFotografia = $Conexion->prepare("INSERT INTO t_fotografias_Encabezado 
        (IdTarja, FechaIngreso, Tipo, Almacen, Estatus) 
        VALUES (?, ?, ?, ?, ?)");

    $resultFotografia = $stmtFotografia->execute([
        $CONT,
        $fechahora,
        3,
        $Almacen,
        0
    ]);

    if (!$resultFotografia) {
        throw new Exception("Error al insertar registro en t_fotografia_Encabezado");
    }


    foreach ($lineas as $linea) {
        $IdRemision = $linea->IdRemision;
        $IdLinea = $linea->IdLinea;
        $EsArmado = $linea->EsArmado;

        $stmt = $Conexion->prepare("SELECT MaterialNo, CodBarras, Piezas FROM t_pasoSalida WHERE IdRemision = ? AND IdLinea = ?");
        $stmt->execute([$IdRemision, $IdLinea]); 
        $datosLinea = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$datosLinea) {
            throw new Exception("No se encontraron datos para la línea $IdLinea de la remisión $IdRemision");
        }

        $CodBarras = $datosLinea->CodBarras;
        $Piezas = $datosLinea->Piezas;
        $MaterialNo = $datosLinea->MaterialNo;

        $stmt = $Conexion->prepare("SELECT COALESCE(MAX(IdLinea), 0) + 1 FROM t_salida WHERE IdTarja = ? AND IdRemision = ?");
        $stmt->execute([$CONT, $IdRemision]);
        $IdLinea2 = $stmt->fetchColumn();

        if ($EsArmado == 0) {
            $sql = "INSERT INTO t_salida (
                IdTarja, IdLinea, IdRemision, IdArticulo, CodBarras, Piezas, FechaSalida, 
                FechaProduccion, NumPedido, NetWeight, GrossWeight, Cliente, HoraInicio, 
                Transportista, Placas, Chofer, Supervisor, Almacen, Estatus, validado, NoTarima, Visible, 
                Alto, Ancho, Largo, Booking
            ) SELECT 
                ?, ?, ?, t4.IdArticulo, ?, ?, ?, t4.FechaProduccion, t4.NumPedido, 
                (t5.NetWeightUnit * ?), (t5.NetWeightUnit * ?) + 21, 
                t2.Cliente, ?, t2.Transportista, t2.Placas, t2.Chofer, ?, t4.Almacen, 2, 0, t4.NoTarima, 1,
                t4.Alto, t4.Ancho, t4.Largo,?
            FROM t_pasoSalida AS t1
            INNER JOIN t_remision_encabezado AS t2 ON t1.IdRemision = t2.IdRemisionEncabezado
            INNER JOIN t_remision_linea AS t3 ON t1.IdRemision = t3.IdRemisionEncabezadoRef AND t1.IdLinea = t3.IdLinea
            INNER JOIN t_ingreso AS t4 ON t1.CodBarras = t4.CodBarras
            INNER JOIN t_articulo AS t5 ON t5.MaterialNo = t1.MaterialNo
            WHERE t2.IdRemisionEncabezado = ? AND t1.IdLinea = ? AND t4.CodBarras = ? 
            GROUP BY t1.CodBarras, t4.IdArticulo, t4.FechaProduccion, t4.NumPedido, t5.NetWeightUnit, 
                     t2.Cliente, t2.Transportista, t2.Placas, t2.Chofer, t4.Almacen, t4.NoTarima, t4.Alto, t4.Ancho, t4.Largo";

        } else {
            $sql = "INSERT INTO t_salida (
                IdTarja, IdLinea, IdRemision, IdArticulo, CodBarras, Piezas, FechaSalida, 
                FechaProduccion, NumPedido, NetWeight, GrossWeight, Cliente, HoraInicio, 
                Transportista, Placas, Chofer, Supervisor, Almacen, Estatus, validado, NoTarima, Visible,
                Alto, Ancho, Largo, Booking
            ) SELECT 
                ?, ?, ?, t2.IdArticulo, ?, ?, ?, MIN(t2.FechaProduccion), MIN(t2.NumPedido),
                (t4.NetWeightUnit * ?), (t4.NetWeightUnit * ?) + 21,
                t5.Cliente, ?, t5.Transportista, t5.Placas, t5.Chofer, ?, t2.Almacen, 2, 0, t2.NoTarima, 1,
                 t2.Alto, t2.Ancho, t2.Largo,?
            FROM t_pasoSalida AS t1
            INNER JOIN t_ingreso_armado AS t2 ON t1.CodBarras = t2.CodBarras
            INNER JOIN t_articulo AS t4 ON t4.MaterialNo = t1.MaterialNo
            INNER JOIN t_remision_encabezado AS t5 ON t1.IdRemision = t5.IdRemisionEncabezado
            INNER JOIN t_remision_linea AS t6 ON t1.IdRemision = t6.IdRemisionEncabezadoRef AND t1.IdLinea = t6.IdLinea
            WHERE t1.IdRemision = ? AND t1.IdLinea = ? AND t2.CodBarras = ?
            GROUP BY t2.IdArticulo, t4.NetWeightUnit, t5.Cliente, t5.Transportista, t5.Placas, 
                     t5.Chofer, t2.Almacen, t2.NoTarima,t2.Alto, t2.Ancho, t2.Largo";
        }

        $params = [
            $CONT, $IdLinea2, $IdRemision, $CodBarras, $Piezas, $fechahora, 
            $Piezas, $Piezas, $fechahora, $usuario, $Booking, $IdRemision, $IdLinea, $CodBarras
        ];
        
        $stmt = $Conexion->prepare($sql);
        if (!$stmt->execute($params)) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error al insertar en t_salida para línea $IdLinea: " . ($errorInfo[2] ?? 'Error desconocido'));
        }

       
        $stmt = $Conexion->prepare("UPDATE t_remision_linea SET CodBarras = ?, Booking = ?  WHERE IdRemisionEncabezadoRef = ? AND IdLinea = ?");
        if (!$stmt->execute([$CodBarras,$Booking , $IdRemision, $IdLinea])) {
            throw new Exception("Error al actualizar t_remision_linea para línea $IdLinea");
        }

        $stmt = $Conexion->prepare("UPDATE t_pasoSalida SET Estatus = 5 WHERE CodBarras = ? AND IdRemision = ? AND IdLinea = ?");
        if (!$stmt->execute([$CodBarras, $IdRemision, $IdLinea])) {
            throw new Exception("Error al actualizar t_pasoSalida para línea $IdLinea");
        }

        if ($EsArmado == 1) {
            $stmt = $Conexion->prepare("UPDATE t_armado SET Estatus = 4, IdTarja = ?, IdRemision = ? WHERE NvoCodBarras = ?");
            if (!$stmt->execute([$CONT, $IdRemision, $CodBarras])) {
                throw new Exception("Error al actualizar t_armado para código $CodBarras");
            }
        }

        $stmt = $Conexion->prepare("UPDATE t_remision_encabezado SET Estatus = 2, IdTransporte= ? WHERE IdRemisionEncabezado = ?");
        if (!$stmt->execute([$tipoTransporte ,$IdRemision])) {
            throw new Exception("Error al actualizar estado de remisión");
        }
    }

    $Conexion->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Se ha generado la Salida correctamente',
        'redirect' => '../../../Salidas/SalidasPendientes.php',
        'IdTarja' => $CONT 
    ]);

} catch (Exception $e) {
    if (isset($Conexion) && $Conexion->inTransaction()) {
        $Conexion->rollBack();
    }

    error_log("Error en generación de salida: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'redirect' => '../Index.php'
    ]);
}
?>