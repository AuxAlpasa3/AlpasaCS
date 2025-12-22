<?php
header('Content-Type: application/json;  charset=UTF-8');
include '../../api/db/conexion.php';

if (isset($_POST['idLote']) && isset($_POST['IdRemision']) && isset($_POST['CodBarras'])) {
    $idLote = (int) $_POST['idLote'];
    $idRemision = $_POST['IdRemision'];
    $codBarras = $_POST['CodBarras'];

    $sql = "DELETE FROM t_pasoSalida WHERE IdLinea= :idLote and IdRemision=:idRemision and CodBarras=:codBarras";
    $stmt = $Conexion->prepare($sql);
    $stmt->bindParam(':idLote', $idLote);
    $stmt->bindParam(':idRemision', $idRemision);
    $stmt->bindParam(':codBarras', $codBarras);

    if ($stmt->execute()) {
        $procedureName = "Sp_Inventario_Materiales";
        $stmtProc = $Conexion->prepare("{CALL $procedureName}");
        $stmtProc->execute();
        echo "Registro eliminado con éxito.";
    } else {
        echo "Error al eliminar el registro.";
    }
}

?>