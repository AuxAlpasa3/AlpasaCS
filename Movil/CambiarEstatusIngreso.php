<?php
header('Content-Type: application/json; charset=UTF-8');
include '../api/db/conexion.php';

try {
    $IdTarja = $_POST["IdTarja"];
    $Estatus = $_POST["Estatus"];
    $IdAlmacen = $_POST["IdAlmacen"];
    $HoraFinal = date('Y-m-d H:i:s');

    $sentencia = $Conexion->prepare("
        UPDATE t_ingreso 
        SET Estatus = ?, HoraFinal = ? 
        WHERE IdTarja = ? AND Almacen = ?
    ");
    $resultado = $sentencia->execute([$Estatus, $HoraFinal, $IdTarja, $IdAlmacen]);

    if ($resultado) {
        echo json_encode(["mensaje" => "Estatus actualizado correctamente."], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["error" => "No se pudo actualizar el estatus."], JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>
