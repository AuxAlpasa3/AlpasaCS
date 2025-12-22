<?php
header('Content-Type: application/json;  charset=UTF-8');

include '../api/db/conexion.php';
$IdArmado = $_GET['IdArmado'];
$NvoCodBarras = $_GET['NvoCodBarras'];

$sentencia = $Conexion->query("SELECT CodBarras,Piezas From t_armado where IdArmado=$IdArmado and NvoCodBarras=$NvoCodBarras order by CodBarras;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if (!$Query) {
  $Query = "No hay códigos.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>