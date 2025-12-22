<?php
header('Content-Type: application/json; charset=UTF-8');

Include '../api/db/conexion.php';

$IdRemisionAgrupada = isset($_GET['IdRemisionAgrupada']) ? $_GET['IdRemisionAgrupada'] : '';
$Almacen = isset($_GET['Almacen']) ? $_GET['Almacen'] : '';

if (empty($IdRemisionAgrupada) || empty($Almacen)) {
    echo json_encode(array("error" => "Parámetros requeridos: IdRemisionAgrupada y Almacen"), JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "SELECT 
        IdRemisionEncabezado,IdRemision FROM t_remision_encabezado 
    WHERE IdRemisionAgrupada = :IdRemisionAgrupada
    AND Almacen = :Almacen
    ORDER BY IdRemision";
    
    $sentencia = $Conexion->prepare($sql);
    $sentencia->bindParam(':IdRemisionAgrupada', $IdRemisionAgrupada);
    $sentencia->bindParam(':Almacen', $Almacen);
    $sentencia->execute();
    
    $resultados = $sentencia->fetchAll(PDO::FETCH_OBJ);

    if(count($resultados) > 0) {
        echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array("message" => "No se encontraron remisiones individuales para los parámetros proporcionados."), JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    echo json_encode(array("error" => "Error en la base de datos: " . $e->getMessage()), JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(array("error" => "Error general: " . $e->getMessage()), JSON_UNESCAPED_UNICODE);
}
?>