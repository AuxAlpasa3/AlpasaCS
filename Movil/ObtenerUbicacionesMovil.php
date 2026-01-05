<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include '../api/db/conexion.php';

try {
    $sentencia = $Conexion->query("SELECT IdUbicacion, NomCorto, NomLargo FROM t_ubicacion_interna");
    
    $ubicaciones = [];
    
    if ($sentencia) {
        while($row = $sentencia->fetch(PDO::FETCH_ASSOC)) {
            $ubicaciones[] = [
                "IdUbicacion" => $row["IdUbicacion"],
                "Nombre" => $row["NomCorto"],
                "Descripcion" => $row["NomLargo"] ? $row["NomLargo"] : null
            ];
        }
        echo json_encode($ubicaciones);
    } else {
        echo json_encode([]);
    }
    
} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta"]);
}

?>