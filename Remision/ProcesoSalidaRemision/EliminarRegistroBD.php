<?php
header('Content-Type: application/json; charset=UTF-8');
include '../../api/db/conexion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $IdRemisionEncabezado = $_POST['IdRemisionEncabezado'] ?? null;
        $CodBarras = $_POST['CodBarras'] ?? null;

        if (!$IdRemisionEncabezado || !$CodBarras) {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos: IdRemisionEncabezado y CodBarras']);
            exit;
        }

        $IdRemisionEncabezadoInt = (int)$IdRemisionEncabezado;
        $Conexion->beginTransaction();

        try {
            $sqlGetIdLineaRemision = "SELECT IdLinea FROM t_remision_linea 
                                     WHERE IdRemisionEncabezadoRef = :IdRemisionEncabezado 
                                     AND CodBarras = :CodBarras";
            $stmtGetIdLineaRemision = $Conexion->prepare($sqlGetIdLineaRemision);
            $stmtGetIdLineaRemision->bindParam(':IdRemisionEncabezado', $IdRemisionEncabezadoInt);
            $stmtGetIdLineaRemision->bindParam(':CodBarras', $CodBarras);
            $stmtGetIdLineaRemision->execute();
            
            $registroRemision = $stmtGetIdLineaRemision->fetch(PDO::FETCH_ASSOC);
            
            $sqlGetIdLineaPasoSalida = "SELECT IdLinea FROM t_pasosalida 
                                       WHERE IdRemision = :IdRemisionEncabezado 
                                       AND CodBarras = :CodBarras";
            $stmtGetIdLineaPasoSalida = $Conexion->prepare($sqlGetIdLineaPasoSalida);
            $stmtGetIdLineaPasoSalida->bindParam(':IdRemisionEncabezado', $IdRemisionEncabezadoInt);
            $stmtGetIdLineaPasoSalida->bindParam(':CodBarras', $CodBarras);
            $stmtGetIdLineaPasoSalida->execute();
            
            $registroPasoSalida = $stmtGetIdLineaPasoSalida->fetch(PDO::FETCH_ASSOC);
            
            if (!$registroRemision || !$registroPasoSalida) {
                throw new Exception('Registro no encontrado en una o ambas tablas');
            }
            
            $IdLineaEliminarRemision = (int)$registroRemision['IdLinea'];
            $IdLineaEliminarPasoSalida = (int)$registroPasoSalida['IdLinea'];

            $sqlUpdateRemisionLineas = "UPDATE t_remision_linea 
                                       SET IdLinea = IdLinea - 1 
                                       WHERE IdRemisionEncabezadoRef = :IdRemisionEncabezado 
                                       AND IdLinea > :IdLineaEliminar";
            $stmtUpdateRemisionLineas = $Conexion->prepare($sqlUpdateRemisionLineas);
            $stmtUpdateRemisionLineas->bindParam(':IdRemisionEncabezado', $IdRemisionEncabezadoInt);
            $stmtUpdateRemisionLineas->bindParam(':IdLineaEliminar', $IdLineaEliminarRemision);
            $stmtUpdateRemisionLineas->execute();

            $sqlUpdatePasoSalidaLineas = "UPDATE t_pasosalida 
                                         SET IdLinea = IdLinea - 1 
                                         WHERE IdRemision = :IdRemisionEncabezado 
                                         AND IdLinea > :IdLineaEliminar";
            $stmtUpdatePasoSalidaLineas = $Conexion->prepare($sqlUpdatePasoSalidaLineas);
            $stmtUpdatePasoSalidaLineas->bindParam(':IdRemisionEncabezado', $IdRemisionEncabezadoInt);
            $stmtUpdatePasoSalidaLineas->bindParam(':IdLineaEliminar', $IdLineaEliminarPasoSalida);
            $stmtUpdatePasoSalidaLineas->execute();

            $sqlRemisionLinea = "DELETE FROM t_remision_linea 
                                WHERE IdRemisionEncabezadoRef = :IdRemisionEncabezado 
                                AND CodBarras = :CodBarras";
            $stmtRemisionLinea = $Conexion->prepare($sqlRemisionLinea);
            $stmtRemisionLinea->bindParam(':IdRemisionEncabezado', $IdRemisionEncabezadoInt);
            $stmtRemisionLinea->bindParam(':CodBarras', $CodBarras);
            $stmtRemisionLinea->execute();

            $sqlPasoSalida = "DELETE FROM t_pasosalida 
                             WHERE IdRemision = :IdRemisionEncabezado 
                             AND CodBarras = :CodBarras";
            $stmtPasoSalida = $Conexion->prepare($sqlPasoSalida);
            $stmtPasoSalida->bindParam(':IdRemisionEncabezado', $IdRemisionEncabezadoInt);
            $stmtPasoSalida->bindParam(':CodBarras', $CodBarras);
            $stmtPasoSalida->execute();

            $procedureName = "Sp_Inventario_Materiales";
            $stmtProcedure = $Conexion->prepare("{CALL $procedureName}");
            $stmtProcedure->execute();

            $Conexion->commit();

            echo json_encode([
                'success' => true, 
                'message' => 'Registro eliminado con éxito.'
            ]);

        } catch (PDOException $e) {
            $Conexion->rollBack();
            throw $e;
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Método no permitido. Use POST.']);
    }

} catch (PDOException $e) {
    error_log("Error en EliminarRegistroBD: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error al eliminar el registro: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error general en EliminarRegistroBD: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error general: ' . $e->getMessage()
    ]);
}
?>