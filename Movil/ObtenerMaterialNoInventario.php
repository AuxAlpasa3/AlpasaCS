<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
$IdAlmacen = $_GET['IdAlmacen'];

$sentencia = $Conexion->query("SELECT distinct (MaterialNo) from t_inventario ti WHERE MaterialNo  is not null  AND Almacen=$IdAlmacen ;");
$Query = $sentencia->fetchAll(PDO::FETCH_OBJ);

if(!$Query)
{
  $Query="No hay información.";
}

echo json_encode($Query, JSON_UNESCAPED_UNICODE);

?>