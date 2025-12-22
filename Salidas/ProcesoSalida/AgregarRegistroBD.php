<?php
header('Content-Type: application/json; charset=UTF-8');
include '../../api/db/conexion.php';
try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!empty($data)) {
        $queryCheck = "SELECT Piezas FROM t_pasoSalida 
                       WHERE CodBarras = :CodBarras 
                       AND IdRemision = :IdRemision 
                       AND IdLinea = :IdLinea 
                       AND MaterialNo = :MaterialNo";

        $queryInsert = "INSERT INTO t_pasoSalida (IdRemision, IdLinea, CodBarras, MaterialNo, Piezas, EsArmado,Estatus) 
                         VALUES (:IdRemision, :IdLinea, :CodBarras, :MaterialNo, :piece, :EsArmado,:Estatus)";

        $queryUpdate = "UPDATE t_pasoSalida 
                        SET Piezas = Piezas + :piece,
                            EsArmado = :EsArmado
                        WHERE CodBarras = :CodBarras 
                        AND IdRemision = :IdRemision 
                        AND IdLinea = :IdLinea 
                        AND MaterialNo = :MaterialNo";

        $stmtCheck = $Conexion->prepare($queryCheck);
        $stmtInsert = $Conexion->prepare($queryInsert);
        $stmtUpdate = $Conexion->prepare($queryUpdate);

        foreach ($data as $registro) {
            if (
                isset($registro['IdRemision']) && isset($registro['id']) &&
                isset($registro['CodBarras']) && isset($registro['MaterialNo']) &&
                isset($registro['piece']) && isset($registro['EsArmado'])
            ) {

                $IdRemision = $registro['IdRemision'];
                $IdLinea = $registro['id'];
                $CodBarras = $registro['CodBarras'];
                $MaterialNo = $registro['MaterialNo'];
                $piece = $registro['piece'];
                $EsArmado = $registro['EsArmado'];
                $Estatus = 1;

                $stmtCheck->bindParam(':CodBarras', $CodBarras);
                $stmtCheck->bindParam(':IdRemision', $IdRemision);
                $stmtCheck->bindParam(':IdLinea', $IdLinea);
                $stmtCheck->bindParam(':MaterialNo', $MaterialNo);
                $stmtCheck->execute();
                $existingPieces = $stmtCheck->fetchColumn();

                if ($existingPieces !== false) {
                    $stmtUpdate->bindParam(':piece', $piece);
                    $stmtUpdate->bindParam(':EsArmado', $EsArmado);
                    $stmtUpdate->bindParam(':CodBarras', $CodBarras);
                    $stmtUpdate->bindParam(':IdRemision', $IdRemision);
                    $stmtUpdate->bindParam(':IdLinea', $IdLinea);
                    $stmtUpdate->bindParam(':MaterialNo', $MaterialNo);
                    $stmtUpdate->execute();
                } else {
                    $stmtInsert->bindParam(':IdRemision', $IdRemision);
                    $stmtInsert->bindParam(':IdLinea', $IdLinea);
                    $stmtInsert->bindParam(':CodBarras', $CodBarras);
                    $stmtInsert->bindParam(':MaterialNo', $MaterialNo);
                    $stmtInsert->bindParam(':piece', $piece);
                    $stmtInsert->bindParam(':EsArmado', $EsArmado);
                    $stmtInsert->bindParam(':Estatus', $Estatus);
                    $stmtInsert->execute();
                    $procedureName = "Sp_Inventario_Materiales";
                    $stmt2 = $Conexion->prepare("{CALL $procedureName}");
                    $stmt2->execute();
                }
            }
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se recibieron datos.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>