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
    $sentencia = $Conexion->prepare("SELECT Sesion FROM t_usuario WHERE IdUsuario = ? AND Sesion != ''");
    $sentencia->execute([$idUsuario]);
    
    if ($sentencia->rowCount() > 0) {
        echo json_encode("Sesion activa", JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode("Sesion inactiva", JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    echo json_encode("Error al verificar la sesión: " . $e->getMessage(), JSON_UNESCAPED_UNICODE);
}
?>