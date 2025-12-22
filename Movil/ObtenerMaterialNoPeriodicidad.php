<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
//$IdTarja = $_GET['IdTarja'];

$sentencia = $Conexion->query("SELECT distinct(IdArticulo), MaterialNo from t_inventario ti WHERE MaterialNo  is not null and ti.piezas>0;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>