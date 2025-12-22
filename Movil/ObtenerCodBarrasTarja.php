<?php
header('Content-Type: application/json;  charset=UTF-8');

Include '../api/db/conexion.php';
$IdTarja = $_GET['IdTarja'];
$IdAlmacen = $_GET['IdAlmacen'];

$query = "SELECT CodBarras as CodBarras FROM T_ingreso WHERE IdTarja = $IdTarja and Almacen=$IdAlmacen";
$stmt = $Conexion->prepare($query);
$stmt->execute();

$codigosBarras = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!$codigosBarras)
{
  $codigosBarras="No hay información.";
}

echo json_encode($codigosBarras, JSON_UNESCAPED_UNICODE);

?>