<?php
header('Content-Type: application/json;  charset=UTF-8');
Include '../api/db/conexion.php';

$IdRevision = $_POST["IdRevision"];
$Estatus = $_POST["Estatus"];

$sentencia = $Conexion->prepare("UPDATE t_Revision set Estatus=$Estatus WHERE IdRevision = $IdRevision");
$resultado = $sentencia->execute([ $Estatus,$IdRevision]);
if($resultado){
  $mensaje = "Actualizado Correctamente.";
}

echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
?>