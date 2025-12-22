<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
Include '../api/db/conexion.php';

    $idUsuario = $_GET['idUsuario'];
    $almacen = $_GET['almacen'];
    $ZonaHoraria = getenv('ZonaHoraria');
    date_default_timezone_set($ZonaHoraria);
    $fechahora = date('Ymd H:i:s');
if (empty($idUsuario) || empty($almacen)) {
    echo json_encode("Parámetros incompletos", JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sentencia = $Conexion->prepare("UPDATE t_usuario SET Sesion = ? , UltimaSesion = ? WHERE IdUsuario = ?");
    $sentencia->execute([$almacen, $fechahora,$idUsuario]);
    
    if ($sentencia->rowCount() > 0) {
        echo json_encode("Sesión actualizada correctamente", JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode("No se pudo actualizar la sesión", JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    echo json_encode("Error al actualizar la sesión: " . $e->getMessage(), JSON_UNESCAPED_UNICODE);
}
?>