<?php
header('Content-Type: application/json; charset=UTF-8');
include_once "../api/db/conexion.php";

function respondWithError($message)
{
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

function respondWithSuccess($message)
{
    echo json_encode(['success' => true, 'message' => $message]);
    exit;
}

if (isset($_POST['mov'])) {
    switch ($_POST['mov']) {
        case 'INGRESAR':
            INGRESAR();
            break;
        default:
            respondWithError('Operación no válida');
    }
}


        // Obtener el siguiente IdTarja disponible
        $stmt = $Conexion->prepare("SELECT ISNULL(MAX(IdTarja), 0) + 1 AS IdTarja 
            FROM t_ingreso
            WHERE Almacen = ?");
        $stmt->execute([$Almacen]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $IdTarja = $result->IdTarja;

        $stmt = $Conexion->prepare("SELECT 
                t7.idLinea, t1.IdRemisionEncabezado, t1.IdRemision, t7.IdArticulo, t7.Piezas, 
                t8.NetWeightUnit, t1.Cliente, t1.Transportista, t1.Placas, 
                t1.Chofer, t1.FechaRemision, t1.Almacen
            FROM t_remision_encabezado AS t1 
            INNER JOIN t_remision_Linea AS t7 ON t1.IdRemision = t7.IdRemision AND t1.IdRemisionEncabezado = t7.IdRemisionEncabezadoRef
            INNER JOIN t_articulo AS t8 ON t7.IdArticulo = t8.IdArticulo
            INNER JOIN t_usuario_almacen AS t3 ON t1.Almacen = t3.IdAlmacen
            WHERE t1.Estatus IN (0, 1) AND t1.IdRemisionEncabezado = ? AND t3.Idusuario = ?
            ORDER BY IdLinea");
        $stmt->execute([$IdRemisionEncabezado, $IdUsuario]);
        $Remisiones = $stmt->fetchAll(PDO::FETCH_OBJ);

        if (empty($Remisiones)) {
            respondWithError('No se encontraron datos para la remisión especificada');
        }

        // Obtener el siguiente código de barras disponible
        $stmt = $Conexion->prepare("SELECT 
            COALESCE(MAX(isnull(CodBarras,0)), 0) + 1 AS NextCodBarras
        FROM (
            SELECT CodBarras FROM t_ingreso WHERE Almacen = ?
            UNION ALL
            SELECT NvoCodBarras AS CodBarras FROM t_armado WHERE Almacen = ?
        ) t");
        $stmt->execute([$Almacen, $Almacen]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $CodBarrasBase = $result->NextCodBarras;
$stmt = $Conexion->prepare("SELECT t7.IdLinea, t1.IdRemision, t7.IdArticulo, t7.Piezas, 
                t8.NetWeightUnit, t1.Cliente, t1.Transportista, t1.Placas, t7.Booking,
                t1.Chofer, t1.FechaRemision,t1.Contenedor,t1.Caja,t1.Sellos,t1.Tracto,t1.Supervisor,t7.IdRemisionEncabezadoRef
            FROM t_remision_encabezado AS t1 
            INNER JOIN t_remision_Linea AS t7 ON t1.IdRemision=t7.IdRemision
            INNER JOIN t_articulo AS t8 ON t7.IdArticulo=t8.IdArticulo
            WHERE t1.Estatus IN(0,1) AND t1.IdRemisionEncabezado = ? 
            ORDER BY t7.idremisionEncabezadoref");
        $stmt->execute([$IdRemisionEncabezado]);
        $Remisiones = $stmt->fetchAll(PDO::FETCH_OBJ);

        if (empty($Remisiones)) {
            respondWithError('No se encontraron datos para la remisión especificada');
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

        $Conexion->beginTransaction();

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

        $stmt = $Conexion->prepare("UPDATE t_remision_encabezado SET Estatus = ? WHERE IdRemisionEncabezado = ?");
        $result = $stmt->execute([2, $IdRemisionEncabezado]);

        if (!$result) {
            throw new Exception("Error al actualizar estatus de la remisión");
        }

        $consulta = "INSERT INTO t_bitacora (Tabla, Movimiento, Fecha, Consulta, Usuario) 
                    VALUES (?, ?, ?, ?, ?)";
        $stmt = $Conexion->prepare($consulta);
        $stmt->execute([
            't_ingreso',
            'AgregarIngreso' . $IdTarja,
            $fechahora,
            "Ingreso de remisión $IdRemisionEncabezado",
            $usuario
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