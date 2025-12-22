<?php
header('Content-Type: application/json; charset=UTF-8');
include '../../api/db/conexion.php';

try {
    $fechahora = date('Ymd H:i:s');
    $User  = $_POST['user'] ?? null;
    $IdRemision = $_POST['IdRemision'] ?? null;
    $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'] ?? null;
    $Almacen = $_POST['Almacen'] ?? null;
    
    if (!$IdRemision) {
        throw new Exception('ID de remisión no proporcionado');
    }

    $Conexion->beginTransaction();

    $stmt = $Conexion->query("SELECT COALESCE(MAX(t1.IdTarja), 0) + 1 AS IdTarja FROM t_salida as t1 INNER JOIN t_usuario_almacen as t2 on t1.Almacen=t2.IdAlmacen where t1.Almacen=$Almacen  and t2.IdUsuario=$User");
    $CONT = $stmt->fetchColumn();

    $stmt = $Conexion->prepare("SELECT IdLinea, EsArmado FROM t_pasoSalida WHERE IdRemision = ? GROUP BY IdLinea, EsArmado");
    $stmt->execute([$IdRemisionEncabezado]);
    $lineas = $stmt->fetchAll(PDO::FETCH_OBJ);

    foreach ($lineas as $linea) {
        $IdLinea = $linea->IdLinea;
        $EsArmado = $linea->EsArmado;

        $stmt = $Conexion->prepare("SELECT MaterialNo, CodBarras, Piezas FROM t_pasoSalida WHERE IdRemision = ? AND IdLinea = ?");
        $stmt->execute([$IdRemisionEncabezado, $IdLinea]);
        $datosLinea = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$datosLinea) {
            throw new Exception("No se encontraron datos para la línea $IdLinea");
        }

        $CodBarras = $datosLinea->CodBarras;
        $Piezas = $datosLinea->Piezas;

        $stmt = $Conexion->prepare("SELECT COALESCE(MAX(IdLinea), 0) + 1 FROM t_salida WHERE IdTarja = ? AND IdRemision = ?");
        $stmt->execute([$CONT, $IdRemisionEncabezado]);
        $IdLinea2 = $stmt->fetchColumn();

        if ($EsArmado == 0) {
            $sql = "INSERT INTO t_salida (
                IdTarja, IdLinea, IdRemision, IdArticulo, CodBarras, Piezas, FechaSalida, 
                FechaProduccion, NumPedido, NetWeight, GrossWeight, Cliente, HoraInicio, 
                Transportista, Placas, Chofer, Supervisor, Almacen, Estatus, validado, NoTarima, Visible
            ) SELECT 
                ?, ?, ?, t4.IdArticulo, ?, ?, ?, t4.FechaProduccion, t4.NumPedido, 
                (t5.NetWeightUnit * ?), (t5.NetWeightUnit * ?) + 21, 
                t2.Cliente, ?, t2.Transportista, t2.Placas, t2.Chofer, ?, t4.Almacen, 2, 0, t4.NoTarima, 1
            FROM t_pasoSalida AS t1
            INNER JOIN t_remision_encabezado AS t2 ON t1.IdRemision = t2.IdRemisionEncabezado
            INNER JOIN t_remision_linea AS t3 ON t1.IdLinea = t3.IdLinea
            INNER JOIN t_ingreso AS t4 ON t1.CodBarras = t4.CodBarras
            INNER JOIN t_articulo AS t5 ON t5.MaterialNo = t1.MaterialNo
            WHERE t2.IdRemisionEncabezado = ? AND t1.IdLinea = ? AND t4.CodBarras = ? and t4.Almacen=?
            group by t1.CodBarras,t4.IdArticulo,t4.FechaProduccion,t4.NumPedido,t5.NetWeightUnit,t2.Cliente,t2.Transportista,t2.Placas,
            t2.Chofer,t4.Almacen,t4.NoTarima";
        } else {
            $sql = "INSERT INTO t_salida (
                IdTarja, IdLinea, IdRemision, IdArticulo, CodBarras, Piezas, FechaSalida, 
                FechaProduccion, NumPedido, NetWeight, GrossWeight, Cliente, HoraInicio, 
                Transportista, Placas, Chofer, Supervisor, Almacen, Estatus, validado, NoTarima, Visible
            ) SELECT 
                ?, ?, ?, t2.IdArticulo, ?, ?, ?, MIN(t2.FechaProduccion), MIN(t2.NumPedido),
                (t4.NetWeightUnit * ?), (t4.NetWeightUnit * ?) + 21,
                t5.Cliente, ?, t5.Transportista, t5.Placas, t5.Chofer, ?, t2.Almacen, 2, 0, t2.NoTarima, 1
            FROM t_pasoSalida AS t1
            INNER JOIN t_ingreso_armado AS t2 ON t1.CodBarras = t2.CodBarras
            INNER JOIN t_articulo AS t4 ON t4.MaterialNo = t1.MaterialNo
            INNER JOIN t_remision_encabezado AS t5 ON t1.IdRemision = t5.IdRemisionEncabezado
            INNER JOIN t_remision_linea AS t6 ON t1.IdRemision = t6.IdRemisionEncabezadoRef AND t1.IdLinea = t6.IdLinea
            WHERE t1.IdRemision = ? AND t1.IdLinea = ? AND t2.CodBarras = ? and t4.Almacen=?
            group by t2.IdArticulo,t4.NetWeightUnit,t5.Cliente,t5.Transportista,t5.Placas,t5.Chofer,t2.Almacen,t2.NoTarima";
        }

        // Use $User instead of undefined $usuario variable
        $params = $EsArmado == 0 ? 
            [$CONT, $IdLinea2, $IdRemisionEncabezado, $CodBarras, $Piezas, $fechahora, $Piezas, $Piezas, $fechahora, $User, $IdRemisionEncabezado, $IdLinea, $CodBarras, $Almacen] :
            [$CONT, $IdLinea2, $IdRemisionEncabezado, $CodBarras, $Piezas, $fechahora, $Piezas, $Piezas, $fechahora, $User, $IdRemisionEncabezado, $IdLinea, $CodBarras, $Almacen];
        
        $stmt = $Conexion->prepare($sql);
        if (!$stmt->execute($params)) {
            throw new Exception("Error al insertar en t_salida para línea $IdLinea");
        }

        // Actualizar remisión línea
        $stmt = $Conexion->prepare("UPDATE t_remision_linea SET CodBarras = ? WHERE IdRemision = ? AND IdLinea = ? and Almacen=?");
        if (!$stmt->execute([$CodBarras, $IdRemision, $IdLinea, $Almacen])) {
            throw new Exception("Error al actualizar t_remision_linea para línea $IdLinea");
        }

        // Actualizar paso salida
        $stmt = $Conexion->prepare("UPDATE t_pasoSalida SET Estatus = 5 WHERE CodBarras = ? AND IdRemision = ? AND IdLinea = ?");
        if (!$stmt->execute([$CodBarras, $IdRemisionEncabezado, $IdLinea])) {
            throw new Exception("Error al actualizar t_pasoSalida para línea $IdLinea");
        }

        if ($EsArmado == 1) {
            // Actualizar armado
            $stmt = $Conexion->prepare("UPDATE t_armado SET Estatus = 4, IdTarja = ?, IdRemision = ? WHERE NvoCodBarras = ? and Almacen=?");
            if (!$stmt->execute([$CONT, $IdRemisionEncabezado, $CodBarras, $Almacen])) {
                throw new Exception("Error al actualizar t_armado para código $CodBarras");
            }
        }
    }

    $stmt = $Conexion->prepare("UPDATE t_remision_encabezado SET Estatus = 2 WHERE IdRemisionEncabezado = ? and Almacen=?");
    if (!$stmt->execute([$IdRemisionEncabezado, $Almacen])) {
        throw new Exception("Error al actualizar estado de remisión");
    }

    $Conexion->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Se ha generado la Salida correctamente',
        'redirect' => '../SalidasPendientes.php'
    ]);

} catch (Exception $e) {
    if ($Conexion->inTransaction()) {
        $Conexion->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'redirect' => '../SalidasPendientes.php'
    ]);
}