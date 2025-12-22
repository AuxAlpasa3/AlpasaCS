<?php
header('Content-Type: application/json; charset=UTF-8');
include '../../api/db/conexion.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)) {
        echo json_encode(['success' => false, 'message' => 'No se recibieron datos.']);
        exit;
    }

    if (!is_array($data)) {
        echo json_encode(['success' => false, 'message' => 'Formato de datos inválido']);
        exit;
    }

    $queryCheck = "SELECT Piezas FROM t_pasoSalida 
                   WHERE CodBarras = :CodBarras 
                   AND IdRemision = :IdRemision";

    $queryInsertPasoSalida = "INSERT INTO t_pasoSalida (IdRemision, IdLinea, CodBarras, MaterialNo, Piezas, EsArmado, Estatus) 
                   VALUES (:IdRemision, :IdLinea, :CodBarras, :MaterialNo, :piece, :EsArmado, :Estatus)";
                    
    $queryInsertRemLinea = "INSERT INTO t_remision_linea (IdRemision, IdLinea, IdArticulo, Piezas, CodBarras, Cliente, Almacen, IdRemisionEncabezadoRef) 
    VALUES (:IdRemision, :IdLinea, :IdArticulo, :piece, :CodBarras, :Cliente, :Almacen, :IdRemisionEncabezadoRef)";

    $queryMaxIdLinea = "SELECT COALESCE(MAX(IdLinea), 0) + 1 as NuevoIdLinea FROM t_pasoSalida WHERE IdRemision = :IdRemision";

    $stmtCheck = $Conexion->prepare($queryCheck);
    $stmtInsertPasoSalida = $Conexion->prepare($queryInsertPasoSalida); 
    $stmtInsertRemLinea = $Conexion->prepare($queryInsertRemLinea);
    $stmtMaxIdLinea = $Conexion->prepare($queryMaxIdLinea);

    $IdRemisionEncabezado = $data[0]['IdRemisionEncabezado'] ?? null;
    
    if (!$IdRemisionEncabezado) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó IdRemisionEncabezado']);
        exit;
    }

    $Conexion->beginTransaction();

    try {
        $stmtMaxIdLinea->bindParam(':IdRemision', $IdRemisionEncabezado);
        $stmtMaxIdLinea->execute();
        $result = $stmtMaxIdLinea->fetch(PDO::FETCH_ASSOC);
        $IdLinea = $result['NuevoIdLinea'] ?? 1;

        $registrosProcesados = 0;
        $errores = [];

        foreach ($data as $index => $registro) {
            $camposRequeridos = [
                'IdRemision', 'CodBarras', 'MaterialNo', 'piece', 
                'EsArmado', 'Estatus', 'IdRemisionEncabezado', 
                'Almacen', 'IdArticulo', 'IdCliente'
            ];

            $camposFaltantes = [];
            foreach ($camposRequeridos as $campo) {
                if (!isset($registro[$campo]) || $registro[$campo] === '') {
                    $camposFaltantes[] = $campo;
                }
            }

            if (!empty($camposFaltantes)) {
                $errores[] = "Registro $index incompleto - faltan campos: " . implode(', ', $camposFaltantes);
                continue;
            }

            $IdRemisionEncabezado = $registro['IdRemisionEncabezado'];
            $IdRemision = $registro['IdRemision'];
            $Almacen = $registro['Almacen'];
            $CodBarras = $registro['CodBarras'];
            $MaterialNo = $registro['MaterialNo'];
            $piece = $registro['piece'];
            $EsArmado = $registro['EsArmado'];
            $Estatus = $registro['Estatus'];
            $IdArticulo = $registro['IdArticulo'];
            $IdCliente = $registro['IdCliente'];

            if (!is_numeric($piece) || $piece <= 0) {
                $errores[] = "Registro $index - Piezas debe ser un número positivo";
                continue;
            }

            $stmtCheck->bindParam(':CodBarras', $CodBarras);
            $stmtCheck->bindParam(':IdRemision', $IdRemisionEncabezado);
            $stmtCheck->execute();
            $existingPieces = $stmtCheck->fetchColumn();

            if ($existingPieces !== false) {
                $errores[] = "El CodBarras $CodBarras ya existe en la Salida";
                continue;
            }

            $stmtInsertRemLinea->bindParam(':IdRemision', $IdRemision);
            $stmtInsertRemLinea->bindParam(':IdLinea', $IdLinea);
            $stmtInsertRemLinea->bindParam(':IdArticulo', $IdArticulo);
            $stmtInsertRemLinea->bindParam(':piece', $piece);
            $stmtInsertRemLinea->bindParam(':CodBarras', $CodBarras);
            $stmtInsertRemLinea->bindParam(':Cliente', $IdCliente);
            $stmtInsertRemLinea->bindParam(':Almacen', $Almacen);
            $stmtInsertRemLinea->bindParam(':IdRemisionEncabezadoRef', $IdRemisionEncabezado);
            
            if (!$stmtInsertRemLinea->execute()) {
                $errores[] = "Error al insertar en t_remision_linea para $CodBarras";
                continue;
            }

            $stmtInsertPasoSalida->bindParam(':IdRemision', $IdRemisionEncabezado);
            $stmtInsertPasoSalida->bindParam(':IdLinea', $IdLinea);
            $stmtInsertPasoSalida->bindParam(':CodBarras', $CodBarras);
            $stmtInsertPasoSalida->bindParam(':MaterialNo', $MaterialNo);
            $stmtInsertPasoSalida->bindParam(':piece', $piece);
            $stmtInsertPasoSalida->bindParam(':EsArmado', $EsArmado);
            $stmtInsertPasoSalida->bindParam(':Estatus', $Estatus);
            
            if (!$stmtInsertPasoSalida->execute()) {
                $errores[] = "Error al insertar en t_pasoSalida para $CodBarras";
                continue;
            }

            try {
                $procedureName = "Sp_Inventario_Materiales";
                $stmtProcedure = $Conexion->prepare("{CALL $procedureName}");
                $stmtProcedure->execute();
            } catch (PDOException $e) {
                error_log("Error en procedimiento almacenado: " . $e->getMessage());
            }

            $registrosProcesados++;
            $IdLinea++;
        }

        if (!empty($errores)) {
            $Conexion->rollBack();
            echo json_encode([
                'success' => false, 
                'message' => 'Se procesaron ' . $registrosProcesados . ' registros, pero hubo errores: ' . implode(', ', $errores)
            ]);
        } else {
            $Conexion->commit();
            echo json_encode([
                'success' => true, 
                'message' => 'Se procesaron exitosamente ' . $registrosProcesados . ' registros'
            ]);
        }

    } catch (Exception $e) {
        $Conexion->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log("Error en AgregarRegistroBD: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Error general en AgregarRegistroBD: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error general: ' . $e->getMessage()]);
}
?>