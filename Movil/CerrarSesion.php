<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
Include '../api/db/conexion.php';

$idUsuario = $_GET['idUsuario'];

if (empty($idUsuario)) {
    echo json_encode("Parámetro idUsuario requerido", JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sentencia = $Conexion->prepare("UPDATE t_usuario SET Sesion = '' WHERE IdUsuario = ?");
    $sentencia->execute([$idUsuario]);
    
    if ($sentencia->rowCount() > 0) {
        echo json_encode("Sesión cerrada correctamente", JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode("No se pudo cerrar la sesión", JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    echo json_encode("Error al cerrar la sesión: " . $e->getMessage(), JSON_UNESCAPED_UNICODE);
}
?>